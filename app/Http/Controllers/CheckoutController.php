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

        // Calculate Wallet Usage for Staff
        $walletUsed = 0;
        if (Auth::check() && Auth::user()->hasRole('staff')) {
            $walletUsed = min($total, Auth::user()->daily_allocation);
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
            
            if ($walletUsed > 0) {
                Auth::user()->decrement('daily_allocation', $walletUsed);
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

        // Log it so you can see what Safaricom sent in your storage/logs/laravel.log
        \Illuminate\Support\Facades\Log::info('M-Pesa Callback Received: ', (array)$data);

        // 2. Make sure it's a valid STK response
        if (!isset($data->Body->stkCallback)) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $callbackData = $data->Body->stkCallback;
        $resultCode = $callbackData->ResultCode; // 0 means Success. Anything else is an error/cancel.
        $checkoutRequestID = $callbackData->CheckoutRequestID; // The ws_CO_... tracking code

        // 3. Find the order waiting for this specific payment
        $order = \App\Models\Order::where('mpesa_code', $checkoutRequestID)->first();

        if ($order) {
            if ($resultCode == 0) {
                // ✅ PAYMENT SUCCESSFUL
                $mpesaReceiptNumber = '';
                
                // Safaricom sends an array of data, we need to loop through to find the Receipt Number
                if (isset($callbackData->CallbackMetadata->Item)) {
                    foreach ($callbackData->CallbackMetadata->Item as $item) {
                        if ($item->Name == 'MpesaReceiptNumber') {
                            $mpesaReceiptNumber = $item->Value;
                            break;
                        }
                    }
                }

                // Update the database with real success data
                $order->update([
                    'status' => 'paid',
                    'mpesa_code' => $mpesaReceiptNumber // Replace ws_CO with real code
                ]);

            } else {
                // ❌ PAYMENT CANCELLED OR FAILED (e.g., Wrong PIN, Insufficient funds)
                $order->update([
                    'status' => 'failed'
                ]);
            }
        }

        // Safaricom requires a response so they know we received the message
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    // --- STEP 2 ADDITIONS ---

    // 1. Live Poller: Checks if the database status has changed
    public function checkStatus($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        return response()->json([
            'status' => $order->status,
            'mpesa_code' => $order->mpesa_code
        ]);
    }

    // 2. Cancel Order: Allows the user to cancel while pending
public function cancelOrder(Request $request, $id)
    {
        $order = \App\Models\Order::with('items')->findOrFail($id);
        
        if ($order->status === 'pending') {
            $order->update(['status' => 'cancelled']); 
            
            // Refund wallet allocation if they used any
            if ($order->wallet_paid > 0 && Auth::check()) {
                Auth::user()->increment('daily_allocation', $order->wallet_paid);
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