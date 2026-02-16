<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceptionAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('reception')->check()) {
            return redirect()->route('reception.dashboard');
        }
        return view('reception.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('reception')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('reception.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('reception')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('reception.login');
    }
}
