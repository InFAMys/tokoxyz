# Rencana: Bikin Semua View Responsive

## Status Audit
Sudah responsive (tak butuh kerja):
- Form (auth, profile, alamat, tambah/edit): pake `auth-card` max-width 420px, centered.
- Welcome grid produk `col-6 col-md-3 col-lg-2`.
- detailBarang layout `row g-4` + `col-sm-6`.
- Navbar customer/owner/pegawai: `navbar-expand-lg` collapse -> hamburger < 992px.
- Kartu stat dashboard `col-md-3` -> stack.

Masalah:
1. 9 tabel ga dibungkus `.table-responsive` -> overflow horizontal di mobile.
2. `search-wrapper` hard `width:230px` -> sempit di layar 360px.
3. detailBarang header 5 tombol `d-flex gap-2` ga `flex-wrap` -> overflow.
4. Ga ada guard global overflow-x.
5. CSS `.sidebar` mati (orphan media-query).

## Langkah
### A. Bungkus 9 tabel pake `<div class="table-responsive">`
Bootstrap native horizontal scroll. Diff kecil tiap file.
- pegawai: k_barang, k_kategori, k_brand, k_ukuran, k_stok, k_stokukuran, dashboard
- owner: k_diskon, k_pegawai, dashboard

### B. Toolbar search
- CSS style.css: tambah
  `@media (max-width:576px){ .search-wrapper{width:100%!important} }`

### C. detailBarang header
- detailBarang.blade.php:18-37: tombol row + `flex-wrap` + `gap-2`;
  stack `flex-column flex-md-row` buat title + tombol.

### D. CSS polish
- `body { overflow-x: hidden; }`
- `@media (max-width:576px){ .auth-card{ padding:2rem 1.25rem } }`
- Hapus blok `.sidebar` mati (opsional).

### E. `w-md-auto` (k_stokukuran)
- Biarkan — no-op, ga ganggu.

## File disentuh
- 10 view: k_barang, k_kategori, k_brand, k_ukuran, k_stok, k_stokukuran,
  pegawai/dashboard, owner/k_diskon, owner/k_pegawai, owner/dashboard, detailBarang
- 1 css: resources/css/style.css
Ga ada perubahan routing/controller/logika. Semua visual.

## Keputusan ditunda
Tabel mobile: (A) scroll horizontal native, atau (B) tambah ubah k_barang & k_diskon
jadi kartu stack di mobile. Belum dipilih.
