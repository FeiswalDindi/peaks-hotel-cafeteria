<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class CartController extends Controller
{
    // 1. Show the Cart Page
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;

        foreach($cart as $id => $details) {
            $total += $details['price'] * $details['quantity'];
        }

        return view('cart', compact('cart', 'total'));
    }

    // 2. Add Item to Cart
public function addToCart($id)
{
    $menu = \App\Models\Menu::findOrFail($id);
    $cart = session()->get('cart', []);

    // Check if item is already in cart
    if(isset($cart[$id])) {
        $cart[$id]['quantity']++;
    } else {
        // THIS WAS THE MISSING PART: We need to save the 'image' here
        $cart[$id] = [
            "name" => $menu->name,
            "quantity" => 1,
            "price" => $menu->price,
            "image" => $menu->image // ✅ Fixing the crash
        ];
    }

    session()->put('cart', $cart);

    // If the request comes from JavaScript (AJAX), return JSON
    if (request()->ajax()) {
        return response()->json([
            'success' => true,
            'cart_count' => count($cart),       // Total items in cart
            'item_quantity' => $cart[$id]['quantity'] // Specific count for this item (for the animation)
        ]);
    }

    return redirect()->back()->with('success', 'Product added to cart successfully!');
}
    // 3. Remove Item
    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Item removed successfully');
        }
    }
}