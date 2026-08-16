<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        return view('customer.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        return back()
            ->withErrors(['email' => 'Email atau Password Salah!'])
            ->onlyInput('email');
    }

    public function showRegister()
    {
        return view('customer.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:64'],
            'username' => [
                'required', 'string', 'max:15',
                Rule::unique('customers', 'username'),
            ],
            'email' => [
                'required', 'string', 'max:255',
                Rule::unique('customers', 'email'),
            ],
            'no_telp' => [
                'required', 'string', 'regex:/^[0-9\-]{9,12}$/', 'min:8', 'max:12',
                Rule::unique('customers', 'no_telp'),
            ],
            'password' => ['required', 'string', 'min:8'],
        ],
            [
                'nama.required' => 'Masukkan Nama!',
                'nama.max' => 'Panjang Nama Maksimal 64 Karakter!',
                'username.required' => 'Masukkan Alamat Username!',
                'username.max' => 'Panjang Username Maksimal 15 Karakter!',
                'username.unique' => 'Username '.$request->input('username').' Sudah Dipakai!',
                'email.required' => 'Masukkan Alamat E-Mail!',
                'email.unique' => 'E-Mail Sudah Dipakai!',
                'no_telp.required' => 'Masukkan Aalamat No. Telepon!',
                'no_telp.unique' => 'No. Telepon Sudah Dipakai!',
                'no_telp.min' => 'No. Telepon Minimal 8 Karakter!',
                'no_telp.max' => 'No. Telepon Melebihi 12 Karakter!',
                'no_telp.regex' => 'No. Telepon Hanya Menerima Angka!',
                'password.required' => 'Masukkan Password!',
                'password.min' => 'Panjang Password Minimal 8 Karakter!',
            ]);

        $customer = Customer::create([
            'nama' => $data['nama'],
            'username' => $data['username'],
            'email' => $data['email'],
            'no_telp' => $data['no_telp'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::guard('customer')->login($customer);

        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function editProfile()
    {
        return view('customer.profile.edit', [
            'customer' => Auth::guard('customer')->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        /** @var Customer $customer */
        $customer = Auth::guard('pegawai')->user();

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_telp' => [
                'required', 'string', 'max:255',
                Rule::unique('customers', 'no_telp')->ignore($customer->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $customer->nama = $data['nama'];
        $customer->no_telp = $data['no_telp'];

        if (! empty($data['password'])) {
            $customer->password = Hash::make($data['password']);
        }

        $customer->save();

        return back()->with('status', 'Profile updated successfully.');
    }
}
