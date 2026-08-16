<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table(key: 'id_ukuran')]
class Ukuran extends Model
{
    use SoftDeletes;

    protected $table = 'ukurans';

    protected $fillable = [
        'id_barang',
        'nama_ukuran',
        'ukuran',
        'harga_ukuran',
        'stok_ukuran',
    ];

    protected function casts(): array
    {
        return [
            'harga_ukuran' => 'decimal:2',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');       // (Foreign Key,Primary Key)
    }

    public function keranjangs(): HasMany
    {
        return $this->hasMany(Keranjang::class, 'id_ukuran', 'id_ukuran');
    }
}
