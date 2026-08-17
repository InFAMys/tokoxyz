<?php

use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\OwnerAuthController;
use App\Http\Controllers\Auth\PegawaiAuthController;
use App\Http\Controllers\Customer\AlamatController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\KeranjangController;
use App\Http\Controllers\Customer\MemberController;
use App\Http\Controllers\Customer\NotificationController;
use App\Http\Controllers\Owner\KelolaDiskonController;
use App\Http\Controllers\Owner\KelolaPegawaiController;
use App\Http\Controllers\Owner\LaporanController;
use App\Http\Controllers\Owner\OwnerController;
use App\Http\Controllers\Pegawai\BarangController;
use App\Http\Controllers\Pegawai\BrandController;
use App\Http\Controllers\Pegawai\KategoriController;
use App\Http\Controllers\Pegawai\PegawaiController;
use App\Http\Controllers\Pegawai\PesananController;
use App\Http\Controllers\Pegawai\StokController;
use App\Http\Controllers\Pegawai\UkuranController;
use App\Models\Checkout;
use App\Models\Diskon;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/', [CustomerController::class, 'home'])->name('home');
Route::get('barang/{id}', [CustomerController::class, 'detailBarang'])->name('barang.detail');
Route::get('cari', [CustomerController::class, 'cari'])->name('barang.search');

