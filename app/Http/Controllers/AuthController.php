<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $key = 'login:' . $request->ip();
        $attempts = app('cache')->get($key, 0);

        if ($attempts >= 5) {
            return back()->withErrors([
                'email' => 'Too many login attempts. Please try again later.',
            ])->onlyInput('email');
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            app('cache')->increment($key);
            app('cache')->put($key, app('cache')->get($key, 0), now()->addMinute());

            return back()->withErrors([
                'email' => 'Access denied. Invalid credentials.',
            ])->onlyInput('email');
        }

        app('cache')->forget($key);
        $request->session()->regenerate();

        return $this->redirectByRole(Auth::user());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    protected function redirectByRole($user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isCollegeStaff()) {
            return redirect()->route('college.dashboard');
        }

        if ($user->isOrgStaff()) {
            return redirect()->route('org.dashboard');
        }

        return redirect()->route('home');
    }
}
