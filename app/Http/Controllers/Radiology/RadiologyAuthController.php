<?php

namespace App\Http\Controllers\Radiology;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RadiologyAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('radiology')->check()) {
            return redirect()->route('radiology.dashboard');
        }
        return view('radiology.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('radiology')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('radiology.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('radiology')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('radiology.login');
    }
}
