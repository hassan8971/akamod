<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت نام</title>
    <script src="https://mailfa.com/js/tailwind.min.js"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen py-12">

    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-center text-gray-900">
            ایجاد حساب کاربری
        </h1>

        @if ($errors->any())
            <div class="mb-4 text-right">
                <div class="font-medium text-red-600">
                    خطا! مشکلی پیش آمده است.
                </div>
                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 text-right">نام</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-right">
            </div>

            <div class="mt-4">
                <label for="mobile" class="block text-sm font-medium text-gray-700 text-right">شماره موبایل</label>
                <input id="mobile" type="text" name="mobile" value="{{ old('mobile') }}" required
                       class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-right">
            </div>

            <div class="mt-4">
                <label for="password" class="block text-sm font-medium text-gray-700 text-right">رمز عبور</label>
                <input id="password" type="password" name="password" required
                       class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:ring-indigo-500 text-right">
            </div>

            <div class="mt-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 text-right">تکرار رمز عبور</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:ring-indigo-500 text-right">
            </div>


            <div class="flex flex-col items-center justify-center mt-6 space-y-4">
                <button type="submit"
                        class="w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    ثبت نام
                </button>
                <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                    قبلاً ثبت نام کرده‌اید؟ وارد شوید
                </a>
            </div>
        </form>
    </div>

</body>
</html>