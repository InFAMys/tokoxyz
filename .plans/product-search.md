# Fitur Pencarian Produk (Customer)

## Ringkasan
Tambahkan search bar di halaman home (`welcome.blade.php`) dan hasil pencarian di halaman khusus. Pencarian mencocokkan nama barang, kode barang, nama brand, dan nama kategori. Hanya produk berstatus `Ditampilkan` yang tampil.

## Keputusan Pengguna
- Dedicated results page (bukan filter di halaman yang sama).
- Pencarian match: `nama_barang`, `kode_barang`, `nama_brand`, `nama_kategori`.

## Perubahan

### 1. Route baru — `routes/web.php:24`
Tambahkan sibling dari route `barang.detail` (ada di area public).

```php
Route::get('cari', [CustomerController::class, 'cari'])->name('barang.search');
```

### 2. Method controller — `app/Http/Controllers/Customer/CustomerController.php`
Tambahkan method `cari()` setelah `home()`.

```php
public function cari(Request $request)
{
    $q = trim((string) $request->query('q'));

    $barang = Barang::with(['brand', 'kategori'])
        ->where('status', 'Ditampilkan')
        ->when($q !== '', function ($query) use ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('nama_barang', 'like', "%{$q}%")
                  ->orWhere('kode_barang', 'like', "%{$q}%")
                  ->orWhereHas('brand', fn ($b) => $b->where('nama_brand', 'like', "%{$q}%"))
                  ->orWhereHas('kategori', fn ($k) => $k->where('nama_kategori', 'like', "%{$q}%"));
            });
        })
        ->orderByDesc('id_barang')
        ->get();

    return view('customer.barang.search', compact('barang', 'q'));
}
```

Catatan relasi:
- `barangs.nama_barang`, `barangs.kode_barang`
- `brands.nama_brand`
- `kategoris.nama_kategori`

### 3. View baru — `resources/views/customer/barang/search.blade.php`
- Layout: `@extends('customer.layouts.app')`, title `Hasil Pencarian`.
- Header + GET form `action="{{ route('barang.search') }}"`, input `name="q"` value `{{ $q }}`, tombol submit.
- Grid hasil: reuse markup `product-card` dari `welcome.blade.php:32-45`, tiap item `<a href="{{ route('barang.detail', $b->id_barang) }}">`.
- Empty state: "Tidak ada produk ditemukan" saat `$barang->isEmpty()`.

### 4. Search bar di home — `resources/views/welcome.blade.php`
Tambahkan GET form di area hero banner (baris 12-26).

```html
<form method="GET" action="{{ route('barang.search') }}" class="d-flex gap-2" role="search">
    <input class="form-control" type="search" name="q" placeholder="Cari produk..." aria-label="Cari produk">
    <button class="btn" type="submit"
        style="background:#fff;color:var(--pink-600);font-weight:700;border-radius:25px">Cari</button>
</form>
```

## Verifikasi
- `php artisan route:list -n` — cek route `barang.search` ada.
- `vendor/bin/pint --dirty --format agent`.

## Catatan
- `orderByDesc('id_barang')` dipakai untuk recency. Pagination hanya kalau katalog besar.
- `q` kosong → tampilkan semua produk berstatus `Ditampilkan` (semantics halaman hasil pencarian).
