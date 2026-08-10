<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function listBrands(Request $request) {
        $q = trim($request->query('q', ''));

        $brn = Brand::query()
            ->when($q, fn ($query) => $query->where('nama_brand', 'like', "%$q%"))
            ->get();

        if ($request->ajax()) {
            return view('pegawai.kelola._brand_rows', compact('brn'))->render();
        }

        return view('pegawai.kelola.k_brand', compact('brn'));
    }

    public function tambahBrand() {
    
        return view('pegawai.kelola.tambah.tambahBrand');
    }

    public function addBrand(Request $request) {
            
        $data = $request->validate([
            'nama_brand' => ['required', 'string', 'max:24'],
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max: 5120'],
        ],
        [   
            'nama_brand.required' => 'Masukkan Nama Brand!',
            'nama_brand.max' => 'Panjang Nama Maksimal 24 Karakter!',
            'logo.required' => 'Upload Logo!',
            'logo.mimes' => 'Ekstensi Logo yang diperbolehkan: JPG, PNG, JPEG!', 
            'logo.max' => 'Logo Maksimal 5 MB!',
        ]);

        $path=$data['logo']->store('brands','public');
        
        $brand = Brand::create([
            'nama_brand' => $data['nama_brand'],
            'logo' => $path,
        ]);

        // return redirect()->route('owner.kpegawai');
        return back()->with('astatus', 'Brand Berhasil Ditambahkan!');
        
    }
    
    public function editBrand($id) {
        $brand=Brand::where('id_brand', $id)->first();    
        
        return view('pegawai.kelola.edit.editBrand', compact('brand'));
    }

    public function updateBrand(Request $request, $id)
    {
        $brand = Brand::where('id_brand', $id)->firstOrFail();
        $data = $request->validate([
            'nama_brand' => ['required', 'string', 'max:24'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'nama_brand.required' => 'Masukkan Nama Brand!',
            'nama_brand.max' => 'Panjang Nama Maksimal 24 Karakter!',
            'logo.mimes' => 'Ekstensi Logo yang diperbolehkan: JPG, PNG, JPEG!',
            'logo.max' => 'Logo Maksimal 5 MB!',
        ]);

        if ($request->hasFile('logo')) {
            $oldLogo = $brand->logo;
            $data['logo'] = $data['logo']->store('brands', 'public');

            $brand->update($data);

            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            return back()->with('estatus', 'Brand Berhasil Di Edit!');
        }

        $brand->update($data);

        return back()->with('estatus', 'Brand Berhasil Di Edit!');
    }

    public function deleteBrand($id) 
    {
        $brand = Brand::where('id_brand', $id)->first();
        
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }
        $brand->logo = '';
        $brand->update();
        $brand->delete();
        
        ////// Pegawai::where('id', $id)->forceDelete();        // Delete Permanently

        return back()->with('delStatus', 'Brand Berhasil Di Hapus!');
    }
}
