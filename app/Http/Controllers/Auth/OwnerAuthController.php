<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerAuthController extends Controller
{
    public function showLogin()
    {
        return view('owner.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ], [
            'username.required' => 'Masukkan Username!',
            'password.required' => 'Masukkan Password!',
        ]);

        if (Auth::guard('owner')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('owner.dashboard'));
        }

        return back()
            ->withErrors(['username' => 'Username atau Password Salah!'])
            ->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::guard('owner')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('owner.login');
    }
}
