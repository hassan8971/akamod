<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    /**
     * Get mobile number, "send" (simulate) OTP.
     */
    public function sendOtp(Request $request)
    {
        $mobile = $this->normalizeMobile($request->input('mobile'));

        $validator = Validator::make(
            ['mobile' => $mobile],
            ['mobile' => ['required', 'string', 'regex:/^09[0-9]{9}$/']],
            ['mobile.regex' => 'فرمت شماره موبایل صحیح نیست. (مثال: 09123456789)']
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطای اعتبارسنجی',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $otp = '1234';

        // Save in cache for 5 minutes
        Cache::put('otp_' . $mobile, $otp, now()->addMinutes(5));

        return response()->json([
            'success' => true,
            'message' => 'کد تایید (1234) با موفقیت "ارسال" شد.',
            'mobile' => $mobile
        ]);
    }

    /**
     * Verify OTP, find/create user, and return Sanctum Token.
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'mobile' => 'required|string',
            'code' => 'required|numeric|digits:4',
        ]);

        $mobile = $this->normalizeMobile($validated['mobile']);

        $cacheKey = 'otp_' . $mobile;
        $cachedOtp = Cache::get($cacheKey);

        if (!$cachedOtp) {
            return response()->json(['success' => false, 'message' => 'کد منقضی شده است. لطفا دوباره تلاش کنید.'], 419);
        }

        
        if ($validated['code'] != $cachedOtp) {
            return response()->json(['success' => false, 'message' => 'کد وارد شده صحیح نمی‌باشد.'], 401);
        }

        
        Cache::forget($cacheKey);

        
        $user = null;
        $guard = 'web';
        $abilities = ['role:user'];

        $admin = Admin::where('mobile', $mobile)->first();

        if ($admin) {
            $user = $admin;
            $guard = 'admin';
            $abilities = ['role:admin'];
        } else {
            $user = User::firstOrCreate(
                ['mobile' => $mobile],
                ['name' => 'کاربر ' . Str::random(4)]
            );
            $guard = 'web';
        }

        // Generate sanctum token
        $user->tokens()->delete();
        $token = $user->createToken('api-token', $abilities)->plainTextToken;

        // Send token to front-end
        return response()->json([
            'success' => true,
            'message' => 'ورود با موفقیت انجام شد.',
            'token' => $token,
            'user' => $user,
            'guard' => $guard,
        ]);
    }

    /**
     * Helper function to normalize mobile numbers.
     */
    private function normalizeMobile(string $mobile): string
    {
        $mobile = preg_replace('/[^\d]/', '', $mobile);
        if (str_starts_with($mobile, '98')) {
            $mobile = '0' . substr($mobile, 2);
        }
        if (strlen($mobile) == 10 && str_starts_with($mobile, '9')) {
            $mobile = '0' . $mobile;
        }
        return $mobile;
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request)
    {
        // This method *requires* the user to be authenticated via Sanctum
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'خروج با موفقیت انجام شد.'
        ]);
    }
}