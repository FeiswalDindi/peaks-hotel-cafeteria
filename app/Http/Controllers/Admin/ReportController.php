<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function dailyFinancial()
    {
        // 1. Get Today's Date
        $today = Carbon::today();

        // 2. Get Departments with their Staff
        // We also load the 'orders' for today to calculate spending
        $departments = Department::with(['staff' => function($query) use ($today) {
            $query->withSum(['orders' => function($q) use ($today) {
                $q->whereDate('created_at', $today)
                  ->where('status', 'paid'); // Only count paid orders
            }], 'total_amount');
        }])->orderBy('name')->get();

        return view('admin.reports.daily', compact('departments', 'today'));
    }
}