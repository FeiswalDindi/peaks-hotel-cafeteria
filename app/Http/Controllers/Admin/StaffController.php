<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    // Screen 1: The "Folders" View (Lists Departments)
    public function index(Request $request)
    {
        $search = $request->get('search');

        // Get departments for the cards
        $departments = Department::withCount('users as staff_count')->get();

        // 🌟 Get the staff members if a search/department is selected
        $users = null;
        if ($search) {
            $users = User::whereHas('department', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('name', 'like', "%{$search}%")->get();
        }

        // 🌟 NEW: Fetch all staff for the Admin Override Modal dropdown
        $allStaff = User::all();

        return view('admin.staff.index', compact('departments', 'users', 'allStaff'));
    }

    // Screen 2: The "Inside Folder" View (Lists Staff)
    public function department($id)
    {
        // Fetch the department and all its associated staff
        $department = Department::with('users')->findOrFail($id);
        
        // 🌟 NEW: Fetch all staff here too, in case the modal is placed inside the folder view
        $allStaff = User::all();
        
        return view('admin.staff.department', compact('department', 'allStaff'));
    }

    // Screen 3: The Profile View (Staff Analytics)
    public function show($id)
    {
        $staff = User::with(['orders.items', 'department'])->findOrFail($id);

        // 🌟 TREND 1: Most Ordered Item
        $favoriteItem = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select('menu_name', DB::raw('count(*) as total'))
            ->where('orders.user_id', $id)
            ->groupBy('menu_name')
            ->orderByDesc('total')
            ->first();

        // 🌟 TREND 2: Lifetime Spending
        $totalSpent = $staff->orders()->sum('total_amount');
        
        // 🌟 TREND 3: Wallet vs M-Pesa Usage
        $walletTotal = $staff->orders()->sum('wallet_paid');
        $mpesaTotal = $staff->orders()->sum('mpesa_paid');

        return view('admin.staff.show', compact('staff', 'favoriteItem', 'totalSpent', 'walletTotal', 'mpesaTotal'));
    }

    // 🌟 Load the Create Form
    public function create()
    {
        $departments = Department::all(); // Needed for the dropdown
        return view('admin.staff.create', compact('departments'));
    }

    // 🌟 Save the New Staff Member
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            // 🌟 THE FIX: Added unique:users,staff_number
            'staff_number' => 'nullable|string|unique:users,staff_number', 
            'department_id' => 'nullable|exists:departments,id',
            'daily_allocation' => 'numeric|min:0',
            'wallet_balance' => 'numeric|min:0'
        ]);

        // Find the Department Name to keep your old database column synced
        $deptName = null;
        if ($request->department_id) {
            $dept = Department::find($request->department_id);
            $deptName = $dept ? $dept->name : null;
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'staff_number' => $request->staff_number,
            'department_id' => $request->department_id,
            'department' => $deptName, // Keeps both columns synced
            'daily_allocation' => $request->daily_allocation ?? 0,
            'wallet_balance' => $request->wallet_balance ?? 0,
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'New staff member added successfully!');
    }

    // 🌟 Load the Edit Form
    public function edit($id)
    {
        $staff = User::findOrFail($id);
        $departments = Department::all();
        
        return view('admin.staff.edit', compact('staff', 'departments'));
    }

    // 🌟 Save the Updates (Now with Password Reset Override)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'department_id' => 'nullable|exists:departments,id',
            'daily_allocation' => 'numeric|min:0',
            'wallet_balance' => 'numeric|min:0',
            'staff_number' => 'nullable|string|max:50|unique:users,staff_number,'.$user->id,
            'password' => 'nullable|string|min:8', // 🌟 NEW: Allow optional password update
        ]);

        // Find department name for the old column
        $deptName = null;
        if ($request->department_id) {
            $dept = Department::find($request->department_id);
            $deptName = $dept ? $dept->name : null;
        }

        // Prepare the data to update
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
            'department' => $deptName, 
            'daily_allocation' => $request->daily_allocation,
            'wallet_balance' => $request->wallet_balance ?? $user->wallet_balance,
            'staff_number' => $request->staff_number,
        ];

        // 🌟 NEW: If the Admin typed a password, hash it and add it to the update list!
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Redirect back to their department list with a success message
        return redirect()->route('admin.staff.department', $user->department_id ?? 1)
                         ->with('success', "{$user->name}'s profile (and password if provided) has been updated.");
    }

    // 🌟 Delete the Staff Member
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Safety Check: Prevent deletion if they have financial records!
        if ($user->orders()->count() > 0) {
            return back()->with('error', 'Cannot delete this staff member because they have past orders. (Database Security)');
        }

        $user->delete();
        return back()->with('success', 'Staff member deleted successfully.');
    }

    public function resetAllocations()
    {
        // This instantly forces a complete hard-reset for every user
        \App\Models\User::query()->update([
            'wallet_balance' => 200,       // <-- Forces current balance to 200
            'daily_allocation' => 200,     // <-- Sets their total allowance for the day to 200
            'allocation_used_today' => 0   // <-- Clears whatever they spent yesterday
        ]);

        // Remember the exact time you clicked the button
        \Illuminate\Support\Facades\Cache::put('last_wallet_reset', now(), now()->addDays(30));

        return redirect()->back()->with('success', 'Absolute Success! All wallets forcibly reset to KES 200, limits updated, and usage cleared.');
    }
}