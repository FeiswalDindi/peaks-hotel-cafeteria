<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Category; // ✅ Import this

class PublicMenuController extends Controller
{
    public function index()
    {
        // Homepage: Show 4 items that have stock
        $featuredItems = Menu::where('quantity', '>', 0)
                             ->with('category') // Load the category name
                             ->orderBy('created_at', 'desc')
                             ->take(4) 
                             ->get();
                             
        return view('welcome', compact('featuredItems'));
    }

    public function all(Request $request)
    {
        // 1. Start query for Available items with their Category
        $query = Menu::where('quantity', '>', 0)->with('category');

        // 2. Search Logic
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 3. Category Filter Logic (Using the Relationship)
        if ($request->has('category') && $request->category != 'All') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        // 4. Get Items (Sorted by Name since we can't sort by category string easily)
        $menuItems = $query->orderBy('name')->get();
        
        // 5. Get Category Names for the Buttons
        // We fetch distinct names from the Categories table, not the Menu table
        $categories = Category::orderBy('name')->pluck('name');

        return view('menu.index', compact('menuItems', 'categories'));
    }
}