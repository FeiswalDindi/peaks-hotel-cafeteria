<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Services\MpesaService; // ✅ CORRECT: Outside the class

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

    // ✅ This checks Safaricom for the real status
    public function checkStatus($id, MpesaService $mpesaService)
    {
        $order = Order::findOrFail($id);

        if ($order->status == 'paid') {
            return redirect()->route('receipt.show', $id)->with('success', 'Payment Confirmed!');
        }

        if (!$order->mpesa_code) {
            return back()->with('error', 'No M-Pesa Request found for this order.');
        }

        $response = $mpesaService->queryStkStatus($order->mpesa_code);

        if ($response['success']) {
            $resultCode = $response['data']['ResultCode'] ?? '99';
            $resultDesc = $response['data']['ResultDesc'] ?? 'Unknown';

            if ($resultCode == '0') {
                $order->update(['status' => 'paid']);
                return redirect()->route('receipt.show', $id)->with('success', 'Payment Verified Successfully!');
            } 
            
            if ($resultCode == '1032') {
                return back()->with('error', 'Payment Cancelled by User.');
            }

            return back()->with('info', 'Status: ' . $resultDesc);
        }

        return back()->with('error', 'Could not verify payment: ' . $response['message']);
    }
}