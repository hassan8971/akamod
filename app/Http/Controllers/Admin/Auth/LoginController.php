<?php

// Move this controller to its own "Admin" namespace
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller; // <-- Make sure to use this
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse; // <-- Add this use statement
use Illuminate\Support\Facades\Auth; // <-- Add this use statement

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // We'll create this view next
        return view('admin.auth.login');
    }

    /**
     * Handle an incoming admin authentication request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Note: We are specifying the 'admin' guard
        // You will need to create this guard in config/auth.php
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect to a protected admin dashboard (e.g., /admin/dashboard)
            // You will need to create this route and controller
            
            // Make sure this route exists
            // return redirect()->intended(route('admin.dashboard')); 
            
            // For now, let's just redirect to a simple path
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the admin out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}


