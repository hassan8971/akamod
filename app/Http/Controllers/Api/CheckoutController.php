<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PackagingOption;
use App\Models\Discount;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    // Shipping Costs
    private $shippingOptions = [
        'pishaz' => 35000,
        'tipax' => 60000,
    ];

    /**
     * Store a new order.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            // Address info
            'address.full_name' => 'required|string|max:255',
            'address.address' => 'required|string|max:255',
            'address.city' => 'required|string|max:255',
            'address.state' => 'required|string|max:255',
            'address.zip_code' => 'required|string|max:20',
            'address.phone' => 'required|string|max:20',
            
            // Order options
            'shipping_method' => 'required|string|in:pishaz,tipax',
            'packaging_id' => 'required|integer|min:0',
            'discount_code' => 'nullable|string',
            'payment_method' => 'required|string|in:online,cod,card',
            'transaction_code' => 'nullable|string|required_if:payment_method,card|max:255',

            // Cart items
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|integer|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'transaction_code.required_if' => 'لطفاً کد تراکنش کارت به کارت را وارد کنید.',
            'items.required' => 'سبد خرید شما نمی‌تواند خالی باشد.',
        ]);

        // Validate packaging option
        if ($validated['packaging_id'] != 0) {
            $request->validate(['packaging_id' => 'exists:packaging_options,id']);
        }

        DB::beginTransaction();
        try {
            // Calculate Prices
            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($validated['items'] as $item) {
                $variant = ProductVariant::find($item['variant_id']);
                
                // Check Stock Availability
                if ($variant->stock < $item['quantity']) {
                    return response()->json(['message' => 'موجودی محصول ' . $variant->name . ' کافی نیست.'], 422);
                }

                // Use sale-price (discount price) if there is any
                $price = $variant->discount_price ?? $variant->price;
                
                $subtotal += $price * $item['quantity'];

                // Making items ready to create the order
                $itemsToCreate[] = [
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'quantity' => $item['quantity'],
                    'price' => $price, // قیمت واحد در زمان خرید
                ];
                
                // Reduce Stock Balance
                $variant->decrement('stock', $item['quantity']);
            }

            // Calculate sidelong prices
            $shippingCost = $this->shippingOptions[$validated['shipping_method']] ?? 0;
            
            $packagingCost = 0;
            $packagingOptionId = null;
            if ($validated['packaging_id'] != 0) {
                $packagingOption = PackagingOption::find($validated['packaging_id']);
                if ($packagingOption) {
                    $packagingCost = $packagingOption->price;
                    $packagingOptionId = $packagingOption->id;
                }
            }

            // Calculate the discount
            $discountAmount = 0;
            $discountCode = null;
            if (!empty($validated['discount_code'])) {
                $discount = Discount::where('code', $validated['discount_code'])->first();
                if ($discount && $discount->min_purchase <= $subtotal) { 
                    $discountCode = $discount->code;
                    if ($discount->type == 'percent') {
                        $discountAmount = ($subtotal * $discount->value) / 100;
                    } else {
                        $discountAmount = $discount->value;
                    }
                    $discount->increment('times_used'); // Increase times the discount code is used
                }
            }
            
            $total = ($subtotal + $shippingCost + $packagingCost) - $discountAmount;
            
            // Save address and order
            $addressData = $validated['address'];
            $addressData['user_id'] = $user->id;
            $shippingAddress = Address::create($addressData);
            
            $shippingMethodName = $validated['shipping_method'] === 'pishaz' ? 'پست پیشتاز' : 'تیپاکس';
            
            $payment_status = 'pending';
            $order_status = 'pending';

            if ($validated['payment_method'] == 'online') {
                $payment_status = 'confirmed';
            }

            if ($validated['payment_method'] == 'cod') {
                $payment_status = 'pending';
            }

            $order = Order::create([
                'user_id' => $user->id,
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $shippingAddress->id,
                'status' => $order_status,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'shipping_method' => $shippingMethodName,
                'packaging_option_id' => $packagingOptionId,
                'packaging_cost' => $packagingCost,
                'discount_code' => $discountCode,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $payment_status,
                'transaction_code' => $validated['transaction_code'] ?? null,
            ]);

            // Save order code
            $orderCode = date('Ym') . '-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
            $order->update(['order_code' => $orderCode]);
            
            // Save order items
            $order->items()->createMany($itemsToCreate);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'سفارش شما با موفقیت ثبت شد!',
                'order_code' => $orderCode,
                'redirect_url' => route('checkout.success', $order)
            ], 201); // 201 Created

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'خطایی در ثبت سفارش رخ داد: ' . $e->getMessage()], 500);
        }
    }
}
