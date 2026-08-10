# Plan: Ukuran-specific prices

Add per-ukuran price; form lives in the pegawai `Ukuran` menu; customer sees a price range.

## Current state (checked)
- `ukurans`: `id_ukuran`, `id_barang` FK, `nama_ukuran`, `ukuran`, `stok_ukuran`, timestamps, softDeletes. **No price column.**
- Only price is `barangs.harga` (decimal:2). `Ukuran::$fillable` = `id_barang, nama_ukuran, ukuran` (no `stok_ukuran`).
- Customer cart already picks `id_ukuran` per line (`customer/barang/detail.blade.php:123`); `Keranjang.ukuran` relation exists (`KeranjangController` eager-loads it at line 23).
- Cart price computed live, not snapshotted:
  - `KeranjangController.php:33` -> `(float) $item->barang->harga * $item->jumlah_barang`
  - `customer/keranjang/index.blade.php:61` -> `(float) $barang->harga * $item->jumlah_barang`
  - `customer/keranjang/index.blade.php:119` -> `number_format($barang->harga, ...)`
- `detailBarang` (`CustomerController.php:43-51`) passes `$barang` with `ukurans` eager-loaded.
- `customer/barang/detail.blade.php:99` shows base `harga` only.
- Pegawai ukuran list `k_ukuran.blade.php` has inline-form precedent: `k_stokukuran.blade.php:32-57`.

## Decisions
- Form: inline in the ukuran list table (mirrors stok menu).
- Price semantics: absolute per-ukuran (replaces base for that ukuran in cart).
- Display: price range when ukuran prices exist (per `status` flash).

## Schema
- New migration `add_harga_ukuran_to_ukurans_table`:
  ```php
  $table->decimal('harga_ukuran', 10, 2)->after('ukuran')->nullable();
  ```

## Changes
### 1. `app/Models/Ukuran.php`
- `$fillable` += `'harga_ukuran'`
- `casts()` += `'harga_ukuran' => 'decimal:2'`

### 2. `routes/web.php` (pegawai group, mirrors `ustoku`)
```php
Route::put('update-harga-ukuran/{id_b}/{id_u}', [UkuranController::class, 'updateHargaUkuran'])->name('uhargau');
```

### 3. `app/Http/Controllers/Pegawai/UkuranController.php`
```php
public function updateHargaUkuran(Request $request, $id_b, $id_u)
{
    $ukuran = Ukuran::where('id_ukuran', $id_u)->first();

    $data = $request->validate([
        'harga_ukuran' => ['required', 'numeric', 'min:0'],
    ], [
        'harga_ukuran.required' => 'Masukkan Harga!',
        'harga_ukuran.numeric' => 'Harga harus angka!',
        'harga_ukuran.min' => 'Harga tidak boleh negatif!',
    ]);

    $ukuran->harga_ukuran = $data['harga_ukuran'];
    $ukuran->update();

    return back()->with('ehargastatus-' . $id_u, 'Harga Ukuran Berhasil Di Update!');
}
```

### 4. `resources/views/pegawai/kelola/k_ukuran.blade.php`
- Add `<th>Harga (Rp)</th>` between `Ukuran` and `Stok Ukuran` (total stays 5 cols, empty-row `colspan=5` unchanged).
- New cell: inline PUT form (mirror `k_stokukuran:32-57`):
  ```blade
  <td>
      <form action="{{ route('pegawai.uhargau', [$uk->id_barang, $uk->id_ukuran]) }}" method="post">
          @csrf @method('PUT')
          <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="number" name="harga_ukuran" min="0" step="0.01"
                  value="{{ old('harga_ukuran', $uk->harga_ukuran) }}"
                  class="form-control form-control-pink" style="max-width: 140px" required>
              <button type="submit" class="btn btn-pink"><i class="fa-solid fa-floppy-disk"></i></button>
          </div>
      </form>
      @if (session('ehargastatus-' . $uk->id_ukuran))
          <small class="text-success">{{ session('ehargastatus-' . $uk->id_ukuran) }}</small>
      @endif
  </td>
  ```

### 5. Cart pricing (live compute)
- `KeranjangController.php:33`:
  ```php
  return (float) ($item->ukuran?->harga_ukuran ?? $item->barang->harga) * $item->jumlah_barang;
  ```
- `customer/keranjang/index.blade.php:61`:
  ```php
  $subtotal = $isAvailable ? (float) ($item->ukuran?->harga_ukuran ?? $barang->harga) * $item->jumlah_barang : 0;
  ```
- `customer/keranjang/index.blade.php:119`:
  `number_format($item->ukuran?->harga_ukuran ?? $barang->harga, 0, ',', '.')`

### 6. Customer detail price range (`customer/barang/detail.blade.php:99`)
```blade
@if ($hasUkuran && $barang->ukurans->pluck('harga_ukuran')->filter()->isNotEmpty())
    @php
        $h = $barang->ukurans->pluck('harga_ukuran')->filter()->map(fn ($p) => (float) $p)->sort()->values();
        $min = $h->first(); $max = $h->last();
    @endphp
    <h3 class="text-pink mb-4">
        Rp {{ number_format($min, 0, ',', '.') }}@if ($max > $min) - Rp {{ number_format($max, 0, ',', '.') }}@endif
    </h3>
@else
    <h3 class="text-pink mb-4">Rp {{ number_format($barang->harga, 0, ',', '.') }}</h3>
@endif
```
`ukurans` already eager-loaded — no controller change.

## Behavior
- Has ukuran prices -> range (`Rp min - Rp max`), single if equal.
- Has ukuran but no prices set -> falls back to base `barang.harga`.
- No ukuran -> base `barang.harga`.
- Cart line uses ukuran price when present, else base.

## Verify
- `php artisan migrate`
- Manual: pegawai `Ukuran` menu edits price inline; flash shows.
- Manual: customer detail shows range; cart line + subtotal use ukuran price.
- `vendor/bin/pint --format agent`
- `php artisan test --compact`
