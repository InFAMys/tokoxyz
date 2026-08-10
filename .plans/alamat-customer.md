# Fitur Alamat Customer

## Tujuan
Customer simpan banyak alamat. Table `alamat` sudah migrasi:
PK `id_alamat`, FK `id_cst`->customers.id_cst (cascade), softDeletes.

Field: nama_alamat, nama_penerima, telp_penerima, detail_alamat,
kecamatan, kelurahan, kota, provinsi, kode_pos.

## File baru
1. app/Models/Alamat.php            $primaryKey=id_alamat, fillable,
                                    belongsTo(Customer,'id_cst')
2. app/Http/Controllers/Customer/AlamatController.php
   index/create/store/edit/update/destroy, guard customer, scope sbh
   auth('customer')->user() biar anti-IDOR
3. resources/views/customer/alamat/index.blade.php   daftar kartu
4. resources/views/customer/alamat/form.blade.php    form create+edit sama

## File ubah
5. app/Models/Customer.php          tambah hasMany(Alamat,'id_cst')
6. routes/web.php                   grup auth:customer:
   GET /alamat                    alamat.index
   GET /alamat/tambah             alamat.create
   POST /alamat                   alamat.store
   GET /alamat/{alamat}/edit      alamat.edit
   PUT /alamat/{alamat}           alamat.update
   POST /alamat/{alamat}          alamat.destroy
7. profil.blade.php:18             link '#' -> route('alamat.index')
8. profile/edit + editpassword     kalau ada link '#'

## Validasi store/update
semua kolom required string; telp regex /^[0-9\-]{9,12}$/ min8 max12;
kode_pos max 10. Pesan error bahasa Indonesia (ikut gaya buat)

## Verifikasi
php artisan route:list --name=alamat
ujicoba manual browser
vendor/bin/pint --format agent
