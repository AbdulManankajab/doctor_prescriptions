<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PharmacyAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('pharmacy')->check()) {
            return redirect()->route('pharmacy.dashboard');
        }
        return view('pharmacy.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('pharmacy')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('pharmacy.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('pharmacy')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('pharmacy.login');
    }
}
