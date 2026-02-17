<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array'
        ]);
        
        $apiKey = env('GEMINI_API_KEY');
        $user = Auth::user();

        // 1. Gather Live Menu & Popularity
        $availableMenu = Menu::where('quantity', '>', 0)->get();
        $menuText = $availableMenu->map(fn($item) => "- {$item->name}: KES {$item->price}")->implode("\n");
        if (empty($menuText)) $menuText = "The menu is currently empty.";

        $popularData = "No sales data yet.";
        if (Schema::hasTable('order_items')) {
            $topItems = DB::table('order_items')
                ->select('menu_name', DB::raw('SUM(quantity) as total_sold'))
                ->groupBy('menu_name')->orderByDesc('total_sold')->limit(3)->get();
            if ($topItems->isNotEmpty()) {
                $popularData = $topItems->map(fn($item) => "- {$item->menu_name} (Sold: {$item->total_sold})")->implode("\n");
            }
        }

        // 2. Role-Based Data Extraction
        $isAdmin = Auth::check() && $user->hasRole('admin');
        $isStaff = Auth::check() && $user->hasRole('staff');

        if ($isAdmin) {
            // 🌟 ADMIN/MANAGEMENT CONTEXT
            $today = Carbon::today();
            $todayRevenue = Order::whereDate('created_at', $today)->where('status', 'paid')->sum('total_amount');
            $pendingOrders = Order::where('status', 'pending')->count();
            
            // 🌟 THE 500 ERROR FIX: Safely counting staff without crashing the relationship
            $totalStaff = User::all()->filter(fn($u) => $u->hasRole('staff'))->count();
            
            $totalWalletLiability = User::sum('wallet_balance');
            $departments = DB::table('users')->whereNotNull('department')->distinct()->pluck('department')->implode(', ');

            $adminData = "
            LIVE MANAGEMENT METRICS:
            - Total Staff Count: {$totalStaff}
            - Active Departments: {$departments}
            - Total Unspent Wallet Funds: KES {$totalWalletLiability}
            - Today's Paid Revenue: KES {$todayRevenue}
            - Alerts: There are {$pendingOrders} orders currently stuck in pending status.
            ";

            $systemInstruction = "You are Neema, the sharp, highly intelligent Executive Management Assistant for KCA University's Peaks Hotel Cafeteria. 
            You are speaking to the System Administrator. Provide data insights and staff statistics directly from the METRICS provided.
            NEVER tell the Admin to 'check the system'. YOU are the system interface.
            
            CONTEXT:
            $adminData
            Menu Today:\n$menuText";

        } elseif ($isStaff) {
            // 🌟 THE ORDER HISTORY FIX: Giving Neema access to staff orders again
            $remainingLimit = $user->daily_allocation - $user->allocation_used_today;
            
            $recentOrders = Order::with('items')->where('user_id', $user->id)->orderBy('created_at', 'desc')->take(3)->get();
            $orderHistoryText = $recentOrders->isNotEmpty() 
                ? $recentOrders->map(fn($o) => "Order #{$o->id}: " . $o->items->pluck('menu_name')->implode(', '))->implode(" | ")
                : "No recent orders.";
            
            $personalContext = "
            STAFF ACCOUNT DETAILS:
            - Name: {$user->name}
            - Department: {$user->department}
            - Wallet Balance: KES {$user->wallet_balance}
            - Daily Limit Remaining: KES {$remainingLimit}
            - Recent Food Orders: {$orderHistoryText}
            ";

            $systemInstruction = "You are Neema, the intelligent Personal Concierge for KCA University Staff. 
            Your goal is to help the staff member manage their wallet and meals. 
            Remind them of their remaining daily limit if they ask for expensive items. 
            If their balance is low, suggest affordable items from the menu.
            You can also see their Recent Food Orders to recommend what they usually like eating.

            CRITICAL LANGUAGE RULE: 
            You must match the user's language and vibe. If they speak to you in English, reply in English. If they speak in Swahili or Kenyan Sheng', reply natively and naturally in the exact same language/slang.
            
            CONTEXT:
            $personalContext
            Menu Today:\n$menuText";

        } else {
            // GUEST CONTEXT
            $userContext = "User Status: Guest (Not logged in). Pay method: M-Pesa only.";
            $systemInstruction = "You are Neema, the friendly and fast lead cashier for Peaks Hotel Cafeteria. 
            Guide the guest through today's menu and recommend the best sellers.
            
            CONTEXT:
            Menu Today:\n$menuText
            Best Sellers:\n$popularData
            $userContext";
        }

        // 3. Process Conversation History
        $contents = [];
        foreach ($request->input('history', []) as $msg) {
            if (empty($msg['text'])) continue;
            $contents[] = [
                'role' => (isset($msg['role']) && $msg['role'] === 'user') ? 'user' : 'model',
                'parts' => [['text' => $msg['text']]]
            ];
        }
        if (empty($contents)) {
            $contents[] = ['role' => 'user', 'parts' => [['text' => $request->message]]];
        }

        // 4. Call Gemini 2.5 Flash
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey, [
                'systemInstruction' => ['parts' => [['text' => $systemInstruction]]],
                'contents' => $contents
            ]);

            if ($response->successful()) {
                $reply = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? "I didn't quite catch that.";
                return response()->json(['reply' => str_replace(['**', '*'], '', $reply)]);
            }
            return response()->json(['reply' => "Still under Development... just stay put, Coming soon!"], 500);

        } catch (\Exception $e) {
            return response()->json(['reply' => "Whoops! My terminal just froze. Give me a second."], 500);
        }
    }
}