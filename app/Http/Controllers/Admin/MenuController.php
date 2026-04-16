<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with('categories');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Search by menu item name
                $q->where('name', 'like', '%' . $search . '%')
                  // Search by category name
                  ->orWhereHas('categories', function($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $menus = $query->latest()->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.menus.create', compact('categories'));
    }

    public function edit($id)
    {
        $menu = Menu::with('categories')->findOrFail($id);
        $categories = Category::all();
        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        // 1. Validate the inputs
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|image|max:2048', // Allow image to be optional on update
        ]);

        // 2. Prepare data to update (exclude image and categories first)
        $data = $request->except(['image', 'categories']);

        // 3. Handle Image Upload directly to public folder (InfinityFree Fix)
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Move file directly to public/uploads/menus
            $file->move(public_path('uploads/menus'), $filename);

            // Add the correct public path to the data
            $data['image'] = 'uploads/menus/' . $filename;
        }

        // 4. Update the menu item
        $menu->update($data);

        // 5. Sync categories (this will add new ones and remove old ones)
        $menu->categories()->sync($request->categories);

        return redirect()->route('admin.menus.index')->with('success', 'Menu updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $menu = Menu::findOrFail($id);
            $menu->delete();
            return redirect()->route('admin.menus.index')->with('danger', 'Item deleted!');
        } catch (\Illuminate\Database\QueryException $e) {
            // Error 23000 means "Foreign Key Constraint Violation" (It's being used elsewhere)
            if ($e->getCode() == "23000") {
                return redirect()->back()->with('error', 'Cannot delete this item because it is part of past orders. Please simply set its Stock to 0 or delete the orders first.');
            }
            return redirect()->back()->with('error', 'An error occurred while deleting.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:0',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $menu = new Menu();
        $menu->name = $request->name;
        $menu->price = $request->price;
        $menu->quantity = $request->quantity;
        $menu->description = $request->description;

        // Handle Image Upload directly to public folder (InfinityFree Fix)
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Move file directly to public/uploads/menus
            $file->move(public_path('uploads/menus'), $filename);

            // Save the exact path to the database
            $menu->image = 'uploads/menus/' . $filename;
        }

        $menu->save();

        // Attach categories to the menu
        $menu->categories()->attach($request->categories);

        return redirect()->route('admin.menus.index')->with('success', 'Menu Item Added!');
    }
}