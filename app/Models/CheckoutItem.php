<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(key: 'id_checkout_item')]
class CheckoutItem extends Model
{
    protected $table = 'checkout_items';

    protected $fillable = [
        'id_checkout',
        'id_barang',
        'id_ukuran',
        'nama_barang',
        'ukuran_name',
        'unit_price',
        'jumlah_barang',
        'subtotal',
        'berat',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'berat' => 'decimal:2',
            'jumlah_barang' => 'integer',
        ];
    }

    public function checkout(): BelongsTo
    {
        return $this->belongsTo(Checkout::class, 'id_checkout', 'id_checkout');
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
