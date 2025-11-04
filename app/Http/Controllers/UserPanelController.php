<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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
     * Show the profile management page.
     */
    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20|unique:users,mobile,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('user.profile')->with('success', 'پروفایل شما با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('user.profile')->with('success', 'رمز عبور شما با موفقیت تغییر کرد.');
    }
}
