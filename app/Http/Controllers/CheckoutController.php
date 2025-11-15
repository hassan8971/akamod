<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\DB; // For database transactions
use App\Models\PackagingOption;
use App\Models\Discount;
use Darryldecode\Cart\CartCondition;
use App\Models\ProductVariant;

class CheckoutController extends Controller
{
    // Define shipping costs here (in Toman)
    // We define it here AND in the view to ensure security
    private $shippingOptions = [
        'pishaz' => 35000,
        'tipax' => 60000,
    ];

    // Show the checkout page
    public function index()
    {
        if (Cart::isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'سبد خرید شما خالی است.');
        }

        // Get user's saved addresses if they are logged in
        $user = Auth::user();
        $addresses = $user ? $user->addresses : collect();

        $cartItems = Cart::getContent();
        
        // --- FIX: Pass 'subtotal' to the view for Alpine.js ---
        $subtotal = Cart::getSubTotal(); // Get subtotal as Toman integer

        // 1. ابتدا ID متغیرها (variants) را از سبد خرید می‌گیریم
        // (بر اساس متد store، می‌دانیم که $item->id همان variant_id است)
        $cartVariantIds = $cartItems->pluck('id')->toArray();

        // 2. حالا ID محصولات اصلی (والد) را از روی ID متغیرها پیدا می‌کنیم
        // ** این بخش حیاتی است و فرض می‌کند مدل ProductVariant وجود دارد **
        $cartProductIds = ProductVariant::whereIn('id', $cartVariantIds)
                                    ->pluck('product_id') // فقط ستون product_id را بگیر
                                    ->unique()           // ID های تکراری را حذف کن
                                    ->toArray();



        // 3. بسته‌بندی‌هایی را واکشی می‌کنیم که:
        //    الف) به یکی از محصولات داخل سبد لینک شده باشند
        //    ب) یا رایگان (پیش‌فرض) باشند
        
        $packagingOptions = PackagingOption::where('is_active', true)
            ->where(function ($query) use ($cartProductIds) {
                
                // شرط الف: اگر ID محصولی در سبد بود
                if (!empty($cartProductIds)) {
                    $query->whereHas('products', function ($subQuery) use ($cartProductIds) {
                        $subQuery->whereIn('products.id', $cartProductIds);
                    });
                }
                
                // شرط ب: گزینه‌های رایگان (مثل "استاندارد") همیشه نمایش داده شوند
                $query->orWhere('price', 0); 
            })
            ->orderBy('price') // مرتب‌سازی بر اساس قیمت
            ->distinct()      // حذف گزینه‌های تکراری (اگر "استاندارد" هم لینک شده بود و هم رایگان)
            ->get();

        $discountAmount = 0;
        $discountCode = null;
        $discountCondition = Cart::getConditions()->first(function ($c) { return $c->getType() === 'discount'; });
        if ($discountCondition) {
            $calculated = $discountCondition->getCalculatedValue($subtotal);
            $discountAmount = -abs($calculated); // Ensure it's negative
            $discountCode = $discountCondition->getName();
        }

