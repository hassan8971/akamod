<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http; // 💡 این خط برای ارسال درخواست به sms.ir اضافه شد
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Get mobile number, send real OTP via sms.ir
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
        
        $otp = rand(10000, 99999);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'text/plain',
                'x-api-key'    => 'mFGKjbt0cQLfFWk74E1mOmTiJrgdwApGTkykBrklBUhUYh5b' 
            ])->post('https://api.sms.ir/v1/send/verify', [
                'mobile'     => $mobile,
                'templateId' => 123456, 
                'parameters' => [
                    [
                        'name'  => 'Code',
                        'value' => (string) $otp
                    ]
                ]
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] == 1) {
                Cache::put('otp_' . $mobile, $otp, now()->addMinutes(5));

                return response()->json([
                    'success' => true,
                    'message' => 'کد تایید با موفقیت ارسال شد.',
                    'mobile' => $mobile
                ]);
            }

            // 💡 لاگ دقیق پاسخی که از سرور پیامک آمده اما موفق نبوده است
            Log::error('SMS.ir Failed Response: ', [
                'status_code' => $response->status(),
                'body' => $response->body()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'خطا در ارسال پیامک: ' . ($result['message'] ?? 'دلیل نامشخص')
            ], 500);

        } catch (\Exception $e) {
            // 💡 لاگ دقیق خطای سرور (مثلا مشکل SSL، تایم اوت، یا قطعی اینترنت سرور لاراول)
            Log::error('SMS.ir Exception/Crash: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'ارتباط با سرور پیامک برقرار نشد. (لطفا لاگ لاراول را چک کنید)'
            ], 500);
        }
    }

    /**
     * Verify OTP, find/create user, and return Sanctum Token.
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'mobile' => 'required|string',
            // 💡 طول کد را به 5 رقم تغییر دادیم چون کد تولید شده ما 5 رقمی است
            'code' => 'required|numeric|digits:5', 
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