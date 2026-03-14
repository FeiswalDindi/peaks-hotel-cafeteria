<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class OrderManagementController extends Controller
{
    // 1. GENERAL ORDERS (M-PESA / HYBRID ONLY)
    public function index(Request $request)
    {
        $orders = Order::with('user')
            ->where('mpesa_paid', '>', 0)
            ->latest()
            ->paginate(10);

        $totalMpesaToday = Order::whereDate('created_at', Carbon::today())->sum('mpesa_paid');
        
        $hotItem = DB::table('order_items')
            ->select('menu_name', DB::raw('count(*) as total'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereDate('orders.created_at', Carbon::today())
            ->groupBy('menu_name')
            ->orderByDesc('total')
            ->first();

        return view('admin.orders.index', [
            'activeTab' => 'general',
            'orders' => $orders,
            'totalMpesaToday' => $totalMpesaToday ?? 0,
            'hotItem' => $hotItem
        ]);
    }

    // 🌟 HELPER: Generate the Unified Master Ledger Collection
    private function getUnifiedLedger($date)
    {
        // A. Get standard wallet transfers/overrides
        $walletTxs = WalletTransaction::with(['sender', 'receiver'])
            ->whereDate('created_at', $date)
            ->get()
            ->map(function ($tx) {
                return (object) [
                    'id' => 'TX-' . $tx->id,
                    'created_at' => $tx->created_at,
                    'type' => $tx->type,
                    'sender_name' => $tx->sender ? $tx->sender->name : 'System',
                    'receiver_name' => $tx->receiver ? $tx->receiver->name : 'Unknown',
                    'amount' => $tx->amount,
                    'description' => $tx->description ?? 'Wallet Transfer',
                    'is_order' => false,
                    'order_id' => null
                ];
            });

        // B. Get Food Orders paid via Wallet
        $orderTxs = Order::with('user')
            ->whereDate('created_at', $date)
            ->where('wallet_paid', '>', 0)
            ->get()
            ->map(function ($order) {
                return (object) [
                    'id' => 'ORD-' . $order->id,
                    'created_at' => $order->created_at,
                    'type' => 'food_purchase',
                    'sender_name' => $order->user ? $order->user->name : 'Guest/Unknown',
                    'receiver_name' => 'Cafeteria',
                    'amount' => $order->wallet_paid,
                    'description' => 'Cafeteria Meal Purchase',
                    'is_order' => true,
                    'order_id' => $order->id
                ];
            });

        // 🌟 THE FIX: Changed merge() to concat() to safely glue plain objects together!
        return $walletTxs->concat($orderTxs)->sortByDesc('created_at');
    }

    // 2. STAFF WALLET LEDGER (UNIFIED)
    public function ledger(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());

        // Fetch unified timeline
        $allTransactions = $this->getUnifiedLedger($date);
        $totalTransferred = $allTransactions->sum('amount');

        // Manually Paginate the Unified Collection
        $perPage = 15;
        $page = Paginator::resolveCurrentPage('tx_page') ?: 1;
        $transactions = new LengthAwarePaginator(
            $allTransactions->forPage($page, $perPage),
            $allTransactions->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'pageName' => 'tx_page']
        );

        return view('admin.orders.index', [
            'activeTab' => 'ledger',
            'transactions' => $transactions,
            'date' => $date,
            'totalTransferred' => $totalTransferred
        ]);
    }

    // 3. EXPORT LEDGER (UNIFIED PDF)
    public function exportLedger(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $action = $request->get('action', 'print'); 

        // Get everything without pagination for the print view
        $transactions = $this->getUnifiedLedger($date);
        $totalTransferred = $transactions->sum('amount');

        return view('admin.orders.ledger_pdf', [
            'transactions' => $transactions,
            'date' => $date,
            'totalTransferred' => $totalTransferred,
            'action' => $action
        ]);
    }

    // 4. UPDATE STATUS
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,paid,completed,cancelled'
        ]);

        $order->update(['status' => $request->status]);
        
        return back()->with('success', "Order #{$id} status updated to " . ucfirst($request->status));
    }
}