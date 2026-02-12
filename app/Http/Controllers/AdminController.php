<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; // Ensure this is imported

class AdminController extends Controller
{
   public function index()
    {
        // 1. Get Totals
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_amount');
        
        // 2. Get Recent Orders with M-Pesa Codes
        // The 'with user' part requires the user() function in Order.php
        $recentOrders = Order::with('user')
                             ->orderBy('created_at', 'desc')
                             ->take(5)
                             ->get();

        return view('dashboard', compact('totalOrders', 'totalRevenue', 'recentOrders'));
    }

    public function downloadReport()
    {
        // Get all orders from Today where Wallet was used
        $todayOrders = \App\Models\Order::whereDate('created_at', now()->today())
                                ->where('wallet_paid', '>', 0)
                                ->with('user') // Get staff details
                                ->get();

        // Group by User to sum up their daily total (in case they ate twice)
        $reportData = $todayOrders->groupBy('user_id')->map(function ($orders) {
            $user = $orders->first()->user;
            return [
                'name' => $user->name,
                'staff_number' => $user->staff_number,
                'department' => $user->department,
                'total_spent' => $orders->sum('wallet_paid') // ONLY Wallet money
            ];
        });

        $totalClaim = $reportData->sum('total_spent');

        return view('admin.reports.daily', compact('reportData', 'totalClaim'));
    }
}