/*
|--------------------------------------------------------------------------
| Owner
|--------------------------------------------------------------------------
*/
Route::prefix('owner')->name('owner.')->group(function () {
    Route::middleware('guest:owner')->group(function () {
        Route::get('/', fn () => redirect()->route('owner.login'));
        Route::get('login', [OwnerAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [OwnerAuthController::class, 'login']);
        Route::get('register', [OwnerAuthController::class, 'showRegister'])->name('register');
        Route::post('register', [OwnerAuthController::class, 'register']);

    });

    Route::middleware('auth:owner')->group(function () {
        Route::get('/', fn () => redirect()->route('owner.dashboard'));
        Route::get('dashboard', function () {
            $stats = LaporanController::monthlySummary();
            $totalPegawai = Pegawai::count();
            $activeDiskons = Diskon::where('status_diskon', 'aktif')->count();

            return view('owner.dashboard', compact('stats', 'totalPegawai', 'activeDiskons'));
        })->name('dashboard');
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('profile', [OwnerController::class, 'editProfile'])->name('profile.edit');
        Route::put('updUsername', [OwnerController::class, 'updateUsername'])->name('update.username');
        Route::put('updPassword', [OwnerController::class, 'updatePassword'])->name('update.password');
        Route::post('logout', [OwnerAuthController::class, 'logout'])->name('logout');

        // Kelola
        Route::get('kelola-pegawai', [KelolaPegawaiController::class, 'listAll'])->name('kpegawai');
        Route::get('edit-pegawai/{id}', [KelolaPegawaiController::class, 'editPegawai'])->name('epegawai');
        Route::put('update-pegawai/{id}', [KelolaPegawaiController::class, 'updatePegawai'])->name('edpegawai');
        Route::get('add-pegawai', [KelolaPegawaiController::class, 'formTambahPgw'])->name('addpegawai');
        Route::post('add-pegawai', [KelolaPegawaiController::class, 'register']);
        Route::post('delete-pegawai/{id}', [KelolaPegawaiController::class, 'deletePegawai'])->name('delpegawai');

        Route::get('kelola-diskon', [KelolaDiskonController::class, 'listDiskons'])->name('kdiskon');
        Route::get('add-diskon', [KelolaDiskonController::class, 'tambahDiskon'])->name('adddiskon');
        Route::post('add-diskon', [KelolaDiskonController::class, 'addDiskon']);
        Route::get('edit-diskon/{id}', [KelolaDiskonController::class, 'editdiskon'])->name('ediskon');
        Route::put('update-diskon/{id}', [KelolaDiskonController::class, 'updateDiskon'])->name('eddiskon');
        Route::post('delete-diskon/{id}', [KelolaDiskonController::class, 'deleteDiskon'])->name('deldiskon');

    });
});

/*
|--------------------------------------------------------------------------
| Pegawai
|--------------------------------------------------------------------------
*/
Route::prefix('pegawai')->name('pegawai.')->group(function () {
    Route::middleware('guest:pegawai')->group(function () {
        Route::get('/', fn () => redirect()->route('pegawai.login'));
        Route::get('login', [PegawaiAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [PegawaiAuthController::class, 'login']);

    });

    Route::middleware('auth:pegawai')->group(function () {
        Route::get('/', fn () => redirect()->route('pegawai.dashboard'));
        Route::get('dashboard', function () {
            $pesananBaru = Checkout::with('items')
                ->where('status', 'paid')
                ->latest('id_checkout')
                ->get();

            return view('pegawai.dashboard', compact('pesananBaru'));
        })->name('dashboard');
        Route::post('logout', [PegawaiAuthController::class, 'logout'])->name('logout');
        Route::get('profile', [PegawaiController::class, 'editProfile'])->name('profile.edit');
        Route::put('updateNama', [PegawaiController::class, 'updateNama'])->name('update.nama');
        Route::put('updUsername', [PegawaiController::class, 'updateUsername'])->name('update.username');
        Route::put('updPassword', [PegawaiController::class, 'updatePassword'])->name('update.password');

        // Kelola
        Route::get('kelola-barang', [BarangController::class, 'listBarang'])->name('barang');
        Route::get('detail-barang/{id}', [BarangController::class, 'detailBarang'])->name('detailbarang');
        Route::get('add-barang', [BarangController::class, 'tambahBarang'])->name('abarang');
        Route::post('add-barang', [BarangController::class, 'addBarang'])->name('addbarang');
        Route::get('edit-barang/{id}', [BarangController::class, 'editBarang'])->name('ebarang');
        Route::put('update-barang/{id}', [BarangController::class, 'updateBarang'])->name('ubarang');
        Route::post('delete-barang/{id}', [BarangController::class, 'deleteBarang'])->name('delbarang');

        Route::get('kelola-kategori', [KategoriController::class, 'listKategoris'])->name('kategori');
        Route::get('add-kategori', [KategoriController::class, 'tambahKategori'])->name('akategori');
        Route::post('add-kategori', [KategoriController::class, 'addKategori']);
        Route::get('edit-kategori/{id}', [KategoriController::class, 'editKategori'])->name('ekategori');
        Route::put('update-kategori/{id}', [KategoriController::class, 'updateKategori'])->name('ukategori');
        Route::post('delete-kategori/{id}', [KategoriController::class, 'deleteKategori'])->name('delkategori');

        Route::get('kelola-brand', [BrandController::class, 'listBrands'])->name('kbrand');
        Route::get('add-brand', [BrandController::class, 'tambahBrand'])->name('abrand');
        Route::post('add-brand', [BrandController::class, 'addBrand']);
        Route::get('edit-brand/{id}', [BrandController::class, 'editBrand'])->name('ebrand');
        Route::put('update-brand/{id}', [BrandController::class, 'updateBrand'])->name('ubrand');
        Route::post('delete-brand/{id}', [BrandController::class, 'deleteBrand'])->name('delbrand');

        Route::get('stok-barang/{id}', [StokController::class, 'stokBarang'])->name('stok');
        Route::put('update-stok/{id}', [StokController::class, 'updateStok'])->name('ustok');
        Route::put('update-stok/{id_b}/{id_u}', [StokController::class, 'updateStokUkuran'])->name('ustoku');
        Route::put('update-harga-ukuran/{id_b}/{id_u}', [UkuranController::class, 'updateHargaUkuran'])->name('uhargau');
        Route::get('ukuran-barang/{id}', [UkuranController::class, 'listUkuran'])->name('ukuran');
        Route::get('add-ukuran/{id}', [UkuranController::class, 'tambahUkuran'])->name('addukuran');
        Route::post('add-ukuran/{id}', [UkuranController::class, 'addUkuran']);
        Route::get('edit-ukuran/{id_b}/{id_u}', [UkuranController::class, 'editUkuran'])->name('eukuran');
        Route::put('update-ukuran/{id_b}/{id_u}', [UkuranController::class, 'updateUkuran'])->name('uukuran');
        Route::post('delete-ukuran/{id}', [UkuranController::class, 'deleteUkuran'])->name('delukuran');

        // Pesanan
        Route::get('pesanan', [PesananController::class, 'listPesanan'])->name('pesanan');
        Route::get('pesanan/{id}', [PesananController::class, 'detailPesanan'])->name('detailpesanan');
        Route::post('pesanan/{id}/proses', [PesananController::class, 'proccessRequest'])->name('prosespesanan');
        Route::post('pesanan/{id}/kirim', [PesananController::class, 'kirim'])->name('kirimpesanan');
        Route::post('pesanan/{id}/cancel-approve', [PesananController::class, 'cancelApprove'])->name('cancelapprovepesanan');
        Route::post('pesanan/{id}/cancel-reject', [PesananController::class, 'cancelReject'])->name('cancelrejectpesanan');

    });
});

/*
|--------------------------------------------------------------------------
| Customer
|--------------------------------------------------------------------------
*/
Route::prefix('/')->group(function () {

    Route::middleware('guest:customer')->group(function () {
        Route::get('login', [CustomerAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [CustomerAuthController::class, 'login']);
        Route::get('register', [CustomerAuthController::class, 'showRegister'])->name('register');
        Route::post('register', [CustomerAuthController::class, 'register']);
        // Route::get('/', fn () => view('welcome'))->name('home');
    });

    Route::middleware('auth:customer')->group(function () {
        // Route::get('/', fn () => view('welcome'))->name('home');
        // Route::get('dashboard', fn () => view('customer.dashboard'))->name('dashboard');
        Route::get('profil', [CustomerController::class, 'profil'])->name('profil');
        Route::get('profil/ubah', [CustomerController::class, 'profilEdit'])->name('profil.edit');
        Route::put('profil/ubah/nama', [CustomerController::class, 'updateNama'])->name('profil.update.nama');
        Route::put('profil/ubah/email', [CustomerController::class, 'updateEmail'])->name('profil.update.email');
        Route::put('profil/ubah/telp', [CustomerController::class, 'updateTelp'])->name('profil.update.telp');
        Route::put('profil/ubah/username', [CustomerController::class, 'updateUsername'])->name('profil.update.username');

        Route::get('profil/password', [CustomerController::class, 'passwordEdit'])->name('password.edit');
        Route::put('profil/ubah/password', [CustomerController::class, 'passwordUpdate'])->name('password.update');
        Route::post('logout', [CustomerAuthController::class, 'logout'])->name('logout');

        // Member
        Route::get('member', [MemberController::class, 'show'])->name('membership.index');
        Route::get('member/mendaftar', [MemberController::class, 'subscribe'])->name('membership.subscribe');
        Route::post('member/token', [MemberController::class, 'token'])->name('membership.token');

        // Notifikasi diskon
        Route::get('notifikasi', [NotificationController::class, 'index'])->name('notifikasi.index');
        Route::post('notifikasi/baca', [NotificationController::class, 'markAllRead'])->name('notifikasi.read');

        // Keranjang
        Route::get('keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
        Route::post('keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
        Route::post('keranjang/checkout', [KeranjangController::class, 'checkoutSelected'])->name('keranjang.checkout');
        Route::put('keranjang/{id}', [KeranjangController::class, 'update'])->name('keranjang.update');
        Route::post('keranjang/{id}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');

        // Alamat
        Route::get('alamat', [AlamatController::class, 'index'])->name('alamat.index');
        Route::get('alamat/tambah', [AlamatController::class, 'create'])->name('alamat.create');
        Route::post('alamat', [AlamatController::class, 'store'])->name('alamat.store');
        Route::get('alamat/{id}/edit', [AlamatController::class, 'edit'])->name('alamat.edit');
        Route::put('alamat/{id}', [AlamatController::class, 'update'])->name('alamat.update');
        Route::post('alamat/{id}', [AlamatController::class, 'destroy'])->name('alamat.destroy');

        // Alamat: Klikresi province -> city -> district
        Route::get('alamat/cities/{id}', [AlamatController::class, 'cities'])->name('alamat.cities');
        Route::get('alamat/districts/{id}', [AlamatController::class, 'districts'])->name('alamat.districts');

        // Checkout
        Route::get('checkout', [CheckoutController::class, 'create'])->name('checkout.create');
        Route::post('checkout/rate', [CheckoutController::class, 'rate'])->name('checkout.rate');
        Route::post('checkout/diskon', [CheckoutController::class, 'diskon'])->name('checkout.diskon');
        Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('checkout/riwayat', [CheckoutController::class, 'history'])->name('checkout.history');
        Route::post('checkout/{id}/confirm', [CheckoutController::class, 'confirm'])->name('checkout.confirm');
        Route::post('checkout/{id}/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
        Route::get('checkout/{id}', [CheckoutController::class, 'show'])->name('checkout.show');
    });
});

// Midtrans payment notification (unauthenticated, CSRF-exempt)
Route::post('checkout/notification', [CheckoutController::class, 'notification'])->name('checkout.notification');
