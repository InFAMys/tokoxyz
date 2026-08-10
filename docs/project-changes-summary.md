# Project Changes Summary

This document summarizes the work already completed on the Toko XYZ project.

Session:

- `codex resume 019fe002-38fe-7d11-a2f9-bae16809d01a`
- `opencode -s ses_01da2634dffeCIW3TgoBi4BG41`

## Database And Barang Table

- Reviewed the `barangs` table structure and related barang management flow.
- Added a migration to move the `kode_barang` column so it appears before `nama_barang` in the `barangs` table.
- Added a nullable `berat` column to the `barangs` table.
- Changed `berat` to use a decimal kilogram value, stored as `decimal(8, 3)`.
- Removed the unnecessary weight conversion step because no existing barang records had `berat` values filled in yet.

## Barang Model And Pegawai Forms

- Updated the `Barang` model so `berat` can be mass assigned and cast correctly.
- Updated the pegawai tambah barang form to include a `berat` input.
- Updated the pegawai edit barang form to include and preserve the `berat` value.
- Normalized comma decimal input for `berat`, so values like `1,5` can be accepted as `1.5` kilograms.
- Updated barang validation to allow nullable decimal weight values.
- Updated pegawai barang list and detail views to display `berat` in kilograms.

## Customer Barang Detail Page

- Added a customer-facing barang detail route:

```text
GET /barang/{id}
Route name: barang.detail
Controller: Customer\CustomerController@detailBarang
```

- Added `detailBarang` to `CustomerController`.
- The detail query loads related `brand`, `kategori`, and `ukurans` data.
- The detail page only shows barang where `status` is `Ditampilkan`.
- Created `resources/views/customer/barang/detail.blade.php`.
- The customer detail page displays:
    - product image gallery
    - product name
    - price
    - brand
    - category
    - stock summary
    - weight in kilograms
    - description
    - ukuran options
    - preorder information

## Customer Home Product Cards

- Updated product cards on the customer home page so clicking a barang opens the customer detail page.
- Used the named route `barang.detail` for links.
- Kept the implementation based on existing Blade, Bootstrap, and custom CSS patterns.
- Did not use Tailwind for the customer barang detail changes.

## Styling Fixes

- Fixed product name text in customer cards appearing blue after the cards became links.
- Added missing CSS variables in `resources/css/style.css`, including `--gray-700` and `--white`.
- Reused existing styling classes for the detail page, including `card-pink`, `product-detail-img`, `thumb-row`, `summary-box`, and `btn-pink-outline`.

## Documentation

- Created `docs/project-summary.md` with a general overview of the project structure, user areas, models, routes, styling, and development commands.
- Created this file, `docs/project-changes-summary.md`, to summarize the completed changes.

## Verification

- Ran PHP syntax checks for edited PHP files.
- Confirmed the customer barang detail route exists with `php artisan route:list --path=barang --no-interaction`.
- Ran Laravel Pint on dirty PHP files with `vendor/bin/pint --dirty --format agent`.
- Attempted to run the test suite with `php artisan test --compact`.
- The test run could not complete in this environment because MySQL was not reachable at the configured database connection.

## Files Changed Or Added

- `routes/web.php`
- `app/Http/Controllers/Customer/CustomerController.php`
- `app/Http/Controllers/Pegawai/BarangController.php`
- `app/Models/Barang.php`
- `resources/views/welcome.blade.php`
- `resources/views/customer/barang/detail.blade.php`
- `resources/views/pegawai/kelola/tambah/tambahBarang.blade.php`
- `resources/views/pegawai/kelola/edit/editBarang.blade.php`
- `resources/views/pegawai/kelola/k_barang.blade.php`
- `resources/views/pegawai/kelola/detail/detailBarang.blade.php`
- `resources/css/style.css`
- `database/migrations/2026_08_08_063009_move_kode_barang_column_in_barangs_table.php`
- `database/migrations/2026_08_08_132140_add_berat_to_barangs_table.php`
- `database/migrations/2026_08_08_132542_change_berat_to_decimal_on_barangs_table.php`
- `docs/project-summary.md`
- `docs/project-changes-summary.md`
