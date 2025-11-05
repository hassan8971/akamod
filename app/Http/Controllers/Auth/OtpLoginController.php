<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; // <-- این را اضافه کنید

class OtpLoginController extends Controller
{
    /**
     * Show the form to request an OTP.
     * (نمایش فرم ورود شماره موبایل)
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * --- این متد اصلاح شده است ---
     * Send the OTP (hardcoded 1234) and redirect to verification page.
     * (ارسال کد و هدایت به صفحه تایید)
     */
    public function sendOtp(Request $request)
    {
        // 1. Validate that input is present
        $request->validate([
            'mobile' => 'required|string',
        ]);

        // 2. Normalize the mobile number
        $mobile = $this->normalizeMobile($request->input('mobile'));

        // 3. Validate the *normalized* number
        $validator = Validator::make(
            ['mobile' => $mobile],
            [
                'mobile' => [
                    'required',
                    'string',
                    'regex:/^09[0-9]{9}$/' // اعتبارسنجی استاندارد موبایل ایران
                ]
            ],
            [
                'mobile.regex' => 'فرمت شماره موبایل صحیح نیست. (مثال: 09123456789)',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // 4. Continue with OTP logic
        $otp = '1234'; // کد ثابت شما

        // Store OTP and *normalized* mobile number in session
        $request->session()->put('otp', $otp);
        $request->session()->put('mobile', $mobile);
        
        return redirect()->route('otp.verify.form')
            ->with('success', 'کد تایید (1234) برای شما "ارسال" شد.');
    }

    /**
     * Show the form to enter the OTP.
     * (نمایش فرم ورود کد تایید)
     */
    public function showVerifyForm(Request $request)
    {
        // If the user hasn't requested an OTP, redirect them back
        if (!$request->session()->has('mobile')) {
            return redirect()->route('login');
        }

        $mobile = $request->session()->get('mobile');

        return view('auth.verify', compact('mobile'));
    }

    /**
     * Verify the OTP and log the user in.
     * (تایید کد، و ورود یا ثبت نام کاربر)
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|numeric|digits:4',
        ]);

        // Check if session data exists
        if (!$request->session()->has('mobile') || !$request->session()->has('otp')) {
            return redirect()->route('login')->with('error', 'جلسه‌ی شما منقضی شده است. لطفا دوباره تلاش کنید.');
        }

        $sessionOtp = $request->session()->get('otp');
        $sessionMobile = $request->session()->get('mobile');

        // Check if the code is correct
        if ($validated['code'] != $sessionOtp) {
            return redirect()->back()->with('error', 'کد وارد شده صحیح نمی‌باشد.');
        }

        $admin = Admin::where('mobile', $sessionMobile)->first();

        if ($admin) {
            // ادمین پیدا شد!
            Auth::guard('admin')->login($admin); // ورود با گارد 'admin'
            $request->session()->forget(['otp', 'mobile']);
            return redirect()->route('admin.dashboard'); // هدایت به داشبورد ادمین
        }

        

        // Find the user or create a new one if they don't exist.
        $user = User::firstOrCreate(
            ['mobile' => $sessionMobile],
            ['name' => 'کاربر'] // Set a default name. 'password' will be null.
        );

        Auth::guard('web')->login($user); // ورود با گارد 'web' (پیش‌فرض)
        $request->session()->forget(['otp', 'mobile']);
        
        return redirect()->route('user.index');
    }


    /**
     * --- این متد جدید است ---
     * Helper function to normalize mobile numbers.
     * (تبدیل شماره موبایل به فرمت استاندارد 09)
     */
    private function normalizeMobile(string $mobile): string
    {
        // 1. Remove all non-digit characters (like spaces, +, -)
        $mobile = preg_replace('/[^\d]/', '', $mobile);

        // 2. Handle +98
        if (str_starts_with($mobile, '98')) {
            $mobile = '0' . substr($mobile, 2);
        }
        // Note: +98 was already handled by preg_replace (removed '+')

        // 3. Handle 9 (e.g., 9123456789)
        if (strlen($mobile) == 10 && str_starts_with($mobile, '9')) {
            $mobile = '0' . $mobile;
        }

        return $mobile;
    }
}