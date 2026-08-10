<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class KategoriController extends Controller
{
    public function listKategoris(Request $request) {
        $q = trim($request->query('q', ''));

        $kategori = Kategori::query()
            ->when($q, fn ($query) => $query->where('nama_kategori', 'like', "%$q%"))
            ->get();

        if ($request->ajax()) {
            return view('pegawai.kelola._kategori_rows', compact('kategori'))->render();
        }

        return view('pegawai.kelola.k_kategori', compact('kategori'));
    }

    public function tambahKategori() {
    
        return view('pegawai.kelola.tambah.tambahKategori');
    }

    public function addKategori(Request $request) {
            
        $data = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:16'],
        ],
        [   
            'nama_kategori.required' => 'Masukkan Nama Kategori!',
            'nama_kategori.max' => 'Panjang Nama Maksimal 16 Karakter!',
        ]);
        
        $kategori = Kategori::create([
            'nama_kategori' => $data['nama_kategori'],
        ]);

        // return redirect()->route('owner.kpegawai');
        return back()->with('astatus', 'Kategori Berhasil Ditambahkan!');
        
    }
    
    public function editKategori($id) {
        $kategori=Kategori::where('id_kategori', $id)->first();    
        
        return view('pegawai.kelola.edit.editKategori', compact('kategori'));
    }

    public function updateKategori(Request $request, $id)
    {
        $kategori = Kategori::where('id_kategori', $id)->first();

        $data = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:16'],
        ],
        [   
            'nama_kategori.required' => 'Masukkan Nama Kategori!',
            'nama_kategori.max' => 'Panjang Nama Maksimal 16 Karakter!',
        ]);
    
        $kategori->nama_kategori = $data['nama_kategori'];
        $kategori->update();

        return back()->with('estatus', 'Kategori Berhasil Di Edit!');
    }

    public function deleteKategori($id) 
    {
        Kategori::where('id_kategori', $id)->first()->delete();
        
        ////// Pegawai::where('id', $id)->forceDelete();        // Delete Permanently

        return back()->with('delStatus', 'Kategori Berhasil Di Hapus!');
    }
}