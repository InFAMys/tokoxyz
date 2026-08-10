<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class OwnerController extends Controller
{
  public function editProfile()
    {
        return view('owner.profile.edit', [
            'owner' => Auth::guard('owner')->user(),
        ]);
    }

    public function updateUsername(Request $request)
    {
        /** @var Owner $owner */
        $owner = Auth::guard('owner')->user();
 
        $data = $request->validate([
            'username' => [
                'string', 'max:255', 'required', 'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('owners', 'username')->ignore($owner->id),
            ],
        ],
        [
            'username.regex' => 'Hanya huruf, angka, garis bawah (_), dan tanda hubung (-) yang diperbolehkan untuk Username.', 
        ]);
 
        $owner->username = $data['username'];
 
        $owner->save();
 
        return back()->with('ustatus', 'Username Berhasil Diubah!');
    }

    public function updatePassword(Request $request)
    {
        /** @var Owner $owner */
        $owner = Auth::guard('owner')->user();
 
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
            if (! Hash::check($data['current_password'], $owner->password)) {
                return back()
                    ->withErrors(['current_password' => 'Password Salah!']);
            }
            if (Hash::check($data['password'], $owner->password)) {
                return back()
                    ->withErrors(['password' => 'Password baru harus berbeda dengan password sekarang!']);
            }

        }
 
        if (! empty($data['password'])) {
            $owner->password = Hash::make($data['password']);
        }
 
        $owner->save();
 
        return back()->with('pstatus', 'Password Berhasil Diubah!');
    }  
}