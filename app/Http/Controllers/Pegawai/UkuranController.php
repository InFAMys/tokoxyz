<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Ukuran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UkuranController extends Controller
{
    public function listUkuran($id) {
        
        $stokuk = Barang::with('ukurans')
            ->whereHas('ukurans')
            ->find($id);

        $stokbrg = Barang::find($id);

        if (!$stokuk) {
            // return response()->json(['message' => 'Barang not found'], 404);
            $stok=null;
        } else {
            $ukuran = Ukuran::where('id_barang', $id)->get();
            $stok=$ukuran;
        }

        return view('pegawai.kelola.k_ukuran', compact('stok', 'stokbrg'));
    }
    
    public function tambahUkuran($id) {
        
        $stokbrg = Barang::find($id);

        return view('pegawai.kelola.tambah.tambahUkuran', compact('stokbrg'));
        
    }

    public function addUkuran(Request $request, $id) {
            
        $brg = Barang::find($id);

        $hadUkuran = $brg->ukurans()->exists();

        $data = $request->validate([
        'nama_ukuran' => ['required', 'string', 'max:10'],
        'ukuran' => ['required', 'string', 'min:1'],
        ],
        [   
            'nama_ukuran.required' => 'Masukkan Nama Ukuran!',
            'nama_ukuran.max' => 'Panjang Nama Ukuran Maksimal 10 Karakter!',
            'ukuran.required' => 'Masukkan Ukuran!',
            'ukuran.min' => 'Panjang Nama Ukuran Minimal 1 Karakter!',
        ]);
        
        $kategori = Ukuran::create([
            'id_barang' => $brg->id_barang,
            'nama_ukuran' => $data['nama_ukuran'],
            'ukuran' => $data['ukuran'],
        ]);

        if (! $hadUkuran) {
            $brg->stok = 0;
            $brg->save();
        }

        // return redirect()->route('owner.kpegawai');
        return back()->with('astatus', 'Ukuran Berhasil Ditambahkan!');
        
    }

    public function editUkuran($id_b, $id_u) {
        $ukuran=Ukuran::where('id_ukuran', $id_u)->first();    
        
        return view('pegawai.kelola.edit.editUkuran', compact('ukuran'));
    }

    public function updateUkuran(Request $request, $id_b, $id_u)
    {
        $ukuran = Ukuran::where('id_ukuran', $id_u)->first();

        $data = $request->validate([
        'nama_ukuran' => ['required', 'string', 'max:10'],
        'ukuran' => ['required', 'string', 'min:1'],
        ],
        [   
            'nama_ukuran.required' => 'Masukkan Nama Ukuran!',
            'nama_ukuran.max' => 'Panjang Nama Ukuran Maksimal 10 Karakter!',
            'ukuran.required' => 'Masukkan Ukuran!',
            'ukuran.min' => 'Panjang Nama Ukuran Minimal 1 Karakter!',
        ]);
        
        $ukuran->nama_ukuran = $data['nama_ukuran'];
        $ukuran->ukuran = $data['ukuran'];
        $ukuran->update();

        return back()->with('estatus', 'Ukuran Berhasil Di Edit!');
    }

    public function updateHargaUkuran(Request $request, $id_b, $id_u)
    {
        $ukuran = Ukuran::where('id_ukuran', $id_u)->first();

        $data = $request->validate([
            'harga_ukuran' => ['required', 'numeric', 'min:0'],
        ],
        [
            'harga_ukuran.required' => 'Masukkan Harga!',
            'harga_ukuran.numeric' => 'Harga harus angka!',
            'harga_ukuran.min' => 'Harga tidak boleh negatif!',
        ]);

        $ukuran->harga_ukuran = $data['harga_ukuran'];
        $ukuran->update();

        return back()->with('ehargastatus-' . $id_u, 'Harga Ukuran Berhasil Di Update!');
    }

    public function deleteUkuran($id) {
        
        Ukuran::where('id_ukuran', $id)->first()->delete(); 
        
        ////// Pegawai::where('id', $id)->forceDelete();        // Delete Permanently

        return back()->with('delStatus', 'Kategori Berhasil Di Hapus!');
    }
    
}