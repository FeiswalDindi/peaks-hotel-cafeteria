<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderManagementController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('user')->latest()->paginate(15);

        // 🌟 Ensure we calculate revenue for the current Nairobi date
        $totalToday = Order::whereDate('created_at', Carbon::today())->sum('total_amount');
        
        $hotItem = \DB::table('order_items')
            ->select('menu_name', \DB::raw('count(*) as total'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereDate('orders.created_at', Carbon::today())
            ->groupBy('menu_name')
            ->orderByDesc('total')
            ->first();

        return view('admin.orders.index', [
            'orders' => $orders,
            'totalToday' => $totalToday ?? 0,
            'hotItem' => $hotItem
        ]);
    }

    // 🌟 NEW: Industrial status management logic
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);
        
        return back()->with('success', "Order #{$id} status updated to " . ucfirst($request->status));
    }
}