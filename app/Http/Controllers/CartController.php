<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class CartController extends Controller
{
    /**
     * Display the cart index page.
     */
    public function index()
    {
        $cartItems = Cart::getContent();
        $cartTotal = Cart::getTotal();

        return view('cart.index', compact('cartItems', 'cartTotal'));
    }

    /**
     * Add an item to the cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::with('product')->findOrFail($request->variant_id);
        $product = $variant->product;

        // Check if there is enough stock
        if ($variant->stock < $request->quantity) {
            return redirect()->back()->with('error', 'Not enough stock for this item.');
        }

        Cart::add([
            'id' => $variant->id,
            'name' => $product->name,
            'price' => $variant->price,
            'quantity' => (int) $request->quantity,
            'attributes' => [
                'variant_name' => $variant->name,
                'image' => $product->images->first() ? $product->images->first()->path : null,
                'slug' => $product->slug,
            ],
            'associatedModel' => $variant
        ]);

        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
    }

    /**
     * Update an item's quantity in the cart.
     */
    public function update(Request $request, $cartId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = Cart::get($cartId);
        $variant = $item->associatedModel;

        // Check stock
        if ($variant->stock < $request->quantity) {
            return redirect()->back()->with('error', 'Not enough stock for this item.');
        }
        
        Cart::update($cartId, [
            'quantity' => [
                'relative' => false,
                'value' => (int) $request->quantity
            ],
        ]);

        return redirect()->route('cart.index')->with('success', 'Cart updated successfully.');
    }

    /**
     * Remove an item from the cart.
     */
    public function destroy($cartId)
    {
        Cart::remove($cartId);
        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    /**
     * Clear the entire cart.
     */
    public function clear()
    {
        Cart::clear();
        return redirect()->route('cart.index')->with('success', 'Cart cleared.');
    }
}
