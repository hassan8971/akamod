<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PackagingOption;
use App\Models\Discount;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // 💡 برای ثبت خطاهای احتمالی بانک
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{

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
            'shipping_method' => 'required|string|exists:shipping_methods,method_key',
            'packaging_id' => 'required|integer|min:0',
            'discount_code' => 'nullable|string',
            'payment_method' => 'required|string|in:online,cod,card,digipay',
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
                $rawPrice = $variant->discount_price ?? $variant->price;
                
                // 💡 حذف کاما (در صورت وجود) و تبدیل قطعی به عدد صحیح
                $price = (int) str_replace(',', '', $rawPrice);
                
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
            // دریافت اطلاعات روش ارسال از دیتابیس به صورت داینامیک
            $shippingMethodModel = ShippingMethod::where('method_key', $validated['shipping_method'])->first();
            $shippingCost = $shippingMethodModel ? $shippingMethodModel->cost : 0;
            $shippingMethodName = $shippingMethodModel ? $shippingMethodModel->title : $validated['shipping_method'];
            

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
            
            
            // 💡 وضعیت پرداخت در لحظه ثبت سفارش همیشه باید pending باشد
            $payment_status = 'pending';
            $order_status = 'pending';

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

            

            // ==========================================
            // 💡 اتصال به درگاه پارسیان در صورت پرداخت آنلاین
            // ==========================================
            if ($validated['payment_method'] == 'online') {
                
                // 💡 جلوگیری از ورود مبالغ نامعتبر به بانک
                if ($total < 1000) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false, 
                        'message' => 'مبلغ کل سفارش برای پرداخت آنلاین نامعتبر است: ' . number_format($total) . ' تومان'
                    ], 422);
                }

                $pin = 'KTb88t1W5v81863Aay85';
                
                // 💡 تبدیل قطعی به عدد صحیح و گرد کردن (برای حذف هرگونه اعشار مخفی)
                $amountInRial = (int) round($total * 10);
                
                // آدرسی که بانک پس از پرداخت کاربر را به آن برمی‌گرداند
                $callbackUrl = url('/api/v1/payment/verify'); 

                $params = [
                    "LoginAccount" => $pin,
                    "Amount"       => $amountInRial,
                    "OrderId"      => $order->id,
                    "CallBackUrl"  => $callbackUrl,
                    "AdditionalData" => "",
                    "Originator"   => ""
                ];

                try {
                    // ارسال درخواست به فایل واسط (پروکسی) در هاست اصلی
                    $response = Http::asForm()->post('https://akaleather.com/pec-proxy.php', [
                        'secret' => 'AkaLeather_PROXY_1402!@#',
                        'action' => 'sale',
                        'params' => json_encode($params)
                    ]);
                    
                    $resData = json_decode($response->body());

                    if (isset($resData->success) && $resData->success === true) {
                        $result = $resData->data;
                        
                        if (isset($result->SalePaymentRequestResult->Token) && $result->SalePaymentRequestResult->Status === 0) {
                            $token = $result->SalePaymentRequestResult->Token;
                            $order->update(['transaction_code' => $token]);

                            DB::commit();
                            
                            return response()->json([
                                'success' => true,
                                'payment_url' => "https://pec.shaparak.ir/NewIPG/?Token=" . $token
                            ], 200);
                        } else {
                            return response()->json([
                                'success' => false, 
                                'message' => 'خطا در درگاه: ' . ($result->SalePaymentRequestResult->Message ?? 'نامشخص')
                            ], 500);
                        }
                    } else {
                        throw new \Exception($resData->message ?? 'خطای ناشناخته از پروکسی');
                    }
                } catch (\Exception $ex) {
                    Log::error("Parsian Proxy GateWay Error: " . $ex->getMessage());
                    return response()->json([
                        'success' => false, 
                        'message' => 'خطای سرور واسط در ارتباط با بانک'
                    ], 500);
                }
            }

            // ==========================================
            // اتصال به درگاه دیجی پی
            // ==========================================
            if ($validated['payment_method'] == 'digipay') {
                if ($total < 1000) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'مبلغ کل سفارش نامعتبر است: ' . number_format($total) . ' تومان'], 422);
                }

                $amountInRial = (int) round($total * 10);
                $callbackUrl = url('/api/v1/payment/digipay/verify');

                $baseUrl = rtrim(env('DIGIPAY_BASE_URL', 'https://api.mydigipay.com/digipay/api'), '/');
                
                try {
                    $authString = base64_encode(env('DIGIPAY_CLIENT_ID') . ':' . env('DIGIPAY_CLIENT_SECRET'));

                    // دریافت توکن لاگین دیجی‌پی
                    $tokenResponse = Http::asForm()
                        ->withHeaders([
                            'Authorization' => 'Basic ' . $authString
                        ])
                        ->post($baseUrl . '/oauth/token', [
                            'username' => env('DIGIPAY_USERNAME'),
                            'password' => env('DIGIPAY_PASSWORD'),
                            'grant_type' => 'password'
                        ]);

                    if (!$tokenResponse->successful()) {
                        Log::error("Digipay Token Error: " . $tokenResponse->body());
                        return response()->json(['success' => false, 'message' => 'خطا در دریافت توکن دیجی‌پی'], 500);
                    }
                    $accessToken = $tokenResponse->json('access_token');

                    // درخواست تیکت خرید
                    $ticketResponse = Http::withHeaders([
                            'Agent' => 'WEB',
                            'Digipay-Version' => '2022-02-02',
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type' => 'application/json'
                        ])
                        ->post($baseUrl . '/tickets/business?type=11', [
                            'amount' => $amountInRial,
                            'cellNumber' => $validated['address']['phone'], 
                            'providerId' => (string)$order->id,
                            'callbackUrl' => $callbackUrl
                        ]);

                    if ($ticketResponse->successful() && $ticketResponse->json('ticket')) {
                        $order->update(['transaction_code' => $ticketResponse->json('ticket')]);

                        DB::commit();
                        
                        return response()->json([
                            'success' => true,
                            'payment_url' => $ticketResponse->json('redirectUrl') 
                        ]);
                    } else {
                        Log::error("Digipay Ticket Error: " . $ticketResponse->body());
                        return response()->json(['success' => false, 'message' => 'خطا در ایجاد تیکت پرداخت دیجی‌پی'], 500);
                    }

                } catch (\Exception $ex) {
                    Log::error("Digipay Connection Error: " . $ex->getMessage());
                    return response()->json(['success' => false, 'message' => 'خطای سرور در اتصال به دیجی‌پی'], 500);
                }
            }

            // ==========================================
            // اگر پرداخت آنلاین نبود (نقدی یا کارت به کارت)
            // ==========================================

            DB::commit();


            return response()->json([
                'success' => true,
                'message' => 'سفارش شما با موفقیت ثبت شد!',
                'order_code' => $orderCode,
                // به جای هدایت به لاراول، مستقیماً آدرس وردپرس را می‌دهیم
                'redirect_url' => "https://akaleather.com/payment-result/?order_id={$order->id}&status=success"
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Checkout Store Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'خطایی در ثبت سفارش رخ داد.'], 500);
        }
    }

    // ==========================================
    // متد وریفای اختصاصی دیجی‌پی
    // ==========================================
    public function verifyDigipayPayment(Request $request)
    {
        $amount = $request->input('amount'); 
        $providerId = $request->input('providerId'); 
        $trackingCode = $request->input('trackingCode'); 
        $resultStatus = $request->input('result'); 
        $type = $request->input('type'); 

        $order = Order::find($providerId);

        if (!$order) {
            return redirect("https://akaleather.com/payment-result/?order_id=0&status=cancelled");
        }

        // اگر قبلاً تایید شده
        if ($order->payment_status === 'confirmed') {
            return redirect("https://akaleather.com/checkout/success/{$order->id}?status=success&rrn={$trackingCode}"); 
        }

        $frontendSuccessUrl = "https://akaleather.com/payment-result/?order_id={$order->id}&status=success&rrn={$trackingCode}";
        $frontendFailedUrl  = "https://akaleather.com/payment-result/?order_id={$order->id}&status=cancelled";

        // لغو توسط کاربر یا خطای اولیه
        if ($resultStatus !== 'SUCCESS') {
            $this->restoreOrderInventory($order); // بازگردانی موجودی
            return redirect($frontendFailedUrl);
        }

        $baseUrl = rtrim(env('DIGIPAY_BASE_URL', 'https://api.mydigipay.com/digipay/api'), '/');

        try {
            $authString = base64_encode(env('DIGIPAY_CLIENT_ID') . ':' . env('DIGIPAY_CLIENT_SECRET'));
            $tokenResponse = Http::asForm()
                ->withHeaders(['Authorization' => 'Basic ' . $authString])
                ->post($baseUrl . '/oauth/token', [
                    'username' => env('DIGIPAY_USERNAME'),
                    'password' => env('DIGIPAY_PASSWORD'),
                    'grant_type' => 'password'
                ]);

            if ($tokenResponse->successful()) {
                $accessToken = $tokenResponse->json('access_token');
                $verifyType = $type ?? 11; 

                $verifyResponse = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json'
                    ])
                    ->post($baseUrl . "/purchases/verify?type={$verifyType}", [
                        'trackingCode' => (string)$trackingCode,
                        'providerId' => (string)$providerId
                    ]);

                if ($verifyResponse->successful() && $verifyResponse->json('result.status') === 0) { 
                    $order->update([
                        'payment_status' => 'confirmed',
                        'transaction_code' => $trackingCode
                    ]);
                    return redirect($frontendSuccessUrl);
                } else {
                    Log::error("Digipay Verify Failed: " . $verifyResponse->body());
                    $this->restoreOrderInventory($order); // خطای تایید نهایی
                }
            } else {
                $this->restoreOrderInventory($order); // خطای توکن
            }
            
            return redirect($frontendFailedUrl);

        } catch (\Exception $ex) {
            Log::error("Digipay Verify Exception: " . $ex->getMessage());
            $this->restoreOrderInventory($order); // خطای شبکه یا اکسپشن
            return redirect($frontendFailedUrl);
        }
    }


    // ==========================================
    // 💡 متد وریفای بعد از بازگشت کاربر از بانک
    // ==========================================
    public function verifyPayment(Request $request)
    {
        // ثبت تمام دیتای دریافتی از بانک برای دیباگ دقیق
        Log::error('BANK CALLBACK DATA: ' . json_encode($request->all()));

        $token = $request->input('Token');
        $status = $request->input('status');
        $orderId = $request->input('OrderId');
        $terminalNo = $request->input('TerminalNo');
        $RRN = $request->input('RRN');

        $frontendSuccessUrl = "https://akaleather.com/payment-result/?order_id={$orderId}&status=success&rrn={$RRN}";
        $frontendFailedUrl  = "https://akaleather.com/payment-result/?order_id={$orderId}&status=cancelled";
        
        $order = Order::find($orderId);
        if (!$order) {
            Log::error('VERIFY ERROR: Order ' . $orderId . ' not found!');
            return redirect($frontendFailedUrl); 
        }

        if ($order->payment_status === 'confirmed') {
            return redirect($frontendSuccessUrl); 
        }

        if (!$token || $status != 0) {
            Log::error('PAYMENT CANCELLED BY USER. Status: ' . $status);
            $this->restoreOrderInventory($order);
            return redirect($frontendFailedUrl); 
        }

        $pin = 'KTb88t1W5v81863Aay85';
        $params = [
            "LoginAccount" => $pin,
            "Token" => $token
        ];

        try {
            $response = Http::asForm()->post('https://akaleather.com/pec-proxy.php', [
                'secret' => 'AkaLeather_PROXY_1402!@#',
                'action' => 'confirm',
                'params' => json_encode($params)
            ]);
            
            $resData = json_decode($response->body());

            if (isset($resData->success) && $resData->success === true) {
                $result = $resData->data;
                
                if (isset($result->ConfirmPaymentResult->Status) && $result->ConfirmPaymentResult->Status == 0) {
                    $order->update([
                        'payment_status' => 'confirmed',
                        'transaction_code' => $RRN 
                    ]);
                    return redirect($frontendSuccessUrl);
                } else {
                    $this->restoreOrderInventory($order); // بازگردانی موجودی
                    return redirect($frontendFailedUrl);
                }
            } else {
                throw new \Exception($resData->message ?? 'خطای ناشناخته از پروکسی در مرحله تایید');
            }
        } catch (\Exception $ex) {
            Log::error("Parsian Proxy Verify Error: " . $ex->getMessage());
            $this->restoreOrderInventory($order); // بازگردانی موجودی در صورت خطای سرور
            return redirect($frontendFailedUrl);
        }
    }

    // ==========================================
    // متد کمکی برای بازگردانی موجودی در صورت لغو پرداخت
    // ==========================================
    private function restoreOrderInventory($order)
    {
        Log::error('RESTORE INVENTORY STARTED FOR ORDER: ' . $order->id);
        
        // بررسی می‌کنیم که سفارش از قبل لغو نشده باشد
        if ($order->status !== 'failed' && $order->status !== 'cancelled') {
            
            $items = $order->items()->get(); 
            
            if ($items->isEmpty()) {
                Log::error('RESTORE FAILED: No items found for order ' . $order->id);
            }

            foreach ($items as $item) {
                $variant = \App\Models\ProductVariant::find($item->product_variant_id);
                if ($variant) {
                    $variant->increment('stock', $item->quantity);
                    Log::error('RESTORE SUCCESS: Added ' . $item->quantity . ' to variant ' . $variant->id);
                } else {
                    Log::error('RESTORE ERROR: Variant not found in DB -> ' . $item->product_variant_id);
                }
            }
            
            // 💡 تغییر وضعیت‌ها به cancelled برای هماهنگی با فرانت‌اند
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'cancelled'
            ]);
            Log::error('ORDER STATUS UPDATED TO CANCELLED.');
        } else {
            Log::error('RESTORE SKIPPED: Order was already failed/cancelled.');
        }
    }
}