<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(key: 'id_keranjang')]
class Keranjang extends Model
{
    protected $table = 'keranjang';

    protected $primaryKey = 'id_keranjang';

    protected $fillable = [
        'id_cst',
        'id_barang',
        'id_ukuran',
        'jumlah_barang',
    ];

    protected $attributes = [
        'jumlah_barang' => 1,
    ];

    protected function casts(): array
    {
        return [
            'jumlah_barang' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'id_cst', 'id_cst');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function ukuran(): BelongsTo
    {
        return $this->belongsTo(Ukuran::class, 'id_ukuran', 'id_ukuran');
    }
}
