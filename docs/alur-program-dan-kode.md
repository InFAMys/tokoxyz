# Alur Program dan Kode — Toko XYZ

Dokumentasi teknis untuk pengembang: arsitektur, alur utama, dan konvensi kode aplikasi toko online (Laravel 13, PHP 8.5). Bahasa alur bisnis sederhana ada di `alur-transaksi-midtrans.md`.

---

## 1. Ringkasan Arsitektur

Aplikasi **monolit Laravel** dengan **3 area terisolasi**, masing-masing punya **guard + layout sendiri**:

| Area | Prefix URL | Guard | Layout |
|---|---|---|---|
| Customer (publik) | `/` (root, tak ber-prefix) | `customer` | `resources/views/customer/layouts/app.blade.php` |
| Owner (pemilik toko) | `/owner` | `owner` | `resources/views/owner/layouts/app.blade.php` |
| Pegawai (staf) | `/pegawai` | `pegawai` | `resources/views/pegawai/layouts/app.blade.php` |

Guards didefinisikan di `config/auth.php` (driver `session`, provider masing-masing model: `owners`, `pegawais`, `customers`). Halaman customer yang butuh login di-klaster `auth:customer`; yang publik (home, detail barang, cari, kategori, brand) berada di luar middleware.

Semua route ada di **satu file**: `routes/web.php` (±250 baris). Nama route di-scope: `owner.*`, `pegawai.*`, `barang.*`, `keranjang.*`, `checkout.*`, dst.

---

## 2. Model, Database, dan Relasi

Semua primary key **kustom** (bukan `id`). Dipakai lewat atribut `#[Table(key: 'id_x')]` dan `$primaryKey`.

| Model | Tabel | Primary key | Catatan |
|---|---|---|---|
| `Customer` | `customers` | `id_cst` | soft delete **tidak** dipakai |
| `Owner` | `owners` | `id_owner` | akun tunggal pemilik |
| `Pegawai` | `pegawais` | `id_pegawai` | soft delete |
| `Barang` | `barangs` | `id_barang` | soft delete; punya `foto` (JSON array), `thumbnail`, `berat`, `preorder`, `estimasi_preorder` |
| `Varian` | `varians` | `id_varian` | **soft delete**; nama lama tabel `ukuran`. Punya `stok_varian`, `harga_varian` (nullable) |
| `Kategori` | `kategoris` | `id_kategori` | soft delete |
| `Brand` | `brands` | `id_brand` | soft delete |
| `Keranjang` | `keranjang` | `id_keranjang` | relasi ke barang+varian |
| `Checkout` | `checkouts` | `id_checkout` | inti transaksi; punya `order_id` unik, `snap_token`, `no_resi`, `refund_failed_at` |
| `CheckoutItem` | `checkout_items` | `id_checkout_item` | snapshot nama/harga saat belanja |
| `Membership` | `memberships` | `id_membership` | pembayaran sekali Rp 25.000 → diskon 10% |
| `Diskon` | `diskons` | `id_diskon` | kode diskon, `status_diskon` (aktif/nonaktif) |
| `Alamat` | `alamats` | `id_alamat` | wilayah via API (provinsi/kota/kecamatan/desa) |
| `Pengaturan` | `pengaturans` | `id_pengaturan` | key-value (`nama_kunci` unik, `nilai` teks) |

Relasi penting (`app/Models/Barang.php`):
- `Barang::stokReady()` = jumlah `stok_varian` bila ada varian, selain itu `stok` barang.
- `Barang::thumbnailPath()` = `thumbnail ?? foto[0] ?? null`.
- `Barang::galleryPaths()` = daftar foto untuk slide, **thumbnail dihilangkan** bila konten filenya identik dengan salah satu foto (deteksi via `md5_file`, hasil di-cache `rememberForever`). File thumbnail & foto disimpan di path berbeda (`…/thumbnails/` vs `…/`), jadi perbandingan string tidak cukup.

