<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alamat extends Model
{
    use SoftDeletes;

    protected $table = 'alamat';

    protected $primaryKey = 'id_alamat';

    protected $fillable = [
        'id_cst',
        'nama_alamat',
        'nama_penerima',
        'telp_penerima',
        'detail_alamat',
        'kecamatan',
        'kelurahan',
        'kota',
        'provinsi',
        'kode_pos',
        'id_provinsi',
        'id_kota',
        'id_kecamatan',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'id_cst');
    }
}
