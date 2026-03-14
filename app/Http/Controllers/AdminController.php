<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; 
use App\Models\Feedback;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // 1. Get Totals
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_amount');
        $feedbackCount = Feedback::where('is_read', false)->count();
        
        // 2. Get Recent Orders
        $recentOrders = Order::with('user')
                             ->orderBy('created_at', 'desc')
                             ->take(5)
                             ->get();

        // 🌟 AJAX LIVE UPDATE LOGIC
        if ($request->ajax()) {
            // Format orders safely for Javascript
            $ordersData = $recentOrders->map(function($order) {
                return [
                    'id_raw' => $order->id,
                    'id' => str_pad($order->id, 5, '0', STR_PAD_LEFT),
                    'user' => $order->user->name ?? 'Guest',
                    'amount' => number_format($order->total_amount),
                    'mpesa_paid' => $order->mpesa_paid,
                    'mpesa_code' => $order->mpesa_code ?? '-',
                    'time' => $order->created_at->diffForHumans()
                ];
            });

            return response()->json([
                'totalOrders' => $totalOrders,
                'totalRevenue' => number_format($totalRevenue),
                'feedbackCount' => $feedbackCount,
                'recentOrders' => $ordersData
            ]);
        }

        // Normal Page Load
        return view('dashboard', compact('totalOrders', 'totalRevenue', 'recentOrders', 'feedbackCount'));
    }

    public function downloadReport()
    {
        $todayOrders = \App\Models\Order::whereDate('created_at', now()->today())
                                ->where('wallet_paid', '>', 0)
                                ->with('user') 
                                ->get();

        $reportData = $todayOrders->groupBy('user_id')->map(function ($orders) {
            $user = $orders->first()->user;
            return [
                'name' => $user->name,
                'staff_number' => $user->staff_number,
                'department' => $user->department,
                'total_spent' => $orders->sum('wallet_paid') 
            ];
        });

        $totalClaim = $reportData->sum('total_spent');

        return view('admin.reports.daily', compact('reportData', 'totalClaim'));
    }
}