<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule; // <-- این خط را برای اعتبارسنجی ایمیل اضافه کنید

class UserPanelController extends Controller
{
    /**
     * Show the main user dashboard / order history.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Load orders, newest first
        $orders = $user->orders()
                       ->with('items') // Load items for summary
                       ->orderBy('created_at', 'desc')
                       ->paginate(10); // Paginate the results

        return view('user.index', compact('user', 'orders'));
    }

    /**
     * Show the page to view a single order.
     */
    public function showOrder($orderId)
    {
        $user = Auth::user();
        
        // Find the order, but only if it belongs to the current user
        $order = $user->orders()
                     ->with('items.productVariant', 'address')
                     ->findOrFail($orderId); // findOrFail ensures it's their order

        return view('user.order-show', compact('user', 'order'));
    }

    /**
     * --- جدید ---
     * Show the profile management page.
     * (صفحه مدیریت پروفایل را نشان می‌دهد)
     */
    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    /**
     * --- جدید ---
     * Update the user's profile information.
     * (اطلاعات پروفایل کاربر را به‌روزرسانی می‌کند)
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'nullable', // ایمیل می‌تواند خالی باشد
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id), // باید یکتا باشد، به جز برای خود کاربر
            ],
        ]);

        // فقط فیلدهای اعتبارسنجی شده را آپدیت می‌کنیم
        // (شماره موبایل آپدیت نمی‌شود)
        $user->update($validated);

        return redirect()->route('user.profile')->with('success', 'پروفایل شما با موفقیت به‌روزرسانی شد.');
    }

    /**
     * --- جدید ---
     * Update the user's password.
     * (رمز عبور کاربر را تغییر می‌دهد)
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'], // بررسی رمز عبور فعلی
            'password' => ['required', 'confirmed', Password::min(8)], // رمز جدید با تکرار
        ], [
            'current_password.current_password' => 'رمز عبور فعلی صحیح نمی‌باشد.',
            'password.confirmed' => 'رمز عبور جدید و تکرار آن مطابقت ندارند.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('user.profile')->with('success', 'رمز عبور شما با موفقیت تغییر کرد.');
    }
}