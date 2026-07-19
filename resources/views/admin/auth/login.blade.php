<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود ادمین</title>
    <script src="{{ asset('js/tailwindcss.js') }}"></script>
    <style>
        body { font-family: Tahoma, 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-center text-gray-900 mb-6">
            ورود به پنل مدیریت
        </h1>

        @if (session('success'))
            <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 font-medium text-sm text-red-600 bg-red-100 p-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-50 p-3 rounded">
                <div class="font-medium text-red-600">
                    اوه! مشکلی پیش آمد.
                </div>
                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- فرم اول: ورود با کلمه عبور -->
        <form method="POST" action="{{ route('admin.login.password') }}" class="space-y-4 border-b-2 border-gray-200 pb-6">
            @csrf
            <h2 class="text-sm font-semibold text-gray-700">ورود با کلمه عبور</h2>
            
            <div>
                <label for="mobile_pass" class="block text-sm font-medium text-gray-700">شماره موبایل</label>
                <input id="mobile_pass" type="tel" name="mobile" value="{{ old('mobile') }}" required autofocus dir="ltr"
                       class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-left">
            </div>

            <div class="mt-4">
                <label for="password" class="block text-sm font-medium text-gray-700">رمز عبور</label>
                <input id="password" type="password" name="password" required dir="ltr"
                       class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-left">
            </div>

            <div class="flex items-center justify-between mt-4">
                <label for="remember" class="flex items-center">
                    <input id="remember" type="checkbox" name="remember" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <span class="mr-2 text-sm text-gray-600">مرا به خاطر بسپار</span>
                </label>
            </div>

            <div class="mt-4">
                <button type="submit"
                        class="w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none">
                    ورود امن
                </button>
            </div>
        </form>

        <!-- فرم دوم: ورود پیامکی -->
        <form method="POST" action="{{ route('admin.otp.send') }}" class="space-y-4 pt-2">
            @csrf
            <h2 class="text-sm font-semibold text-gray-700">ورود با کد یکبار مصرف (پیامک)</h2>
            
            <div>
                <label for="mobile_otp" class="block text-sm font-medium text-gray-700">شماره موبایل</label>
                <input id="mobile_otp" type="tel" name="mobile" required dir="ltr"
                       class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-left">
            </div>

            <div class="mt-4">
                <button type="submit"
                        class="w-full px-4 py-2 text-base font-medium text-white bg-gray-800 border border-transparent rounded-md shadow-sm hover:bg-gray-900 focus:outline-none">
                    دریافت کد تایید
                </button>
            </div>
        </form>

    </div>

</body>
</html>