### Soft delete
`barangs`, `varians`, `pegawais`, dll memakai `SoftDeletes`. Eloquent otomatis mengecualikan baris terhapus; keranjang sengaja menampilkan barang/varian yang sudah dihapus (`withTrashed()` di `KeranjangController`) lalu menandainya tidak tersedia.

---

## 3. Alur Utama

### 3.1 Home Customer & Katalog
- `GET /` → `CustomerController@home` → `resources/views/welcome.blade.php`.
- Query: barang baru (12), rekomendasi (`inRandomOrder`), kategori & brand yang punya barang aktif, plus "paling banyak dibeli" dari `CheckoutItem` agregat (status `shipping/delivered/completed`).
- Jika tidak ada barang, tiap seksi produk menampilkan kartu kosong ("Belum ada produk baru." dll).
- Pencarian (`/cari`), filter kategori/brand memakai `paginate(12)`.

### 3.2 Detail Barang & Varian
- `GET /barang/{id}` → `CustomerController@detailBarang` → `customer/barang/detail.blade.php`.
- Slide foto dari `$barang->galleryPaths()`.
- Harga tampil sebagai **rentang** (min–max harga varian) bila varian punya `harga_varian`.
- Harga varian juga ditampilkan di dropdown "Pilih Varian" dan daftar "Pilihan Ukuran" (`Rp …`).
- Klik "Masukkan Keranjang" → POST `keranjang.store`. Server cek stok (varian bila ada), preorder memakai jalur stok = 0.

### 3.3 Keranjang → Checkout
1. `keranjang.index` — daftar item, centang untuk checkout. Item preorder diberi badge **Preorder** (base tersedia + `preorder === 'Tersedia'` + stok 0).
2. `keranjang.checkout` — simpan pilihan ke session, redirect ke `checkout.create`.
3. `checkout.create` — pilih alamat (wilayah dari API), layanan kirim (ongkir di-query server via `KlikresiApi`), kode diskon. Total: `subtotal + ongkir - diskon`.
   - **Server-side recompute**: nilai `shipping_cost`, diskon, dan member diskon **dihitung ulang di server** (`CheckoutController@verifyShippingCost`), tidak pernah percaya input klien.
4. `checkout.store` (POST `/checkout`) → buat `Checkout`:
   - `order_id` unik, daftar item (snapshot nama+harga), alamat, ongkir, diskon.
   - `status = 'pending'` jika total > 0, selain itu langsung `'paid'`.
   - Minta `snap_token` Midtrans (`MidtransApi`).
   - Item keranjang terpilih dihapus.
   - Stok **dikurangi hanya saat status jadi `paid`** (`CheckoutStatusService@decrementStockForItems`).

### 3.4 Pembayaran Midtrans (Snap)
- `checkout.show` → tombol "Bayar Sekarang" membuka popup Snap (`resources/js/script.js`, blok `Snap.js payment popup`).
- Butuh token baru bila `snap_token` kosong: POST `checkout/token` (via `data-token-url` di form).
- Setelah `onSuccess/onPending` → polling `GET checkout/{id}/status` (maks 20× / 1 detik) lalu reload.
- **Webhook**: `POST /checkout/notification` (tanpa CSRF, tanpa login) → `CheckoutController@notification` → memetakan `transaction_status` Midtrans:
  `capture/settlement → paid`, `expire → expired`, `cancel → cancelled`, `refund → refunded`, `partial_refund/partially_refunded`.
- Kalau status jadi `paid` dan sebelumnya belum → **stok dikurangi**.
- Webhook juga menangani `memberships` (bayar member) lewat jalur terpisah di controller yang sama.

### 3.5 Status Pesanan (CheckoutStatusService)
`app/Services/CheckoutStatusService.php` — satu-satunya otoritas transisi status. Status: `pending, paid, processed, shipping, delivered, completed, cancelled, expired, refunded, partially_refunded, cancel_pending`.

