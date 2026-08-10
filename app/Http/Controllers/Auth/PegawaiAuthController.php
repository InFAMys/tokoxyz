<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PegawaiAuthController extends Controller
{
    public function showLogin()
    {
        return view('pegawai.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username_pegawai' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::guard('pegawai')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('pegawai.dashboard'));
        }

        return back()
            ->withErrors(['username_pegawai' => 'Username atau Password Salah!'])
            ->onlyInput('username_pegawai');
    }

    public function logout(Request $request)
    {
        Auth::guard('pegawai')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('pegawai.login');
    }


}