<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $staffMembers = User::whereNotNull('staff_number')->get();
        return view('admin.staff.index', compact('staffMembers'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'staff_number' => 'required|string|unique:users',
            'department' => 'required|string',
            'daily_allocation' => 'required|numeric|min:0',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'staff_number' => $request->staff_number,
            'department' => $request->department,
            'daily_allocation' => $request->daily_allocation,
            'wallet_balance' => 0,
            'password' => Hash::make('password'), // Default password
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff Member Created Successfully.');
    }

    // ✅ This allows the Edit Page to load
    public function edit($id)
    {
        $staff = User::findOrFail($id);
        return view('admin.staff.edit', compact('staff'));
    }

    // ✅ This saves the changes
    public function update(Request $request, $id)
    {
        $staff = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$staff->id,
            'staff_number' => 'required|unique:users,staff_number,'.$staff->id,
            'department' => 'required|string',
            'daily_allocation' => 'required|numeric|min:0',
            'password' => 'nullable|string|min:6',
        ]);

        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->staff_number = $request->staff_number;
        $staff->department = $request->department;
        $staff->daily_allocation = $request->daily_allocation;

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
        }

        $staff->save();

        return redirect()->route('admin.staff.index')->with('success', 'Staff Details Updated Successfully!');
    }

    public function destroy($id)
    {
        $staff = User::findOrFail($id);
        $staff->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Staff Member Deleted.');
    }
}