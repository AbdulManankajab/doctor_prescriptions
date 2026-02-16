<?php

namespace App\Http\Controllers\Laboratory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaboratoryAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('laboratory')->check()) {
            return redirect()->route('laboratory.dashboard');
        }
        return view('laboratory.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('laboratory')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('laboratory.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('laboratory')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('laboratory.login');
    }
}
