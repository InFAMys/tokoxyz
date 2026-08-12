# Alur Transaksi dari Belanja sampai Pembayaran

Penjelasan ramah-customer: dari menemukan barang sampai order terbayar lewat Midtrans. Tidak ada istilah teknis.

---

## 1. Cari & Pilih Barang

Jelajahi katalog di halaman utama, hasil pencarian, atau halaman detail barang. Setiap barang menampilkan rentang harga. Kalau barang punya beberapa ukuran, kamu tinggal pilih ukuran di halaman detail — harganya otomatis menyesuaikan. Ada label **Habis** di atap kartu kalau stok barang sedang kosong.

## 2. Masukkan ke Keranjang

Tekan tombol tambah, tentukan jumlah, lalu barang masuk ke keranjang belanjamu. Sistem otomatis cek stok: kalau jumlah yang kamu pilih melebihi stok yang ada, kamu akan diberi tahu dan tidak bisa lanjut.

## 3. Pilih Barang untuk Dibayar

Dari halaman keranjang, beri tanda centang pada barang yang mau kamu bayar (bisa lebih dari satu). Lalu tekan tombol checkout. Barang yang tidak dicentang tetap aman di keranjang dan tidak ikut terbayar.

## 4. Tentukan Alamat & Ongkir

Di halaman checkout, kamu melihat ringkasan barang terpilih, subtotal, dan total berat. Lalu:

- **Pilih alamat pengiriman** — pilih dari alamat yang sudah tersimpan.
- **Pilih layanan pengiriman** — biaya ongkir otomatis muncul sesuai tujuan pengiriman.
- **Masukkan kode diskon** (kalau punya) — potongan langsung dihitung.

Semua angka dihitung ulang oleh sistem di sisi server, jadi pasti akurat dan tidak bisa dimanipulasi dari halaman.

## 5. Buat Pesanan

Setelah kamu klik bang-un pesanan:

- Pesanan dibuat dengan nomor unik, lengkap dengan daftar barang, alamat tujuan, ongkir, dan diskon.
- Total yang harus dibayar dihitung: `subtotal + ongkir - diskon`.
- Kalau totalnya **Rp 0** (misalnya pesanan gratis), status langsung **Lunas tanpa pembayaran**.
- Kalau ada nominalnya, sistem membuat tagihan pembayaran via **Midtrans** dan membawa kamu ke langkah pembayaran.

Barang-barang yang sudah dipesan **dihapus dari keranjangmu**, menandakan kamu sudah berbelanja barang itu.

## 6. Bayar

Kamu akan melihat halaman ringkasan dengan tombol **Bayar Sekarang**. Saat ditekan, jendela pembayaran Midtrans terbuka — kamu tinggal pilih cara bayar (bank transfer, e-wallet, dll) dan selesaikan pembayaran. Kalau pesanan gratis, status langsung muncul tanpa perlu bayar.

## 7. Konfirmasi & Status Pesanan

Pembayaran terkonfirmasi otomatis:

- Midtrans mengirim notifikasi pembayaran ke sistem, biasanya tidak sampai beberapa menit setelah kamu bayar.
- Status pesanan diperbarui otomatis menjadi **Lunas/Bayar**. Sistem juga memeriksa ulang status ke Midtrans untuk berjaga-jaga kalau notifikasinya telat.
- Kalau lama tidak dibayar, pesanan otomatis berstatus **Kadaluarsa**.
- Kamu bisa cek semua pesananmu di halaman **Riwayat**.

## Tentang Stok

Stok barang dikurangi **hanya setelah pesanan benar-benar lunas** (bukan saat memasukkan ke keranjang). Ini menjaga agar stok tidak terpotong ganda untuk pesanan yang dibatalkan atau tidak dibayar.
