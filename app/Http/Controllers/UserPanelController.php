<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule; // <-- این خط را برای اعتبارسنجی ایمیل اضافه کنید
use App\Models\Address;

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

        return view('user.show', compact('user', 'order'));
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

    public function addressesIndex()
    {
        $addresses = Auth::user()->addresses;
        return view('user.addresses.index', compact('addresses'));
    }

    public function addressesCreate()
    {
        $address = new Address(); // یک آدرس خالی برای فرم
        return view('user.addresses.create', compact('address'));
    }

    public function addressesStore(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'country' => 'required|string|max:100',
        ]);

        Auth::user()->addresses()->create($validated);

        return redirect()->route('user.addresses.index')->with('success', 'آدرس جدید با موفقیت اضافه شد.');
    }

    public function addressesEdit(Address $address)
    {
        // Security check: Make sure this address belongs to the logged-in user
        if ($address->user_id !== Auth::id()) {
            abort(403, 'شما اجازه‌ی دسترسی به این آدرس را ندارید.');
        }

        return view('user.addresses.edit', compact('address'));
    }

    public function addressesUpdate(Request $request, Address $address)
    {
        // Security check
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'country' => 'required|string|max:100',
        ]);

        $address->update($validated);

        return redirect()->route('user.addresses.index')->with('success', 'آدرس با موفقیت به‌روزرسانی شد.');
    }

    public function addressesDestroy(Address $address)
    {
        // Security check
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        // TODO: Check if address is used in any non-completed orders?
        // For now, we just delete it.
        $address->delete();

        return redirect()->route('user.addresses.index')->with('success', 'آدرس با موفقیت حذف شد.');
    }
}