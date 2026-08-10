<?php

use App\Http\Controllers\Auth\OwnerAuthController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\PegawaiAuthController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\AlamatController;
use App\Http\Controllers\Customer\KeranjangController;
use App\Http\Controllers\Owner\KelolaDiskonController;
use App\Http\Controllers\Owner\KelolaPegawaiController;
use App\Http\Controllers\Owner\OwnerController;
use App\Http\Controllers\Pegawai\BarangController;
use App\Http\Controllers\Pegawai\PegawaiController;
use App\Http\Controllers\Pegawai\BrandController;
use App\Http\Controllers\Pegawai\KategoriController;
use App\Http\Controllers\Pegawai\StokController;
use App\Http\Controllers\Pegawai\UkuranController;
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
        Route::get('dashboard', fn () => view('owner.dashboard'))->name('dashboard');
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
        Route::get('dashboard', fn () => view('pegawai.dashboard'))->name('dashboard');
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

        // Keranjang
        Route::get('keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
        Route::post('keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
        Route::put('keranjang/{id}', [KeranjangController::class, 'update'])->name('keranjang.update');
        Route::post('keranjang/{id}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');

        // Alamat
        Route::get('alamat', [AlamatController::class, 'index'])->name('alamat.index');
        Route::get('alamat/tambah', [AlamatController::class, 'create'])->name('alamat.create');
        Route::post('alamat', [AlamatController::class, 'store'])->name('alamat.store');
        Route::get('alamat/{id}/edit', [AlamatController::class, 'edit'])->name('alamat.edit');
        Route::put('alamat/{id}', [AlamatController::class, 'update'])->name('alamat.update');
        Route::post('alamat/{id}', [AlamatController::class, 'destroy'])->name('alamat.destroy');
    });
});
