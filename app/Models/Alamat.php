<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'id_cst');
    }
}
