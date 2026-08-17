<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Brand;
use App\Models\Kategori;
use App\Models\Ukuran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BarangController extends Controller
{
    public function listBarang(Request $request)
    {
        $q = trim($request->query('q', ''));

        $filter = fn ($query) => $query
            ->where('nama_barang', 'like', "%$q%")
            ->orWhere('kode_barang', 'like', "%$q%")
            ->orWhereHas('brand', fn ($b) => $b->where('nama_brand', 'like', "%$q%"))
            ->orWhereHas('kategori', fn ($k) => $k->where('nama_kategori', 'like', "%$q%"));

        $barang = Barang::query()
            ->with('ukurans')
            ->when($q, $filter)
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('pegawai.kelola._barang_rows', compact('barang'))->render();
        }

        return view('pegawai.kelola.k_barang', compact('barang'));
    }

    public function detailBarang(int $id)
    {
        $barang = Barang::with(['brand', 'kategori'])
            ->where('id_barang', $id)
            ->firstOrFail();

        $stokbrg = Barang::with('ukurans')
            ->whereHas('ukurans')
            ->find($id);

        if (! $stokbrg) {
            $stok = null;
        } else {
            $ukuran = Ukuran::where('id_barang', $id)->get();
            $stok = $ukuran;
        }

        return view('pegawai.kelola.detail.detailBarang', compact('barang', 'stok'));
    }

    public function tambahBarang()
    {
        $brands = Brand::orderBy('nama_brand')->get(['id_brand', 'nama_brand']);
        $kategoris = Kategori::orderBy('nama_kategori')->get(['id_kategori', 'nama_kategori']);

        return view('pegawai.kelola.tambah.tambahBarang', compact('brands', 'kategoris'));
    }

    public function addBarang(Request $request)
    {
        $this->normalizeBerat($request);

        $data = $request->validate([
            'id_brand' => ['required', 'exists:brands,id_brand'],
            'id_kategori' => ['required', 'exists:kategoris,id_kategori'],
            'kode_barang' => ['required', 'string', 'max:15', 'regex:/^[A-Za-z0-9_-]+$/'],
            'nama_barang' => ['required', 'string', 'max:32'],
            'deskripsi' => ['required', 'string'],
            'thumbnail' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'foto' => ['required', 'array', 'max:5'],
            'foto.*' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'harga' => ['required', 'numeric', 'min:1'],
            'berat' => ['required', 'numeric', 'min:0.001'],
        ],
            [
                'id_brand.required' => 'Brand Harus Diisi!',
                'id_kategori.required' => 'Kategori Harus Diisi!',
                'id_brand.exists' => 'Brand Tidak Valid!',
                'id_kategori.exists' => 'Kategori Tidak Valid!',
                'kode_barang.required' => 'Kode Barang Harus Diisi!',
                'kode_barang.max' => 'Kode Barang Maksimal 15 Karakter!',
                'kode_barang.regex' => 'Hanya huruf, angka, garis bawah (_), dan tanda hubung (-) yang diperbolehkan untuk Kode Barang.',
                'nama_barang.required' => 'Nama Barang Harus Diisi!',
                'nama_barang.max' => 'Nama Barang Maksimal 32 Karakter!',
                'deskripsi.required' => 'Deskripsi Harus Diisi!',
                'thumbnail.required' => 'Thumbnail Harus Diisi!',
                'thumbnail.image' => 'Thumbnail Harus Berupa Foto!',
                'thumbnail.mimes' => 'Thumbnail harus berformat JPG, JPEG, atau PNG!',
                'thumbnail.max' => 'Ukuran Thumbnail Maksimal 5 MB!',
                'foto.required' => 'Foto Harus Diisi!',
                'foto.array' => 'Format Foto Tidak Valid!',
                'foto.max' => 'Maksimal 5 Foto!',
                'foto.*.required' => 'Foto Harus Diisi!',
                'foto.*.image' => 'File Harus Berupa Foto!',
                'foto.*.mimes' => 'Foto harus berformat JPG, JPEG, atau PNG!',
                'foto.*.max' => 'Ukuran Foto Maksimal 5 MB!',
                'harga.required' => 'Harga Harus Diisi!',
                'harga.min' => 'Harga Minimal 1 Karakter!',
                'berat.numeric' => 'Berat harus berupa angka kilogram!',
                'berat.min' => 'Berat minimal 0,001 kg!',
            ]
        );

        $data['thumbnail'] = $data['thumbnail']->store($this->thumbnailDirectory($data['kode_barang']), 'public');
        $data['foto'] = array_map(
            fn ($foto) => $foto->store($this->photoDirectory($data['kode_barang']), 'public'),
            $data['foto'],
        );

        Barang::create($data);

        return redirect()->route('pegawai.barang')->with('astatus', 'Barang berhasil ditambahkan!');
    }

    public function editBarang(int $id)
    {
        $barang = Barang::where('id_barang', $id)->firstOrFail();
        $brands = Brand::orderBy('nama_brand')->get(['id_brand', 'nama_brand']);
        $kategoris = Kategori::orderBy('nama_kategori')->get(['id_kategori', 'nama_kategori']);

        return view('pegawai.kelola.edit.editBarang', compact('barang', 'brands', 'kategoris'));
    }

    public function updateBarang(Request $request, int $id)
    {
        $barang = Barang::where('id_barang', $id)->firstOrFail();

        $this->normalizeBerat($request);

        $data = $request->validate([
            'id_brand' => ['required', 'exists:brands,id_brand'],
            'id_kategori' => ['required', 'exists:kategoris,id_kategori'],
            'kode_barang' => ['required', 'string', 'max:15', 'regex:/^[A-Za-z0-9_-]+$/'],
            'nama_barang' => ['required', 'string', 'max:32'],
            'deskripsi' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'foto' => ['nullable', 'array', 'max:5'],
            'foto.*' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'hapus_foto' => ['nullable', 'array'],
            'hapus_foto.*' => ['required', 'string', Rule::in($barang->foto)],
            'harga' => ['required', 'numeric', 'min:1'],
            'berat' => ['required', 'numeric', 'min:0.001'],
            'status' => ['required', 'in:Ditampilkan,Disembunyikan'],
            'preorder' => ['required', 'in:Tersedia,Tidak Tersedia'],
            'estimasi_preorder' => ['nullable', 'required_if:preorder,Tersedia', 'integer', 'min:1'],
        ], [
            'id_brand.required' => 'Brand Harus Diisi!',
            'id_kategori.required' => 'Kategori Harus Diisi!',
            'id_brand.exists' => 'Brand Tidak Valid!',
            'id_kategori.exists' => 'Kategori Tidak Valid!',
            'kode_barang.required' => 'Kode Barang Harus Diisi!',
            'kode_barang.max' => 'Kode Barang Maksimal 15 Karakter!',
            'kode_barang.regex' => 'Hanya huruf, angka, garis bawah (_), dan tanda hubung (-) yang diperbolehkan untuk Kode Barang.',
            'nama_barang.required' => 'Nama Barang Harus Diisi!',
            'nama_barang.max' => 'Nama Barang Maksimal 32 Karakter!',
            'deskripsi.required' => 'Deskripsi Harus Diisi!',
            'thumbnail.image' => 'Thumbnail Harus Berupa Foto!',
            'thumbnail.mimes' => 'Thumbnail harus berformat JPG, JPEG, atau PNG!',
            'thumbnail.max' => 'Ukuran Thumbnail Maksimal 5 MB!',
            'foto.array' => 'Format Foto Tidak Valid!',
            'foto.max' => 'Maksimal 5 Foto!',
            'foto.*.image' => 'File Harus Berupa Foto!',
            'foto.*.mimes' => 'Foto harus berformat JPG, JPEG, atau PNG!',
            'foto.*.max' => 'Ukuran Foto Maksimal 5 MB!',
            'hapus_foto.*.in' => 'Foto yang dipilih tidak valid!',
            'harga.required' => 'Harga Harus Diisi!',
            'harga.min' => 'Harga Minimal 1 Karakter!',
            'berat.numeric' => 'Berat harus berupa angka kilogram!',
            'berat.min' => 'Berat minimal 0,001 kg!',
            'status.required' => 'Status Harus Diisi!',
            'status.in' => 'Status Tidak Valid!',
            'preorder.required' => 'Preorder Harus Diisi!',
            'preorder.in' => 'Status Preorder Tidak Valid!',
            'estimasi_preorder.required_if' => 'Estimasi Preorder Harus Diisi jika preorder tersedia!',
            'estimasi_preorder.integer' => 'Estimasi Preorder harus berupa angka!',
            'estimasi_preorder.min' => 'Estimasi Preorder minimal 1 hari!',
        ]);

        $photosToRemove = $data['hapus_foto'] ?? [];
        $remainingPhotos = array_values(array_diff($barang->foto, $photosToRemove));
        $newPhotos = $data['foto'] ?? [];
        $newThumbnail = $data['thumbnail'] ?? null;

        if (count($remainingPhotos) + count($newPhotos) > 5) {
            throw ValidationException::withMessages([
                'foto' => 'Jumlah foto barang maksimal 5.',
            ]);
        }

        if (count($remainingPhotos) + count($newPhotos) === 0) {
            throw ValidationException::withMessages([
                'hapus_foto' => 'Barang harus memiliki minimal satu foto.',
            ]);
        }

        $uploadedPhotos = array_map(
            fn ($foto) => $foto->store($this->photoDirectory($data['kode_barang']), 'public'),
            $newPhotos,
        );
        $finalPhotos = array_merge($remainingPhotos, $uploadedPhotos);
        $thumbnailToDelete = null;

        unset($data['foto'], $data['hapus_foto'], $data['thumbnail']);

        if ($newThumbnail) {
            $data['thumbnail'] = $newThumbnail->store($this->thumbnailDirectory($data['kode_barang']), 'public');

            if ($barang->thumbnail && ! in_array($barang->thumbnail, $finalPhotos, true)) {
                $thumbnailToDelete = $barang->thumbnail;
            }
        } elseif (! $barang->thumbnail || in_array($barang->thumbnail, $photosToRemove, true)) {
            $data['thumbnail'] = $finalPhotos[0] ?? null;
        }

        $data['foto'] = $finalPhotos;
        $data['estimasi_preorder'] = $data['preorder'] === 'Tersedia'
            ? $data['estimasi_preorder']
            : null;

        $barang->update($data);
        Storage::disk('public')->delete(array_filter(array_merge($photosToRemove, [$thumbnailToDelete])));

        return redirect()
            ->route('pegawai.detailbarang', $barang->id_barang)
            ->with('estatus', 'Barang berhasil diperbarui!');
    }

    private function photoDirectory(string $kodeBarang): string
    {
        return 'barangs/'.Str::slug($kodeBarang);
    }

    private function thumbnailDirectory(string $kodeBarang): string
    {
        return $this->photoDirectory($kodeBarang).'/thumbnails';
    }

    private function normalizeBerat(Request $request): void
    {
        if (! $request->filled('berat')) {
            return;
        }

        $request->merge([
            'berat' => Str::of($request->berat)->trim()->replace(',', '.')->toString(),
        ]);
    }

    public function deleteBarang(int $id)
    {
        $barang = Barang::where('id_barang', $id)->firstOrFail();
        $photos = $barang->foto;
        $thumbnail = $barang->thumbnail;

        $barang->delete();
        Storage::disk('public')->delete(array_unique(array_filter(array_merge($photos, [$thumbnail]))));

        return redirect()->route('pegawai.barang')->with('delStatus', 'Barang berhasil dihapus!');
    }
}
