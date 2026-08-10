<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

    public function showRegister()
    {
        return view('owner.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            // 'username' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:255',
                Rule::unique('owners', 'username'),
            ],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $owner = Owner::create([
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::guard('owner')->login($owner);

        $request->session()->regenerate();

        return redirect()->route('owner.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('owner')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('owner.login');
    }

    
}