        // Pass 'subtotal', not 'total'
        return view('checkout.index', compact('cartItems', 'subtotal', 'addresses','packagingOptions', 'discountAmount', 'discountCode'));
    }

    // Process the order
    public function store(Request $request)
    {
        if (Cart::isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'سبد خرید شما خالی است.');
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            // --- FIX: Use 'address_line_1' to match migration ---
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'save_address' => 'nullable|boolean',
            // --- FIX: Validate 'shipping_method' instead of 'payment_method' ---
            'shipping_method' => 'required|string|in:pishaz,tipax',
            'packaging_id' => 'required|exists:packaging_options,id',

            'payment_method' => 'required|string|in:online,cod,card',
            'transaction_code' => 'nullable|string|required_if:payment_method,card|max:255',
        ], [
            'transaction_code.required_if' => 'لطفاً کد تراکنش کارت به کارت را وارد کنید.',
        ]);

        // Use a database transaction to ensure all data is saved, or none is.
        DB::beginTransaction();

        try {
            // 1. Create the Shipping Address
            $shippingAddress = Address::create([
                'user_id' => Auth::id(), // Will be null for guests
                'full_name' => $request->full_name,
                // --- FIX: Save 'address_line_1' and 'country' ---
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'phone' => $request->phone,
            ]);

            // If user is logged in and checked "Save Address", save it to their profile
            if (Auth::check() && $request->save_address) {
                Auth::user()->addresses()->create($shippingAddress->toArray());
            }

            // 2. Create the Order
            $subtotal = Cart::getSubTotal();
            
            // --- FIX: Get shipping cost securely from server-side ---
            $shippingCost = $this->shippingOptions[$request->shipping_method] ?? 0;

            $packagingOption = PackagingOption::findOrFail($request->packaging_id);
            $packagingCost = $packagingOption->price;

            $discountAmount = 0;
            $discountCode = null;
            $discountCondition = Cart::getConditions()->first(function ($c) { return $c->getType() === 'discount'; });
            
            if ($discountCondition) {
                $calculated = $discountCondition->getCalculatedValue($subtotal);
                $discountAmount = -abs($calculated); // Ensure it's negative
                $discountCode = $discountCondition->getName();
            }

            $total = $subtotal + $shippingCost + $packagingCost + $discountAmount;
            
            // --- FIX: Get shipping method name ---
            $shippingMethodName = $request->shipping_method === 'pishaz' ? 'پست پیشتاز' : 'تیپاکس';

            $payment_status = 'pending'; // پیش‌فرض برای 'online' و 'card'
            $order_status = 'pending'; // پیش‌فرض برای 'online' و 'card'

            if ($request->payment_method == 'online') {
                $payment_status = 'confirmed'; // پرداخت در محل هنوز پرداخت نشده
            }

            if ($request->payment_method == 'cod') {
                $payment_status = 'pending'; // پرداخت در محل هنوز پرداخت نشده
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $shippingAddress->id, // Assuming same for now
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost, // <-- Save the cost
                'shipping_method' => $shippingMethodName, // <-- Save the name
                'packaging_option_id' => $packagingOption->id, // <-- ذخیره ID بسته‌بندی
                'packaging_cost' => $packagingCost,
                'discount_code' => $discountCode,
                'discount_amount' => abs($discountAmount),
                'total' => $total,
                'payment_method' => $request->payment_method, // <-- اصلاح شد
                'payment_status' => $payment_status, // <-- اصلاح شد
                'transaction_code' => $request->transaction_code,
            ]);

            // --- FIX: Corrected order code generation ---
            $orderCode = date('Ym') . '-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
            
            $order->update([
                'order_code' => $orderCode
            ]);

            // 3. Create Order Items
            foreach (Cart::getContent() as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    // --- FIX: Use $item->id (which is the variant_id from CartController) ---
                    'product_variant_id' => $item->id,
                    'product_name' => $item->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price, // Toman integer
                ]);
                
                // 4. (Optional but recommended) Decrement stock
                // $variant = ProductVariant::find($item->id);
                // $variant->decrement('stock', $item->quantity);
            }

            // 5. Update status (e.g., payment is pending, order is processing)
            $order->update(['status' => 'processing']);
            
            // 6. Clear the cart
            Cart::clear();

            // 7. Commit the transaction
            DB::commit();

            // Redirect to a "Thank You" page
            return redirect()->route('checkout.success', $order)
                ->with('success', 'سفارش شما با موفقیت ثبت شد!');

        } catch (\Exception $e) {
            // If anything went wrong, roll back the database changes
            DB::rollBack();
            return redirect()->back()->with('error', 'خطایی رخ داد: ' . $e->getMessage())->withInput();
        }
    }

    // Show a "Thank You" page
    public function success(Order $order)
    {
        // You'll need to create this view
        return view('checkout.success', compact('order'));
    }

    public function applyDiscount(Request $request)
    {
        $request->validate(['discount_code' => 'required|string']);
        $code = $request->discount_code;
        $subtotal = Cart::getSubTotal();

        $discount = Discount::where('code', $code)->first();

        // Validation checks
        if (!$discount) {
            return response()->json(['success' => false, 'message' => 'کد تخفیف معتبر نیست.'], 404);
        }
        if (!$discount->is_active) {
            return response()->json(['success' => false, 'message' => 'کد تخفیف فعال نیست.'], 422);
        }
        if ($discount->expires_at && $discount->expires_at->isPast()) {
            return response()->json(['success' => false, 'message' => 'کد تخفیف منقضی شده است.'], 422);
        }
        if ($discount->starts_at && $discount->starts_at->isFuture()) {
            return response()->json(['success' => false, 'message' => 'زمان استفاده از این کد تخفیف هنوز شروع نشده است.'], 422);
        }
        if ($discount->usage_limit && $discount->times_used >= $discount->usage_limit) {
            return response()->json(['success' => false, 'message' => 'ظرفیت استفاده از این کد تخفیف تمام شده است.'], 422);
        }
        if ($discount->min_purchase > $subtotal) {
            return response()->json(['success' => false, 'message' => 'حداقل خرید برای استفاده از این کد ' . number_format($discount->min_purchase) . ' تومان است.'], 422);
        }

        // Clear old conditions
        Cart::clearCartConditions();

        // Prepare new condition with absolute value to ensure negativity
        $discount_value = abs($discount->value);
        $value = $discount->type == 'percent' ? '-' . $discount_value . '%' : '-' . $discount_value;
        
        $condition = new CartCondition([
            'name' => $discount->code,
            'type' => 'discount',
            'target' => 'total',
            'value' => $value,
        ]);
        Cart::condition($condition);

        // Get the new calculated discount amount
        $calculated = $condition->getCalculatedValue($subtotal);
        $discountAmount = -abs($calculated); // Ensure it's negative

        return response()->json([
            'success' => true,
            'message' => 'کد تخفیف با موفقیت اعمال شد.',
            'discount_amount' => $discountAmount,
            'discount_code' => $discount->code,
        ]);
    }

    // --- NEW METHOD 2: Remove Discount (JSON Response) ---
    /**
     * Remove applied discount via AJAX from checkout page.
     * (حذف کد تخفیف از طریق ای‌جکس)
     */
    public function removeDiscount()
    {
        Cart::clearCartConditions();
        
        return response()->json([
            'success' => true,
            'message' => 'کد تخفیف حذف شد.',
            'discount_amount' => 0,
            'discount_code' => null,
        ]);
    }
}