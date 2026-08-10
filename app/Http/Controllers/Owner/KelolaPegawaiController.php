<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class KelolaPegawaiController extends Controller
{
    public function listAll(Request $request) {
        $q = trim($request->query('q', ''));

        $pgw = Pegawai::query()
            ->when($q, fn ($query) => $query
                ->where('nama_pegawai', 'like', "%$q%")
                ->orWhere('username_pegawai', 'like', "%$q%"))
            ->get();

        if ($request->ajax()) {
            return view('owner.kelola._pegawai_rows', compact('pgw'))->render();
        }

        return view('owner.kelola.k_pegawai', compact('pgw'));
    }

    public function formTambahPgw()
    {
        return view('owner.kelola.tambah.tambahPegawai');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nama_pegawai' => ['required', 'string', 'max:255'],
            'username_pegawai' => [
                'required', 'string', 'max:255', 'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('pegawais', 'username_pegawai'),
            ],
            'password' => ['required', 'string', 'min:8'],
        ],
        [   
            'nama_pegawai.required' => 'Masukkan Nama!',
            'username_pegawai.required' => 'Masukkan Alamat Username!',
            'username_pegawai.unique' => 'Username ' . $request->input('username_pegawai') . ' Sudah Dipakai!',
            'username_pegawai.regex' => 'Hanya huruf, angka, garis bawah (_), dan tanda hubung (-) yang diperbolehkan untuk Username.', 
            'password.required' => 'Masukkan Password!',
            'password.min' => 'Panjang Password Minimal 8 Karakter!',
        ]);

        $pegawai = Pegawai::create([
            'nama_pegawai' => $data['nama_pegawai'],
            'username_pegawai' => $data['username_pegawai'],
            'password' => Hash::make($data['password']),
        ]);

        // return redirect()->route('owner.kpegawai');
        return back()->with('regstatus', 'Akun Berhasil Ditambahkan!');
    }

    public function editPegawai($id) {
        $pgw=Pegawai::where('id_pegawai', $id)->first();    
        
        return view('owner.kelola.edit.editPegawai', compact('pgw'));
    }

    public function updatePegawai(Request $request, $id)
    {
        $pegawai = Pegawai::where('id_pegawai', $id)->first();
        
        if ($request->input('username_pegawai') == $pegawai->username_pegawai) {
            // $check='USER SAMA';
            $data = $request->validate([
            'nama_pegawai' => ['required', 'string', 'max:255'],
            'username_pegawai' => [
                'required', 'string', 'max:255', 'regex:/^[A-Za-z0-9_-]+$/',
            ],
            'password' => ['nullable', 'string', 'min:8'],
        ]);
        } else {
            // $check='USER BEDA';
            $data = $request->validate([
            'nama_pegawai' => ['required', 'string', 'max:255'],
            'username_pegawai' => [
                'required', 'string', 'max:255', 'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('pegawais', 'username_pegawai')->ignore($pegawai->id),
            ],
            'password' => ['nullable', 'string', 'min:8'],
        ],
        [
            'username_pegawai.regex' => 'Hanya huruf, angka, garis bawah (_), dan tanda hubung (-) yang diperbolehkan untuk Username.', 

        ]);
        }

        $pegawai->nama_pegawai = $data['nama_pegawai'];
        $pegawai->username_pegawai = $data['username_pegawai'];

        if (! empty($data['password'])) {
            $pegawai->password = Hash::make($data['password']);
        }

        $pegawai->update();

        return back()->with('status', 'Akun Pegawai Berhasil Di Edit!');
    }

    public function deletePegawai($id) 
    {
        Pegawai::where('id_pegawai', $id)->delete();
        
        ////// Pegawai::where('id', $id)->forceDelete();        // Delete Permanently

        return back()->with('delStatus', 'Akun Pegawai Berhasil Di Hapus!');
    }
}