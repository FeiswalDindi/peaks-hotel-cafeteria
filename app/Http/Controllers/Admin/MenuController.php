<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('category')->latest()->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.menus.create', compact('categories'));
    }

    public function edit($id)
{
    $menu = Menu::findOrFail($id);
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
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|max:2048', // Allow image to be optional on update
    ]);

    // 2. Prepare data to update (exclude image first)
    $data = $request->except('image');

    // 3. Handle Image Upload ONLY if a new file is chosen
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('menu_images', 'public');
        $data['image'] = $path; // Add the correct path to the data
    }

    // 4. Update the menu item
    $menu->update($data);

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
            'quantity' => 'required|integer|min:0', // NEW VALIDATION
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $menu = new Menu();
        $menu->name = $request->name;
        $menu->price = $request->price;
        $menu->quantity = $request->quantity; // SAVE THE QUANTITY
        $menu->category_id = $request->category_id;
        $menu->description = $request->description;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menu_images', 'public');
            $menu->image = $path;
        }

        $menu->save();

        return redirect()->route('admin.menus.index')->with('success', 'Menu Item Added!');
    }
}