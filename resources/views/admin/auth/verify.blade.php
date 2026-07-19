<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تایید کد ورود</title>
    <script src="{{ asset('js/tailwindcss.js') }}"></script>
    <style>
        body { font-family: Tahoma, 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-center text-gray-900 mb-6">
            کد تایید را وارد کنید
        </h1>

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                {{ session('error') }}
            </div>
        @endif
        
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
                {{ session('success') }}
            </div>
        @endif
        
        <p class="text-center text-gray-600 mb-6 text-sm leading-loose">
            کد تایید ۵ رقمی پیامک شده به شماره
            <span class="font-bold text-gray-800" dir="ltr">{{ $mobile ?? session('admin_mobile') }}</span>
            را در کادر زیر وارد کنید.
        </p>

        <form method="POST" action="{{ route('admin.otp.verify') }}" class="space-y-6">
            @csrf

            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 text-right">کد تایید ۵ رقمی</label>
                <input id="code" type="tel" name="code" 
                       required autofocus autocomplete="one-time-code"
                       class="w-full px-3 py-3 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-center tracking-[1em] font-bold text-lg"
                       maxlength="5" dir="ltr">
                @error('code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <button type="submit"
                        class="w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none">
                    بررسی کد و ورود
                </button>
            </div>

            <div class="text-center mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.login') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                    بازگشت به صفحه ورود
                </a>
            </div>
        </form>
    </div>

</body>
</html>