Logika utama:
- `reconcile()` — sinkronkan dengan Midtrans (`reconcileMidtrans`) dan kurir (`reconcileShipping`).
- Pending > 24 jam → `cancelled`.
- `paid` > 3 hari tanpa proses → refund otomatis.
- `processed` > 3 hari → refund otomatis.
- `shipping` + `no_resi` → cek tracking kurir → `delivered` bila sampai.
- `delivered` > 7 hari → `completed`.
- **Refund otomatis**: kirim refund ke Midtrans (`refund`), set `refunded` + kosongkan `refund_failed_at`. Bila gagal → `refund_failed_at` terisi (muncul alert merah di dashboard owner/pegawai, bisa ditutup dengan tombol ✕; hilang permanen hanya setelah refund manual & `orders:reconcile`).

Command terjadwal: `php artisan orders:reconcile` (`app/Console/Commands/ReconcileOrders.php`) — memproses semua status aktif, lalu membersihkan `refund_failed_at` untuk yang sudah `refunded`.

### 3.6 Pegawai Mengelola Pesanan
Route di prefix `pegawai` (auth `pegawai`):
- `pesanan` → daftar + filter status.
- `pesanan/{id}` → detail + timeline (produk, alamat, kurir, tracking).
- Aksi: `proses` (paid→processed), `kirim` (set `no_resi` + status shipping), `cancel` (pembatalan), `cancel-approve` / `cancel-reject` (keputusan pembatalan, reject butuh alasan → `cancel_response`), `ubah-tracking`.

### 3.7 Pegawai Mengelola Inventori
Prefix `pegawai`, dibungkus middleware `pegawai.inventory`:
- Barang: CRUD + `check-kode-barang` (validasi unik AJAX), upload thumbnail + foto (max 5, 5MB, JPG/PNG).
- Kategori & Brand: CRUD.
- Varian (`ukuran`): `ukuran-barang/{id}` menampilkan daftar varian + **edit stok inline** (`name="stok[{id_varian}]"`, error per-baris) + **edit harga varian inline** (`harga_varian`).
- Stok barang tanpa varian: `stok-barang/{id}`.
- `normalizeBerat` (`BarangController`) mengubah koma desimal berat → titik sebelum validasi.

### 3.8 Membership & Diskon
- **Member**: `member/mendaftar` → bayar Rp 25.000 via Midtrans (route `membership.token`), status `memberships` dicek ulang (reconcile) saat halaman dibuka. Member aktif → diskon 10% otomatis di checkout.
- **Diskon kode**: owner buat kode (`KelolaDiskonController`), `sendDiskon` → kirim ke customer via WhatsApp (`config/services.whatsapp`). Customer pakai kode di checkout; validasi di server.

### 3.9 Laporan & Pengaturan (Owner)
- `owner/laporan` → `LaporanController` (filter tanggal, ringkasan, produk terlaris, kategori). Belum ada export PDF/Excel.
- `owner/kelola-pengaturan` → key-value `Pengaturan` (nama toko, kontak, sosmed, deskripsi) — dibaca via `Pengaturan::allAsArray()` dan di-share ke layout sebagai `$namaToko`, `$tagline`, dst.
- `owner/kelola-pegawai` → buat/edit/hapus akun pegawai (bukan di route `register`; **tidak ada register untuk pegawai**). Route `owner/register` sudah **dihapus** — owner dibuat langsung/di seeder.

---

## 4. Konvensi Kode yang Harus Diikuti

