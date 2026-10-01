<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Varian;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function stokBarang($id)
    {
        $stokcek = Barang::with('ukurans')
            ->whereHas('ukurans')
            ->find($id);

        $stokuk = Varian::where('id_barang', $id)->get();

        $stokbrg = Barang::where('id_barang', $id)->first();

        if (! $stokcek) {
            $stok = $stokbrg->stok;

            return view('pegawai.kelola.k_stok', compact('stok', 'stokbrg', 'stokcek'));

        } else {
            $stok = $stokuk;

            return view('pegawai.kelola.k_stokukuran', compact('stok', 'stokbrg', 'stokcek'));
        }
        // return view('pegawai.kelola.k_stok', compact('stok', 'stokbrg', 'stokcek'));

    }

    public function updateStok(Request $request, $id)
    {
        // $ukuran = Varian::where('id_ukuran', $id_u)->first();
        $stok = Barang::where('id_barang', $id)->first();

        $data = $request->validate([
            'stok' => ['required', 'integer', 'min:0'],
        ],
            [
                'stok.required' => 'Masukkan Stok!',
                'stok.integer' => 'Stok Harus Angka!',
                'stok.min' => 'Stok Tidak Boleh Kurang Dari 0!',
            ]);

        $stok->stok = $data['stok'];
        $stok->update();

        return back()->with('estatus', 'Stok Berhasil Di Ubah!');
    }

    public function updateStokUkuran(Request $request, $id_b, $id_u)
    {
        // $ukuran = Varian::where('id_ukuran', $id_u)->first();
        $stok = Varian::where('id_ukuran', $id_u)->first();

        $data = $request->validate([
            'stok' => ['required', 'integer', 'min:0'],
        ],
            [
                'stok.required' => 'Masukkan Stok!',
                'stok.integer' => 'Stok Harus Angka!',
                'stok.min' => 'Stok Tidak Boleh Kurang Dari 0!',
            ]);

        $stok->stok_ukuran = $data['stok'];
        $stok->update();

        return back()->with('estatus-'.$id_u, 'Stok Berhasil Di Ubah!');
    }
}
