<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    public function editProfile()
    {
        return view('pegawai.profile.edit', [
            'pegawai' => Auth::guard('pegawai')->user(),
        ]);
    }

    public function updateNama(Request $request)
    {
        /** @var Pegawai $pegawai */
        $pegawai = Auth::guard('pegawai')->user();
 
        $data = $request->validate([
            'nama_pegawai' => [
                'string', 'max:255', 'required',
            ],
        ]);
 
        $pegawai->nama_pegawai = $data['nama_pegawai'];
 
        $pegawai->save();
 
        return back()->with('nstatus', 'Nama Berhasil Diubah!');
    }

    public function updateUsername(Request $request)
    {
        /** @var Pegawai $pegawai */
        $pegawai = Auth::guard('pegawai')->user();

        $data = $request->validate([
            'username_pegawai' => [
                'string', 'max:255', 'required', 'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('pegawais', 'username_pegawai')->ignore($pegawai->id),
            ],
        ],
        [
            'username_pegawai.unique' => 'Username ' . $request->input('username_pegawai') . ' Sudah Dipakai!', 
            'username_pegawai.regex' => 'Hanya huruf, angka, garis bawah (_), dan tanda hubung (-) yang diperbolehkan untuk Username.', 
        ]);
 
        $pegawai->username_pegawai = $data['username_pegawai'];
 
        $pegawai->save();
 
        return back()->with('ustatus', 'Username Berhasil Diubah!');
    }

    public function updatePassword(Request $request)
    {
        /** @var Pegawai $pegawai */
        $pegawai = Auth::guard('pegawai')->user();
 
        $data = $request->validate([
            // Only required if they're actually trying to set a new password.
            'current_password' => ['required_with:password', 'string'],
            'password' => ['string', 'min:8', 'required'],
        ], [
            'current_password.required_with' => 'Masukan password sekarang untuk merubah password!',
        ]);
 
        // If they filled in a new password, verify the old one matches
        // what's actually stored before touching anything.
        if (! empty($data['password'])) {
            if (! Hash::check($data['current_password'], $pegawai->password)) {
                return back()
                    ->withErrors(['current_password' => 'Password Salah!']);
            }
            if (Hash::check($data['password'], $pegawai->password)) {
                return back()
                    ->withErrors(['password' => 'Password baru harus berbeda dengan password sekarang!']);
            }

        }
 
        if (! empty($data['password'])) {
            $pegawai->password = Hash::make($data['password']);
        }
 
        $pegawai->save();
 
        return back()->with('pstatus', 'Password Berhasil Diubah!');
    }
}