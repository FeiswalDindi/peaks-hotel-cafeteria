<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    public function index()
    {
        // Get orders for the logged-in user, newest first
        $orders = Order::where('user_id', Auth::id())
                       ->with('items')
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        return view('orders.index', compact('orders'));
    }
}