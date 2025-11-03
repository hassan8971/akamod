<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\DB; // For database transactions

class CheckoutController extends Controller
{
    // Show the checkout page
    public function index()
    {
        if (Cart::isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        // Get user's saved addresses if they are logged in
        $user = Auth::user();
        $addresses = $user ? $user->addresses : collect();

        $cartItems = Cart::getContent();
        $total = Cart::getTotal();

        return view('checkout.index', compact('cartItems', 'total', 'addresses'));
    }

    // Process the order
    public function store(Request $request)
    {
        if (Cart::isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'save_address' => 'nullable|boolean',
            'payment_method' => 'required|string', // e.g., 'stripe'
        ]);

        // Use a database transaction to ensure all data is saved, or none is.
        DB::beginTransaction();

        try {
            // 1. Create the Shipping Address
            // We create a new address for every order for historical accuracy
            $shippingAddress = Address::create([
                'user_id' => Auth::id(), // Will be null for guests
                'full_name' => $request->full_name,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'country' => $request->country,
                'phone' => $request->phone,
            ]);

            // If user is logged in and checked "Save Address", save it to their profile
            if (Auth::check() && $request->save_address) {
                // (You might want to add logic here to prevent duplicates)
                Auth::user()->addresses()->create($shippingAddress->toArray());
            }

            // 2. Create the Order
            $subtotal = Cart::getSubTotal(false); // Store in cents
            $total = Cart::getTotal(); // Store in cents
            // (In a real app, you'd calculate shipping/taxes here)

            $order = Order::create([
                'user_id' => Auth::id(),
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $shippingAddress->id, // Assuming same for now
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => 0, // TODO: Implement shipping
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
            ]);

            // 3. Create Order Items
            foreach (Cart::getContent() as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->attributes->variant_id,
                    'product_name' => $item->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price, // Store in cents
                ]);
                
                // 4. (Optional but recommended) Decrement stock
                // $variant = ProductVariant::find($item->attributes->variant_id);
                // $variant->decrement('stock', $item->quantity);
            }

            // 5. SIMULATE PAYMENT
            // In a real app, you'd redirect to Stripe here.
            // For now, we'll just "confirm" the payment.
            if ($request->payment_method == 'cod') { // Cash on Delivery example
                $order->update(['payment_status' => 'paid', 'status' => 'processing']);
            }
            
            // 6. Clear the cart
            Cart::clear();

            // 7. Commit the transaction
            DB::commit();

            // Redirect to a "Thank You" page
            return redirect()->route('checkout.success', $order)
                ->with('success', 'Your order has been placed!');

        } catch (\Exception $e) {
            // If anything went wrong, roll back the database changes
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong. Please try again. ' . $e->getMessage())->withInput();
        }
    }

    // Show a "Thank You" page
    public function success(Order $order)
    {
        // You'll need to create this view
        return view('checkout.success', compact('order'));
    }
}
