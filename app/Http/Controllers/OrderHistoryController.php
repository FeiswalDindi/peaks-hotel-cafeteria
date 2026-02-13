<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class OrderHistoryController extends Controller
{
    public function index()
    {
        // 1. Get the currently logged-in user
        $user = Auth::user();

        // 2. Fetch their orders, newest first, including the items
        $orders = Order::with('items')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Show 10 orders per page

        // 3. Send it to the view
        return view('orders.index', compact('orders', 'user'));
    }
}