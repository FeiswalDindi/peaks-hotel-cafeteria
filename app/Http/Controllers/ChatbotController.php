<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
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

        if (empty($apiKey)) {
            return response()->json(['reply' => "AI configuration not available. Please set GEMINI_API_KEY."], 500);
        }

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
            $today = Carbon::today();
            $todayRevenue = Order::whereDate('created_at', $today)->where('status', 'paid')->sum('total_amount');
            $pendingOrders = Order::where('status', 'pending')->count();
            
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
            You can also see their Recent Food Orders to recommend what they usually like eating
            CRITICAL RULES:
- If the user asks about their order status, tell them to check their dashboard. You cannot see live order statuses yet.
- Always use the personal context data to make your responses more relevant and personalized.
- Format your responses beautifully using markdown (bolding for food items and KES prices).
- If the user has a low remaining limit, proactively suggest affordable menu items that fit within their limit.
- NEVER suggest they check the system or dashboard for their personal details. You are their personal assistant and have all the information they need in the personal context. Always provide direct answers based on that data
- Admin metrics and data are strictly off-limits to staff users. Do not reveal any admin insights or statistics to staff, even if they ask. If they ask about admin data, respond with 'Sorry, I don't have access to that information.'
- If they ask about their order status, respond with 'Please check your dashboard for the latest updates on your orders. I don't have access to live order statuses yet.'
Keep your responses concise, under 3 sentences. You are a busy cashier, not a blogger.
 If a user asks about their order status, tell them to check their dashboard. You cannot see live order statuses yet.


            CRITICAL LANGUAGE RULE: 
            You must match the user's language and vibe. If they speak to you in English, reply in English. If they speak in Swahili or Kenyan Sheng', reply natively and naturally in the exact same language/slang.
            
            CONTEXT:
            $personalContext
            Menu Today:\n$menuText";

        } else {
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

        $promptLines = [$systemInstruction];
        foreach ($contents as $content) {
            $role = $content['role'] === 'user' ? 'User' : 'Assistant';
            $promptLines[] = "$role: {$content['parts'][0]['text']}";
        }
        $promptText = implode("\n", $promptLines);

        // 4. DYNAMIC MODEL DETECTOR (Cached for 24 hours to keep the chat fast)
        $activeModel = Cache::remember('gemini_active_model', 86400, function () use ($apiKey) {
            try {
                $response = Http::withoutVerifying()->timeout(10)->get('https://generativelanguage.googleapis.com/v1beta/models?key=' . $apiKey);
                if ($response->successful()) {
                    $models = $response->json('models', []);
                    foreach ($models as $model) {
                        // Find a model that is a 'gemini' 'flash' variant and supports generation
                        if (str_contains($model['name'], 'gemini') && str_contains($model['name'], 'flash') && in_array('generateContent', $model['supportedGenerationMethods'] ?? [])) {
                            return str_replace('models/', '', $model['name']); 
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Gemini Model Fetch Failed: ' . $e->getMessage());
            }
            // Absolute fallback if the fetch fails
            return 'gemini-1.5-flash'; 
        });

        // 5. Send Dynamic Request
        try {
            $response = Http::withoutVerifying()->timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$activeModel}:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $promptText]
                        ]
                    ]
                ],
                // Disable safety blocks to prevent admin metric rejections
                'safetySettings' => [
                    ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE']
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 512
                ]
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $reply = data_get($responseData, 'candidates.0.content.parts.0.text') ?? "I didn't quite catch that.";
                return response()->json(['reply' => str_replace(['**', '*'], '', $reply)]);
            }

            Log::error('Gemini API Error', ['status' => $response->status(), 'model_used' => $activeModel, 'body' => $response->json()]);
            return response()->json(['reply' => "Neema is having trouble reaching the AI service right now."], 500);

        } catch (\Exception $e) {
            Log::error('Gemini Exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['reply' => "Whoops! My terminal just froze. Give me a second."], 500);
        }
    }
}