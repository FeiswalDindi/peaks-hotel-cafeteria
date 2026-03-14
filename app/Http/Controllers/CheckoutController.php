<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\MpesaService;

class CheckoutController extends Controller
{
    /**
     * ✅ RESTORED: This method loads the checkout page
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return view('checkout', compact('cart', 'total'));
    }

    /**
     * ✅ FIXED: Handles payment processing with strict phone formatting
     */
    public function process(Request $request, MpesaService $mpesaService)
{
        $cart = session()->get('cart', []);
        if(empty($cart)) return redirect()->route('home');

        $total = 0;
        foreach($cart as $item) { $total += $item['price'] * $item['quantity']; }

        // 🌟 THE FIX: Removed strict 'staff' role check. If they are logged in, use their wallet!
        $walletUsed = 0;
        if (Auth::check()) {
            $walletUsed = min($total, Auth::user()->wallet_balance); 
        }
        $mpesaAmount = $total - $walletUsed;

        $finalPhone = null;
        if ($mpesaAmount > 0) {
            $request->validate(['phone' => 'required|numeric|digits:9']);
            
            // Clean formatting: Strip leading zeros to ensure 2547... format
            $finalPhone = '254' . ltrim($request->phone, '0');
        }

        $checkoutRequestId = null;
        $status = 'paid'; 
        
        if ($mpesaAmount > 0) {
            $status = 'pending'; 
            $response = $mpesaService->stkPush($finalPhone, $mpesaAmount, "Order");
            
            if (!$response['success']) {
                return back()->with('error', 'M-Pesa Error: ' . $response['message']);
            }
            
            $checkoutRequestId = $response['data']['CheckoutRequestID'] ?? null;
        }

        $order = null;
        DB::transaction(function () use ($cart, $total, $walletUsed, $mpesaAmount, $checkoutRequestId, $status, $finalPhone, &$order) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'wallet_paid' => $walletUsed,
                'mpesa_paid' => $mpesaAmount,
                'phone_number' => $finalPhone,
                'mpesa_code' => $checkoutRequestId, 
                'status' => $status,
            ]);

