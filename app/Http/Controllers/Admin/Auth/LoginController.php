<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    // ۱. نمایش فرم لاگین ادمین
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    // ۲. ورود با نام کاربری (موبایل) و پسورد
    public function loginWithPassword(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
            'password' => 'required|string',
        ]);

        $mobile = $this->normalizeMobile($request->input('mobile'));

        // تلاش برای ورود با گارد اختصاصی ادمین
        if (Auth::guard('admin')->attempt(['mobile' => $mobile, 'password' => $request->password], $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'شماره موبایل یا رمز عبور اشتباه است.');
    }

    // ۳. درخواست کد پیامکی (مخصوص ادمین)
    public function sendOtp(Request $request)
    {
        $mobile = $this->normalizeMobile($request->input('mobile'));

        $validator = Validator::make(['mobile' => $mobile], [
            'mobile' => ['required', 'string', 'regex:/^09[0-9]{9}$/']
        ], ['mobile.regex' => 'فرمت شماره موبایل صحیح نیست.']);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // بررسی اینکه آیا این شماره اصلاً متعلق به ادمین است؟
        $admin = Admin::where('mobile', $mobile)->first();
        if (!$admin) {
            return back()->with('error', 'شما دسترسی ورود به پنل ادمین را ندارید.');
        }

        $otp = rand(10000, 99999);
        Cache::put('admin_otp_' . $mobile, $otp, now()->addMinutes(5));

        // ارسال پیامک با فراز اس‌ام‌اس
        try {
            $apiKey  = 'UY7DZPKVoti8cVCAlb1TyEV2cGvSzvzal4M3wog8XABjFAtBk2'; 
            $pattern = 'lXVJA5vH5g'; 
            $sender  = '50002178584000'; 

            Http::withHeaders([
                'Api-Key'      => $apiKey,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->post('https://api.iranpayamak.com/ws/v1/sms/pattern', [
                'code'          => $pattern,
                'attributes'    => ['code' => (string) $otp],
                'recipient'     => $mobile,
                'line_number'   => $sender,
                'number_format' => 'english'
            ]);

            $request->session()->put('admin_mobile', $mobile);
            return redirect()->route('admin.verify.form')->with('success', 'کد ورود به سیستم مدیریت ارسال شد.');

        } catch (\Exception $e) {
            Log::error('Admin SMS Send Error: ' . $e->getMessage());
            return back()->with('error', 'خطا در ارسال پیامک. لطفاً با پسورد وارد شوید.');
        }
    }

    // ۴. نمایش فرم تایید کد
    public function showVerifyForm(Request $request)
    {
        if (!$request->session()->has('admin_mobile')) {
            return redirect()->route('admin.login');
        }
        $mobile = $request->session()->get('admin_mobile');
        return view('admin.auth.verify', compact('mobile'));
    }

    // ۵. تایید کد و ورود نهایی
    public function verifyOtp(Request $request)
    {
        $request->validate(['code' => 'required|numeric|digits:5']);

        if (!$request->session()->has('admin_mobile')) {
            return redirect()->route('admin.login')->with('error', 'جلسه منقضی شده است.');
        }

        $mobile = $request->session()->get('admin_mobile');
        $cachedOtp = Cache::get('admin_otp_' . $mobile);

        if (!$cachedOtp || $request->code != $cachedOtp) {
            return back()->with('error', 'کد وارد شده اشتباه یا منقضی شده است.');
        }

        $admin = Admin::where('mobile', $mobile)->first();
        
        if ($admin) {
            Auth::guard('admin')->login($admin);
            Cache::forget('admin_otp_' . $mobile);
            $request->session()->forget('admin_mobile');
            $request->session()->regenerate();
            
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'شما دسترسی ورود به پنل ادمین را ندارید.');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    private function normalizeMobile(string $mobile): string
    {
        $mobile = preg_replace('/[^\d]/', '', $mobile);
        if (str_starts_with($mobile, '98')) $mobile = '0' . substr($mobile, 2);
        if (strlen($mobile) == 10 && str_starts_with($mobile, '9')) $mobile = '0' . $mobile;
        return $mobile;
    }
}