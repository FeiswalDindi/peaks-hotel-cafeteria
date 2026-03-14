<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User; 
use App\Models\WalletTransaction; // 🌟 NEW: Import the Ledger Model

class OrderHistoryController extends Controller
{
    public function index()
    {
        // 1. Get the currently logged-in user
        $user = Auth::user();

        // 2. Fetch their food orders
        $orders = Order::with('items')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'orders_page'); // 🌟 Separated pagination names

        // 3. Fetch colleagues for the transfer modal
        $colleagues = User::where('id', '!=', $user->id)->get();

        // 🌟 4. NEW: Fetch their Wallet Transfers (Sent AND Received)
        $transactions = WalletTransaction::with(['sender', 'receiver'])
            ->where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'tx_page');

        // 5. Send everything to the view
        return view('orders.index', compact('orders', 'user', 'colleagues', 'transactions'));
    }
}