            foreach($cart as $id => $details) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $id,
                    'menu_name' => $details['name'],
                    'quantity' => $details['quantity'],
                    'price' => $details['price']
                ]);
                $menu = Menu::find($id);
                if($menu) $menu->decrement('quantity', $details['quantity']);
            }
            
            // 🌟 THE FIX #2: Deduct from spendable balance AND increase the daily usage tally
            if ($walletUsed > 0) {
                $user = Auth::user();
                $user->decrement('wallet_balance', $walletUsed);
                $user->increment('allocation_used_today', $walletUsed);
            }
        });

        session()->forget('cart');
        
        $msg = ($mpesaAmount > 0) ? 'Order Placed! Please check your phone for the PIN.' : 'Payment Successful via Wallet!';
        return redirect()->route('receipt.show', $order->id)->with('success', $msg);
    }

 public function mpesaCallback(\Illuminate\Http\Request $request)
    {
        // 1. Get the data sent by Safaricom
        $data = json_decode($request->getContent());
        \Illuminate\Support\Facades\Log::info('M-Pesa Callback Received: ', (array)$data);

        if (!isset($data->Body->stkCallback)) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $callbackData = $data->Body->stkCallback;
        $resultCode = $callbackData->ResultCode; // 0 means Success.
        $checkoutRequestID = $callbackData->CheckoutRequestID; // The ws_CO_... tracking code

        // 2. Find the pending order (Load items so we can restore stock if it fails)
        $order = \App\Models\Order::with('items')->where('mpesa_code', $checkoutRequestID)->first();

        // 3. Only process if the order exists and is still pending
        if ($order && $order->status === 'pending') {
           if ($resultCode == 0) {
                // ✅ PAYMENT SUCCESSFUL
                $order->update(['status' => 'paid']);
                
                // 🌟 THE NEW TRIGGER: Shout to Pusher that it's paid!
                event(new \App\Events\PaymentReceived($order->id, 'paid'));
                
                // Note: We deliberately leave the mpesa_code as 'ws_CO...' so we don't break tracking.
            } else {
                // ❌ PAYMENT CANCELLED OR FAILED
                $order->update(['status' => 'cancelled']);

                // 🌟 THE FIX: Refund the wallet safely
                if ($order->wallet_paid > 0 && \App\Models\User::find($order->user_id)) {
                    $user = \App\Models\User::find($order->user_id);
                    $user->increment('wallet_balance', $order->wallet_paid);
                    
                    if($user->allocation_used_today >= $order->wallet_paid) {
                        $user->decrement('allocation_used_today', $order->wallet_paid);
                    } else {
                        $user->update(['allocation_used_today' => 0]);
                    }
                }

                // 🌟 THE FIX: Restore the food stock
                foreach($order->items as $item) {
                    $menu = \App\Models\Menu::find($item->menu_id);
                    if ($menu) {
                        $menu->increment('quantity', $item->quantity);
                    }
                }
            }
        }

        // 4. Safaricom requires this exact response so they know we received it
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    // --- STEP 2 ADDITIONS ---

    // 1. Live Poller: Actively checks Safaricom for status changes
 // 1. Live Poller: Actively checks Safaricom for status changes (STRICT MODE)
    public function checkStatus($id, \App\Services\MpesaService $mpesaService)
    {
        $order = \App\Models\Order::with('items')->findOrFail($id);

        // If it's already processed, just return the status
        if ($order->status !== 'pending' || !$order->mpesa_code) {
            return response()->json(['status' => $order->status]);
        }

        $response = $mpesaService->queryStkStatus($order->mpesa_code);

        if ($response['success']) {
            // Check if ResultCode actually exists in the response
            if (isset($response['data']['ResultCode'])) {
                $resultCode = (string) $response['data']['ResultCode'];

                if ($resultCode === '0') {
                    // ✅ PAID SUCCESSFULLY
                    $order->update(['status' => 'paid']);
                } 
                // ❌ STRICT FAILURES: 1032 (Cancelled), 1 (Insufficient Funds), 1037 (Timeout), 2001 (Wrong PIN)
                elseif (in_array($resultCode, ['1032', '1', '1037', '2001', '1036'])) {
                    
                    $order->update(['status' => 'cancelled']);

                    // Refund the wallet safely
                    if ($order->wallet_paid > 0 && \App\Models\User::find($order->user_id)) {
                        $user = \App\Models\User::find($order->user_id);
                        $user->increment('wallet_balance', $order->wallet_paid);
                        
                        if($user->allocation_used_today >= $order->wallet_paid) {
                            $user->decrement('allocation_used_today', $order->wallet_paid);
                        } else {
                            $user->update(['allocation_used_today' => 0]);
                        }
                    }

                    // Restore the food stock
                    foreach($order->items as $item) {
                        $menu = \App\Models\Menu::find($item->menu_id);
                        if ($menu) {
                            $menu->increment('quantity', $item->quantity);
                        }
                    }
                }
                // If it's any other random code from the Sandbox, DO NOTHING and keep waiting!
            }
        }

        return response()->json(['status' => $order->status]);
    }

    // 2. Cancel Order: Allows the user to cancel while pending
    public function cancelOrder(Request $request, $id)
    {
        $order = \App\Models\Order::with('items')->findOrFail($id);
        
        if ($order->status === 'pending') {
            $order->update(['status' => 'cancelled']); 
            
            // 🌟 THE FIX #3: Refund funds to the spendable wallet column AND reduce tally
            if ($order->wallet_paid > 0 && Auth::check()) {
                $user = Auth::user();
                $user->increment('wallet_balance', $order->wallet_paid);
                
                if($user->allocation_used_today >= $order->wallet_paid) {
                    $user->decrement('allocation_used_today', $order->wallet_paid);
                } else {
                    $user->update(['allocation_used_today' => 0]);
                }
            }

            // 🌟 RESTORE CART ITEMS so they can try paying again
            $cart = session()->get('cart', []);
            foreach($order->items as $item) {
                $menu = \App\Models\Menu::find($item->menu_id);
                if ($menu) {
                    $menu->increment('quantity', $item->quantity); // Restore stock
                }
                $cart[$item->menu_id] = [
                    "name" => $item->menu_name,
                    "quantity" => $item->quantity,
                    "price" => $item->price,
                    "image" => $menu ? $menu->image : null
                ];
            }
            session()->put('cart', $cart);
            
            // Redirect back to Checkout instead of Homepage
            return redirect()->route('checkout.index')->with('success', 'Order cancelled. You can update your details and try again.');
        }

        return back()->with('error', 'Cannot cancel this order.');
    }
}