- **Primary key kustom**: selalu `#[Table(key: 'id_x')]`, pakai nama kolom persis (`id_barang`, `id_varian`, `id_cst`, …) di query/relasi.
- **Controller**: `php artisan make:` + `--no-interaction`. HTTP controller hanya orkestrasi; logika bisnis/transisi status di service (`CheckoutStatusService`, `MidtransApi`, `KlikresiApi`, `WilayahApi`).
- **Validator**: pesan error custom Indonesia via parameter kedua `$request->validate([...], [...])`. Default `lang/id/validation.php` (locale `id`).
- **Flash → toast**: semua pesan flash (`astatus`, `estatus`, per-baris `estatus-{id}`, `delStatus`, …) ditampilkan sebagai **Bootstrap toast** lewat partial `resources/views/components/toasts.blade.php` (otomatis men-collect semua key string + `$errors`). **Jangan** re-introduce alert box untuk flash.
- **Error field** tetap `@error`/`invalid-feedback` di bawah input.
- **Harga**: input `type="text"`, diformat live id-ID (titik ribuan) oleh `moneyFormat()`/`toIntegerDigits()` di `resources/js/script.js`; JS melepas separator saat submit karena server validasi `numeric`. DB `decimal(12,2)` menghasilkan nilai seperti `"3000000.00"` — `number_format(…, 0, ',', '.')` untuk tampilan.
- **Berat**: input mask koma-desimal (`.` atau `,`, maks 3 desimal), diubah ke titik di server.
- **Per-row form** (daftar stok/harga varian): nama input unik per baris (`stok[{id_varian}]`) agar `@error('stok.{id}')` hanya muncul di baris yang gagal.
- **JS terpusat**: semua kustom JS di `resources/js/script.js` (satu modul Vite, dimuat `@vite` di tiap layout). Jangan taruh JS inline di view.
- **Line ending view bercampur** (CRLF/LF) — pakai tool edit, bukan sed dengan anchor `$`.

---

## 5. Frontend & Build

- **Vite** (`vite.config.js`). Setelah ubah CSS/JS wajib `npm run build`. Blade dicache ulang dengan `php artisan view:cache`.
- Bootstrap 5.3.8 + Font Awesome dimuat dari CDN **setelah** `@vite`, sehingga objek global `bootstrap` tersedia untuk `script.js` (`bootstrap.Toast`).
- CSS utama: `resources/css/style.css` (variabel pink, `product-card`, `product-detail-img`, `card-pink`, `summary-box`, timeline pengiriman).
- Kartu produk di grid memakai `aspect-ratio: 1/1` + `object-fit: contain` pada foto; `.product-img` harus `min-height: 0; overflow: hidden` agar gambar 1280px tidak memaksa kartu melebar tinggi.

---

## 6. Service Integrasi (app/Services)

| Service | Fungsi |
|---|---|
| `CheckoutStatusService` | Transisi status, stok, refund otomatis, tracking kurir |
| `MidtransApi` | Snap token, status transaksi, refund |
| `KlikresiApi` | Cek ongkir / tracking kirim |
| `WilayahApi` | Data provinsi/kota/kecamatan/desa untuk form alamat |
| `FakeKlikresiApi` | Mock untuk development/tests |

Konfigurasi di `config/services.php`: `midtrans` (server key, client key, is_production), `klikresi`, `whatsapp`.

---

## 7. Testing & Tools

- **Pest 5** (`php artisan test --compact`). Saat ini `tests/Feature` hanya berisi `ExampleTest` dan `DiskonTest` (validasi kode diskon) — critical path uang (webhook, refund, total checkout) belum ter-cover.
- **Pint** setelah ubah PHP: `vendor/bin/pint --dirty --format agent`.
- Error message semua user-facing berbahasa Indonesia; yang **sengaja English**: respons webhook Midtrans (`Invalid signature`, `Order not found`) di `CheckoutController@notification`.

---

## 8. Catatan Pengembangan Selanjutnya

1. Notifikasi status pesanan (infra `notifications` sudah ada, baru 1 tipe: `DiscountAvailable`).
2. Test coverage untuk alur Midtrans/refund/perhitungan total.
3. Expiry otomatis pesanan `pending` (sebagian sudah ada di `CheckoutStatusService`: >24 jam → cancelled).
4. Export laporan PDF/Excel untuk owner; webhook & notifikasi dipindah ke queue.
5. Audit trail perubahan stok/harga oleh pegawai.