# Toko XYZ Project Summary

## Overview

Toko XYZ is a Laravel 13 web application for managing and browsing store products. It supports three main user areas: customer, employee, and owner. The app uses Blade views, Bootstrap, custom CSS in `resources/css/style.css`, Vite assets, MySQL, and Pest for testing.

## Main User Areas

### Customer

- Browse displayed products from the home page.
- Open a customer-facing barang detail page at `barang/{id}`.
- Register, log in, log out, and manage profile information.
- Manage saved addresses through the alamat pages.

Main files:

- `app/Http/Controllers/Customer/CustomerController.php`
- `app/Http/Controllers/Customer/AlamatController.php`
- `resources/views/welcome.blade.php`
- `resources/views/customer/barang/detail.blade.php`
- `resources/views/customer/profile/*`
- `resources/views/customer/alamat/*`

### Pegawai

- Manage barang, brand, kategori, stok, and ukuran.
- Add, edit, view detail, and delete barang.
- Upload barang photos and thumbnails.
- Manage product status: `Ditampilkan` or `Disembunyikan`.
- Manage stock either directly on barang or by ukuran.

Main files:

- `app/Http/Controllers/Pegawai/BarangController.php`
- `app/Http/Controllers/Pegawai/BrandController.php`
- `app/Http/Controllers/Pegawai/KategoriController.php`
- `app/Http/Controllers/Pegawai/StokController.php`
- `app/Http/Controllers/Pegawai/UkuranController.php`
- `resources/views/pegawai/kelola/*`

### Owner

- Manage pegawai accounts.
- Manage diskon records.
- Access owner dashboard and profile pages.

Main files:

- `app/Http/Controllers/Owner/OwnerController.php`
- `app/Http/Controllers/Owner/KelolaPegawaiController.php`
- `app/Http/Controllers/Owner/KelolaDiskonController.php`
- `resources/views/owner/*`

## Core Models

- `Barang`: products, with brand, kategori, stock, photos, thumbnail, status, preorder data, and berat in kilograms.
- `Brand`: product brands.
- `Kategori`: product categories.
- `Ukuran`: product size options and size-specific stock.
- `Customer`: customer accounts.
- `Alamat`: customer addresses.
- `Pegawai`: employee accounts.
- `Owner`: owner accounts.
- `Diskon`: discount records.

## Important Routes

Public/customer routes:

- `GET /` -> customer home page.
- `GET /barang/{id}` -> customer barang detail page.
- `GET /login`, `POST /login` -> customer login.
- `GET /register`, `POST /register` -> customer registration.
- Authenticated customer profile and alamat routes live under the root prefix.

Pegawai routes:

- Prefix: `/pegawai`
- Route names: `pegawai.*`
- Includes dashboard, profile, barang, brand, kategori, stok, and ukuran management.

Owner routes:

- Prefix: `/owner`
- Route names: `owner.*`
- Includes dashboard, profile, pegawai management, and diskon management.

## Barang Data Notes

- Primary key: `id_barang`.
- `kode_barang` is positioned before `nama_barang` in the `barangs` table.
- `foto` is stored as JSON and cast to an array.
- `thumbnail` stores the selected thumbnail path.
- `berat` is stored as a nullable decimal kilogram value with three decimal places.
- Customer pages should only show products where `status` is `Ditampilkan`.
- The customer detail page displays brand, category, stock, weight, description, ukuran, preorder, and gallery images.

## Styling

- The app primarily uses Bootstrap and custom CSS from `resources/css/style.css`.
- Customer and admin layouts load Bootstrap, Google Fonts, Font Awesome, and Vite assets.
- Product cards and detail pages reuse shared classes such as `product-card`, `product-detail-img`, `thumb-row`, `card-pink`, `summary-box`, and `btn-pink-outline`.
- Tailwind is present as a dependency, but recent customer-facing barang changes were intentionally made without Tailwind.

## Development Commands

Common commands:

```bash
composer run dev
php artisan migrate
php artisan test --compact
vendor/bin/pint --dirty --format agent
npm run dev
npm run build
```
