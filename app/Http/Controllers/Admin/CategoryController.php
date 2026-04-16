<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'image' => 'nullable|image|max:2048',
        ]);

        $category = new Category();
        $category->name = $request->name;

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Move file directly to public/uploads/categories
            $file->move(public_path('uploads/categories'), $filename);

            // Save the exact path to the database
            $category->image = 'uploads/categories/' . $filename;
        }

        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('image');

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Move file directly to public/uploads/categories
            $file->move(public_path('uploads/categories'), $filename);

            // Add the correct public path to the data
            $data['image'] = 'uploads/categories/' . $filename;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return redirect()->route('admin.categories.index')->with('danger', 'Category deleted!');
        } catch (\Illuminate\Database\QueryException $e) {
            // Foreign Key Constraint Violation
            if ($e->getCode() == "23000") {
                return redirect()->back()->with('error', 'Cannot delete this category because it has associated menu items. Please delete the menu items first or reassign them.');
            }
            return redirect()->back()->with('error', 'An error occurred while deleting.');
        }
    }
}