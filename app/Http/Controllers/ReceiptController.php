<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\MpesaService;

class ReceiptController extends Controller
{
    public function show($id)
    {
        $order = Order::with('items')->findOrFail($id);

        if (Auth::check()) {
            $user = Auth::user();
            if ($order->user_id !== $user->id && !$user->hasRole('admin')) {
                abort(403, 'Unauthorized access to this receipt.');
            }
        }

        return view('receipts.show', compact('order'));
    }

    public function checkStatus($id, MpesaService $mpesaService)
    {
        $order = Order::with('items')->findOrFail($id);

        if ($order->status !== 'pending' || !$order->mpesa_code) {
            return response()->json(['status' => $order->status]);
        }

        // 🌟 THE NEW FIX: Realistic M-Pesa Timeout
        // If the system waits for 35 seconds with no response from Safaricom,
        // it assumes the prompt failed/timed out and safely cancels the order.
        if ($order->created_at->diffInSeconds(now()) > 35) {
            $order->update(['status' => 'cancelled']);

            // Refund the wallet safely
            if ($order->wallet_paid > 0 && User::find($order->user_id)) {
                $user = User::find($order->user_id);
                $user->increment('wallet_balance', $order->wallet_paid);
                
                if($user->allocation_used_today >= $order->wallet_paid) {
                    $user->decrement('allocation_used_today', $order->wallet_paid);
                } else {
                    $user->update(['allocation_used_today' => 0]);
                }
            }

            // Restore the food stock
            foreach($order->items as $item) {
                $menu = Menu::find($item->menu_id);
                if ($menu) $menu->increment('quantity', $item->quantity);
            }

            // Send a creative message back to the UI
            session()->flash('timeout_message', 'Payment took too long to verify or failed. The system has automatically cancelled the order. Please try again!');
            return response()->json(['status' => 'cancelled']);
        }

        // Check actual Safaricom status
        $response = $mpesaService->queryStkStatus($order->mpesa_code);

        if ($response['success']) {
            if (isset($response['data']['ResultCode'])) {
                $resultCode = (string) $response['data']['ResultCode'];

                if ($resultCode === '0') {
                    // ✅ PAID SUCCESSFULLY
                    $order->update(['status' => 'paid']);
                } 
                elseif (in_array($resultCode, ['1032', '1', '1037', '2001', '1036'])) {
                    // ❌ FAILED OR CANCELLED
                    $order->update(['status' => 'cancelled']);

                    if ($order->wallet_paid > 0 && User::find($order->user_id)) {
                        $user = User::find($order->user_id);
                        $user->increment('wallet_balance', $order->wallet_paid);
                        
                        if($user->allocation_used_today >= $order->wallet_paid) {
                            $user->decrement('allocation_used_today', $order->wallet_paid);
                        } else {
                            $user->update(['allocation_used_today' => 0]);
                        }
                    }

                    foreach($order->items as $item) {
                        $menu = Menu::find($item->menu_id);
                        if ($menu) $menu->increment('quantity', $item->quantity);
                    }
                }
            }
        }

        return response()->json(['status' => $order->status]);
    }
}