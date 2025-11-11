@extends('layouts.app')

@section('title', 'پرداخت')

@section('content')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="container mx-auto px-4 py-12" dir="rtl">
    <h1 class="text-3xl font-bold text-center mb-8">پرداخت</h1>

    @if(session('error'))
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">خطا!</strong>
            <span class="block sm:inline">مشکلاتی در ورودی شما وجود دارد.</span>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row-reverse gap-12" x-data="{
            // --- Basic Values ---
            subtotal: {{ $subtotal }},
            selectedShipping: 'pishaz',
            shippingOptions: {
                pishaz: 35000,
                tipax: 60000
            },
            packagingOptions: {{ $packagingOptions->pluck('price', 'id') }},
            selectedPackaging: {{ $packagingOptions->first()?->id ?? 0 }},
            
            // --- Discount State (from controller) ---
            discountAmount: {{ $discountAmount ?? 0 }},
            discountCode: '{{ $discountCode ?? '' }}',
            selectedPayment: '{{ old('payment_method', 'cod') }}', // 'cod' as default
            
            // --- AJAX Form State (moved from nested component) ---
            discountInput: '{{ $discountCode ?? '' }}', // The text field
            message: '',
            isSuccess: null,
            isLoading: false,
        
            // --- Getters ---
            get shippingCost() {
                return this.shippingOptions[this.selectedShipping] || 0;
            },
            get packagingCost() {
                // Use Number() for safety, matching x-model.number
                return this.packagingOptions[Number(this.selectedPackaging)] || 0;
            },
            get total() {
                // Use Number() to ensure math is correct
                return Number(this.subtotal) + Number(this.shippingCost) + Number(this.packagingCost) + Number(this.discountAmount);
            },
            
            // --- Helper Functions ---
            formatToman(amount) {
                return new Intl.NumberFormat('fa-IR').format(amount) + ' تومان';
            },
        
            // --- AJAX Functions (moved from nested component and fixed) ---
            applyDiscount() {
                if (!this.discountInput) return; // Prevent applying empty code
                this.isLoading = true;
                this.message = '';
                fetch('{{ route('checkout.discount.apply') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ discount_code: this.discountInput })
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(({ status, body }) => {
                    this.message = body.message;
                    if (status === 200 && body.success) {
                        this.isSuccess = true;
                        this.discountAmount = body.discount_amount; // Directly update state
                        this.discountCode = body.discount_code;   // Directly update state
                    } else {
                        this.isSuccess = false;
                        this.discountAmount = 0;
                        this.discountCode = '';
                        this.discountInput = ''; // Clear input on failure
                    }
                })
                .catch(err => {
                    this.message = 'خطا در ارتباط. لطفا اتصال اینترنت خود را بررسی کنید.';
                    this.isSuccess = false;
                })
                .finally(() => {
                    this.isLoading = false;
                });
            },
            
            removeDiscount() {
                this.isLoading = true;
                this.message = '';
                fetch('{{ route('checkout.discount.remove') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.message = data.message;
                    this.isSuccess = true;
                    this.discountInput = ''; // Clear input
                    this.discountAmount = 0; // Clear state
                    this.discountCode = '';  // Clear state
                })
                .catch(err => {
                    this.message = 'خطا در حذف کد. لطفا اتصال اینترنت خود را بررسی کنید.';
                    this.isSuccess = false;
                })
                .finally(() => {
                    this.isLoading = false;
                });
            }
        }">

        <div class="w-full lg:w-2/3">
            <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST">
                @csrf
                <input type="hidden" name="shipping_method" x-model="selectedShipping">
                <input type="hidden" name="payment_method" x-model="selectedPayment">

                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-2xl font-semibold mb-6 text-right">اطلاعات ارسال</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 text-right">نام کامل</label>
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name', Auth::user()->name ?? '') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 text-right">شماره تلفن</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', Auth::user()->mobile ?? '') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="address" class="block text-sm font-medium text-gray-700 text-right">آدرس</label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                    </div>
                    
                    <div class="mt-6">
                        <label for="address_line_2" class="block text-sm font-medium text-gray-700 text-right">پلاک / واحد (اختیاری)</label>

                        <input type="text" id="address_line_2" name="address_line_2" value="{{ old('address_line_2') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right">
                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 text-right">شهر</label>
                            <input type="text" id="city" name="city" value="{{ old('city') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                        </div>
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 text-right">استان</label>
                            <input type="text" id="state" name="state" value="{{ old('state') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                        </div>
                        <div>
                            <label for="zip_code" class="block text-sm font-medium text-gray-700 text-right">کد پستی</label>
                            <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code') }}"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right" required>
                        </div>
                    </div>
                    
                 
                    
                    @auth
                    <div class="mt-6 text-right">
                        <label class="flex items-center justify-end">
                            <input type="checkbox" name="save_address" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 ml-2">
                            <span class="text-sm text-gray-600">ذخیره این آدرس در حساب کاربری من</span>
                        </label>
                    </div>
                    @endauth

                    <h2 class="text-2xl font-semibold mt-8 mb-6 text-right">روش ارسال</h2>
                    <div class="space-y-4">
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer" 
                               :class="{ 'bg-blue-50 border-blue-500 ring-2 ring-blue-500': selectedShipping === 'pishaz' }">
                            <input type="radio" name="shipping_method_option" value="pishaz" x-model="selectedShipping" class="text-blue-600 ml-3">
                            <span class="flex-grow flex justify-between items-center text-sm font-medium text-gray-700">
                                <span>پست پیشتاز</span>
                                <span class="font-bold text-gray-900" x-text="formatToman(shippingOptions.pishaz)"></span>
                            </span>
                        </label>
                        
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer"
                               :class="{ 'bg-blue-50 border-blue-500 ring-2 ring-blue-500': selectedShipping === 'tipax' }">
                            <input type="radio" name="shipping_method_option" value="tipax" x-model="selectedShipping" class="text-blue-600 ml-3">
                            <span class="flex-grow flex justify-between items-center text-sm font-medium text-gray-700">
                                <span>تیپاکس</span>
                                <span class="font-bold text-gray-900" x-text="formatToman(shippingOptions.tipax)"></span>
                            </span>
                        </label>
                    </div>

                    <h2 class="text-2xl font-semibold mt-8 mb-6 text-right">نوع بسته‌بندی</h2>
                    <div class="space-y-4">
                        @forelse ($packagingOptions as $option)
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer" 
                               :class="{ 'bg-blue-50 border-blue-500 ring-2 ring-blue-500': selectedPackaging == {{ $option->id }} }">
                            <input type="radio" name="packaging_id" value="{{ $option->id }}" x-model.number="selectedPackaging" class="text-blue-600 ml-3">
                            <span class="flex-grow flex justify-between items-center text-sm font-medium text-gray-700">
                                <span>{{ $option->name }}</span>
                                <span class="font-bold text-gray-900">{{ number_format($option->price) }} تومان</span>
                            </span>
                        </label>
                        @empty
                        <p class="text-gray-500 text-right">گزینه بسته‌بندی فعالی وجود ندارد.</p>
                        @endforelse
                    </div>

                </div>
            </form>
        </div>

        

        <div class="w-full lg:w-1/3">
            <div class="bg-white shadow-md rounded-lg p-6 sticky top-8">
                <h2 class="text-2xl font-semibold mb-6 text-right">خلاصه سفارش</h2>
                
                <div class="mb-6">
                    <label for="discount_code" class="block text-sm font-medium text-gray-700 text-right">کد تخفیف</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        
                        <button type="button" @click.prevent="!discountCode ? applyDiscount() : removeDiscount()" :disabled="isLoading"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-gray-50 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-r-md disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isLoading" x-text="!discountCode ? 'اعمال' : 'حذف'"></span>
                            <span x-show="isLoading">...</span>
                        </button>
                        
                        <input type="text" id="discount_code" x-model="discountInput" 
                            class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border-gray-300 text-right" 
                            placeholder="کد تخفیف خود را وارد کنید" 
                            :disabled="isLoading"> </div>
                
                    <template x-if="message">
                        <p class="mt-2 text-sm" :class="{ 'text-green-600': isSuccess, 'text-red-600': !isSuccess }" x-text="message"></p>
                    </template>
                </div>


                <div class="space-y-4">
                    @foreach ($cartItems->sortBy('name') as $item)
                        <div class="flex justify-between items-center">
                            <p class="text-gray-700">{{ number_format($item->getPriceSum()) }} <span class="text-xs">تومان</span></p>
                            <div class="flex-grow text-right">
                                <p class="font-medium">{{ $item->name }}</p>
                                <p class="text-sm text-gray-500">تعداد: {{ $item->quantity }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="border-t my-6"></div>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <p class="font-medium" x-text="formatToman(subtotal)"></p>
                        <p class="text-gray-600">جمع سبد خرید</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-medium" x-text="formatToman(shippingCost)"></p>
                        <p class="text-gray-600">هزینه ارسال</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="font-medium" x-text="formatToman(packagingCost)"></p>
                        <p class="text-gray-600">هزینه بسته‌بندی</p>
                    </div>

                    <template x-if="discountAmount < 0">
                        <div class="flex justify-between text-red-600">
                            <p class="font-medium" x-text="formatToman(discountAmount)"></p>
                            <p class="text-gray-600">
                                تخفیف (<span x-text="discountCode"></span>)
                            </p>
                        </div>
                    </template>

                    <div class="border-t my-4"></div>
                    <div class="flex justify-between text-lg font-bold">
                        <p x-text="formatToman(total)"></p>
                        <p>مبلغ کل</p>
                    </div>
                </div>

                <div class="border-t my-6"></div>
                <h3 class="text-lg font-semibold mb-4 text-right">روش پرداخت</h3>
                <div class="space-y-4">
                    <!-- گزینه ۱: پرداخت آنلاین (فعلا غیرفعال) -->
                    <label class="flex items-center p-4 border rounded-lg cursor-not-allowed bg-gray-50 opacity-50"
                           :class="{ 'bg-blue-50 border-blue-500 ring-2 ring-blue-500': selectedPayment === 'online' }">
                        <input type="radio" name="payment_method_option" value="online" x-model="selectedPayment" class="text-blue-600 ml-3" disabled>
                        <span class="flex-grow text-sm font-medium text-gray-700">
                            پرداخت آنلاین (به زودی)
                        </span>
                    </label>
                    
                    <!-- گزینه ۲: پرداخت در محل -->
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer"
                           :class="{ 'bg-blue-50 border-blue-500 ring-2 ring-blue-500': selectedPayment === 'cod' }">
                        <input type="radio" name="payment_method_option" value="cod" x-model="selectedPayment" class="text-blue-600 ml-3">
                        <span class="flex-grow text-sm font-medium text-gray-700">
                            پرداخت در محل
                        </span>
                    </label>

                    <!-- گزینه ۳: کارت به کارت -->
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer"
                           :class="{ 'bg-blue-50 border-blue-500 ring-2 ring-blue-500': selectedPayment === 'card' }">
                        <input type="radio" name="payment_method_option" value="card" x-model="selectedPayment" class="text-blue-600 ml-3">
                        <span class="flex-grow text-sm font-medium text-gray-700">
                            کارت به کارت
                        </span>
                    </label>
                    
                    <!-- فیلد کد تراکنش (فقط وقتی کارت به کارت انتخاب است) -->
                    <div x-show="selectedPayment === 'card'" x-transition style="display: none;">
                        <label for="transaction_code" class="block text-sm font-medium text-gray-700 text-right">کد تراکنش</label>
                        <input type="text" name="transaction_code" id="transaction_code" 
                               form="checkout-form"
                               value="{{ old('transaction_code') }}"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-left" 
                               dir="ltr"
                               placeholder="123456789">
                        @error('transaction_code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-2">لطفاً پس از واریز، کد تراکنش را در این فیلد وارد کنید.</p>
                    </div>
                </div>
                <!-- --- End Payment Section --- -->

                <button type="submit" form="checkout-form"
                        class="w-full mt-8 bg-blue-600 text-white text-lg font-semibold py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    ثبت سفارش
                </button>
            </div>
        </div>
    </div>
</div>
@endsection