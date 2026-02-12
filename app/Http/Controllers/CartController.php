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
        $menu = Menu::findOrFail($id);
        $cart = session()->get('cart', []);

        // Logic: If item exists, add +1. If not, add it new.
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
        return redirect()->back()->with('success', 'Food added to tray!');
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