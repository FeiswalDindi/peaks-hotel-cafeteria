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
}