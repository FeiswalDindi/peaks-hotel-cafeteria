<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    // Screen 1: The "Folders" View (Lists Departments)
    public function index(Request $request)
    {
        $query = Department::withCount('staff');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $departments = $query->get(); // Get all departments

        return view('admin.staff.index', compact('departments'));
    }

    // Screen 2: The "Inside Folder" View (Lists Staff)
    public function show($id)
    {
        $department = Department::findOrFail($id);
        $staffMembers = $department->staff()->latest()->get();

        return view('admin.staff.show', compact('department', 'staffMembers'));
    }

    // Add this to StaffController.php
public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'department_id' => 'required|exists:departments,id',
            'daily_allocation' => 'required|numeric|min:0',
            'staff_number' => 'nullable|string|max:50',
        ]);

        // 1. Find the Department Name so we can save it to the old column too
        $dept = Department::findOrFail($request->department_id);

        // 2. Create the User with BOTH the ID and the Name
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            
            // The New Way (Links to the folder)
            'department_id' => $request->department_id, 
            
            // The Old Way (Shows the name in your database table)
            'department' => $dept->name, 

            'daily_allocation' => $request->daily_allocation,
            'staff_number' => $request->staff_number,
        ]);

        return back()->with('success', 'New staff member added successfully!');
    }


public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'department_id' => 'required|exists:departments,id',
            'daily_allocation' => 'required|numeric|min:0',
            'staff_number' => 'nullable|string|max:50',
        ]);

        // Find department name for the old column
        $dept = Department::findOrFail($request->department_id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
            'department' => $dept->name, // Keep both columns synced
            'daily_allocation' => $request->daily_allocation,
            'staff_number' => $request->staff_number,
        ]);

        return back()->with('success', 'Staff details updated!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // 1. Safety Check: Does this user have orders?
        if ($user->orders()->count() > 0) {
            return back()->with('error', 'Cannot delete this staff member because they have past orders. (Database Security)');
        }

        $user->delete();
        return back()->with('success', 'Staff member deleted successfully.');
    }

}