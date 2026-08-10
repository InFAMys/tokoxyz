# Plan: Zero `stok` on first ukuran add

## Goal
Prevent stok-tracking conflict when a barang originally carries a plain `stok`
(no ukuran) and a pegawai later adds an ukuran.

`barang.stok` and the ukuran rows' `stok_ukuran` double-count in
`Barang::stokReady()` (Barang.php:59), which sums `stok_ukuran` when ukuran
exist. Value left stale and inconsistent.

## Change

File: `app/Http/Controllers/Pegawai/UkuranController.php`, method `addUkuran`.

1. Before `Ukuran::create`: capture prior state.
   ```php
   $hadUkuran = $brg->ukurans()->exists();
   ```
2. After create, zero out the scalar when this was the first ukuran.
   ```php
   if (! $hadUkuran) {
       $brg->stok = 0;
       $brg->save();
   }
   ```

## Behavior
- Barang has ukuran-less stok (`stok` > 0) + first ukuran added → `stok` reset
  to 0, ukuran `stok_ukuran` becomes the source of truth.
- Later ukuran adds → no-op (already 0).
- All ukuran deleted → stays 0 (unchanged, nested behind `exists()` check).

## Verify
- `vendor/bin/pint --dirty --format agent`
- Manual flow: create barang with stok, add first ukuran, confirm
  barang `stok` = 0 and stokReady matches ukuran sum.

---

## Addendum: stok validation hardening

`StokController.php` uses `'stok' => ['required', 'string', 'min:1']` in both
write methods. `min:1` on a string counts characters, not magnitude — accepts
`"0"`, `"abc"`, `"1.5"`, negatives.

Change rule in both `updateStok` (line 48) and `updateStokUkuran` (line 67):

```php
$data = $request->validate([
    'stok' => ['required', 'integer', 'min:0'],
], [
    'stok.required' => 'Masukkan Stok!',
    'stok.integer'  => 'Stok Harus Angka!',
    'stok.min'      => 'Stok Tidak Boleh Kurang Dari 0!',
]);
```

- `string` → `integer`: reject junk, decimals.
- `min:1` (chars) → `min:0` (numeric): allow 0, reject negative.
- `min:0` valid in `updateStokUkuran` — `barang.stok` forced to 0 once ukuran
  exist, ukuran row holds the real count.
- `addUkuran` has no stok input; nothing to harden there.
- Success flash messages unchanged.

## Out of scope (skipped)
- Stok validation hardening on the add form (covered by addendum above).
- Any change to `stokReady()` itself.
