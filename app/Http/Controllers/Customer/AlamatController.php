<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlamatController extends Controller
{
    public function index()
    {
        $alamat = Auth::guard('customer')->user()->alamats()->get();

        return view('customer.alamat.index', compact('alamat'));
    }

    public function create()
    {
        return view('customer.alamat.form');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        Auth::guard('customer')->user()->alamats()->create($data);

        return redirect()->route('alamat.index')->with('status', 'Alamat Berhasil Ditambahkan!');
    }

    public function edit($id)
    {
        $alamat = $this->findOwned($id);

        return view('customer.alamat.form', compact('alamat'));
    }

    public function update(Request $request, $id)
    {
        $alamat = $this->findOwned($id);

        $alamat->update($this->validateData($request));

        return redirect()->route('alamat.index')->with('status', 'Alamat Berhasil Diubah!');
    }

    public function destroy($id)
    {
        $this->findOwned($id)->delete();

        return redirect()->route('alamat.index')->with('status', 'Alamat Berhasil Dihapus!');
    }

    private function findOwned($id)
    {
        return Auth::guard('customer')->user()->alamats()->findOrFail($id);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'nama_alamat' => ['required', 'string', 'max:50'],
            'nama_penerima' => ['required', 'string', 'max:64'],
            'telp_penerima' => ['required', 'regex:/^[0-9\-]{9,12}$/', 'min:8', 'max:12'],
            'detail_alamat' => ['required', 'string'],
            'kecamatan' => ['required', 'string', 'max:64'],
            'kelurahan' => ['required', 'string', 'max:64'],
            'kota' => ['required', 'string', 'max:64'],
            'provinsi' => ['required', 'string', 'max:64'],
            'kode_pos' => ['required', 'string', 'max:10'],
        ], [
            'nama_alamat.required' => 'Masukkan Label Alamat!',
            'nama_penerima.required' => 'Masukkan Nama Penerima!',
            'telp_penerima.required' => 'Masukkan No. Telepon Penerima!',
            'telp_penerima.regex' => 'No. Telepon Hanya Menerima Angka!',
            'telp_penerima.min' => 'No. Telepon Minimal 8 Karakter!',
            'telp_penerima.max' => 'No. Telepon Melebihi 12 Karakter!',
            'detail_alamat.required' => 'Masukkan Detail Alamat!',
            'kecamatan.required' => 'Masukkan Kecamatan!',
            'kelurahan.required' => 'Masukkan Kelurahan!',
            'kota.required' => 'Masukkan Kota!',
            'provinsi.required' => 'Masukkan Provinsi!',
            'kode_pos.required' => 'Masukkan Kode Pos!',
        ]);
    }
}