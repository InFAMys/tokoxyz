# Plan: "Stok" column on Kelola Barang (renewed)

## Current state (checked)
- `_barang_rows.blade.php:18` already uses `<td>{{ $brg->stok }}</td>`
  (old `{{ $barangs }}` collection bug gone).
- `$barangs` still defined in `BarangController@listBarang` line 30:
  `Barang::withExists(['ukurans as ada_di_ukuran'])->when($q, $filter)->get();`
  -> now an **unused orphan** in the controller.
- No `stokReady()` method exists yet.
- Other `stok` uses (detailBarang, k_stok, StokController) are for editing/detail,
  legit, left untouched.

## Desired
- If `ukurans` table has rows referencing this `id_barang` ->
  show `sum(stok_ukuran)`.
- Otherwise -> show `barang.stok`.

## Schema
- `barangs`: PK `id_barang`, `stok` int.
- `ukurans`: FK `id_barang` -> barangs.id_barang, `stok_ukuran` int, softDeletes.
- `Barang::ukurans()` relation exists.

## Changes
### 1. Barang model — add method
```php
public function stokReady(): int
{
    return $this->ukurans->isNotEmpty()
        ? $this->ukurans->sum('stok_ukuran')
        : $this->stok;
}
```
Uses loaded `ukurans` relation; soft-deleted ukurans auto-excluded.

### 2. listBarang — drop orphan `$barangs`, eager-load `ukurans`
```php
$barang = Barang::query()
    ->with('ukurans')
    ->when($q, $filter)
    ->get();

if ($request->ajax()) {
    return view('pegawai.kelola._barang_rows', compact('barang'))->render();
}

return view('pegawai.kelola.k_barang', compact('barang'));
```
`$barangs` removed everywhere (only listBarang referenced it).

### 3. _barang_rows — stok cell
```html
<td>{{ $brg->stokReady() }}</td>
```
(replaces `{{ $brg->stok }}`).

## Behavior
- Has ukuran rows -> `sum(stok_ukuran)` of non-deleted ukurans.
- No ukuran rows -> `barang.stok`.
- int return; soft-deleted ukurans excluded.

## Verify
- k_barang: each row shows correct sum/fallback.
- `grep -rn 'tbody\|__barangs'` -> no `$barangs` refs.
- `vendor/bin/pint --format agent`
- `php artisan view:cache`
- `php artisan test --compact`
