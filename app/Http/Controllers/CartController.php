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

        // 1. Add item to cart
        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $menu->name,
                "quantity" => 1,
                "price" => $menu->price,
                "image" => $menu->image
            ];
        }
        session()->put('cart', $cart);

        // 2. Calculate new total
        $total = 0;
        foreach($cart as $details) {
            $total += $details['price'] * $details['quantity'];
        }

        // 3. Check for Low Balance (Staff Only)
        $exceedsAllowance = false;
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->hasRole('staff')) {
            if ($total > \Illuminate\Support\Facades\Auth::user()->daily_allocation) {
                $exceedsAllowance = true;
            }
        }

        // 4. Send response back to the live screen
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'cart_count' => count($cart),
                'item_quantity' => $cart[$id]['quantity'],
                'exceeds_allowance' => $exceedsAllowance // 🌟 NEW: Send the warning flag to the frontend
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

    // 4. Update Cart Quantity
    public function update(Request $request)
    {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Cart updated successfully');
        }
    }
}