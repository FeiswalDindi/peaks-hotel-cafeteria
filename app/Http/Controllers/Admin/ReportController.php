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
 public function dailyFinancial(\Illuminate\Http\Request $request)
    {
        // 1. Get the requested date, or default to Today
        $dateString = $request->input('date', now()->toDateString());
        $selectedDate = \Carbon\Carbon::parse($dateString);

        // 2. Fetch Departments & Staff, but ONLY sum up the 'wallet_paid' for the selected date!
        $departments = \App\Models\Department::with(['staff' => function($query) use ($selectedDate) {
            $query->withSum(['orders' => function($q) use ($selectedDate) {
                // Only count orders from the chosen date where wallet was used
                $q->whereDate('created_at', $selectedDate)
                  ->where('status', '!=', 'cancelled'); // Don't count cancelled orders!
            }], 'wallet_paid'); 
        }])->get();

        return view('admin.reports.daily', [
            'departments' => $departments,
            'today' => $selectedDate // Pass the chosen date to the view
        ]);
    }
}