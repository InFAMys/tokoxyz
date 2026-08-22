<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(key: 'id_checkout')]
class Checkout extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'id_cst',
        'id_pegawai',
        'id_alamat',
        'order_id',
        'customer_name',
        'customer_email',
        'customer_telp',
        'subtotal',
        'diskon_nominal',
        'member_diskon_nominal',
        'shipping_cost',
        'total_amount',
        'berat_total',
        'shipping_courier',
        'shipping_service',
        'shipping_address',
        'kode_diskon',
        'status',
        'snap_token',
        'payment_type',
        'paid_at',
        'no_resi',
        'delivered_at',
        'cancel_reason',
        'cancel_from',
        'cancel_response',
        'cancel_requested_at',
        'kritik_saran',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'diskon_nominal' => 'decimal:2',
            'member_diskon_nominal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'berat_total' => 'decimal:2',
            'paid_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancel_requested_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'id_cst', 'id_cst');
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function alamat(): BelongsTo
    {
        return $this->belongsTo(Alamat::class, 'id_alamat', 'id_alamat');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CheckoutItem::class, 'id_checkout', 'id_checkout');
    }

    /** @return array<int, string> */
    public static function cancellableStatuses(): array
    {
        return ['pending', 'paid', 'processed'];
    }

    public function isFinal(): bool
    {
        return in_array($this->status, ['completed', 'expired', 'cancelled', 'refunded', 'partially_refunded', 'deny'], true);
    }

    public const STATUSES = [
        'pending' => 'Menunggu Pembayaran',
        'paid' => 'Menunggu Konfirmasi',
        'expired' => 'Kadaluarsa',
        'cancelled' => 'Dibatalkan',
        'refunded' => 'Dana Dikembalikan',
        'partially_refunded' => 'Dana Dikembalikan Sebagian',
        'deny' => 'Ditolak',
        'processed' => 'Diproses',
        'shipping' => 'Dalam Pengiriman',
        'delivered' => 'Sampai Di Tujuan',
        'completed' => 'Selesai',
        'cancel_pending' => 'Menunggu Pembatalan',
    ];

    private const COLORS = [
        'pending' => 'warning',
        'paid' => 'warning',
        'expired' => 'secondary',
        'cancelled' => 'secondary',
        'refunded' => 'secondary',
        'partially_refunded' => 'secondary',
        'deny' => 'danger',
        'processed' => 'primary',
        'shipping' => 'info',
        'delivered' => 'success',
        'completed' => 'dark',
        'cancel_pending' => 'danger',
    ];

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function statusColor(): string
    {
        return self::COLORS[$this->status] ?? 'secondary';
    }

    /** @return array<int, array{id_ukuran?: int|null, id_barang: int, jumlah_barang: int}> */
    public function restoreStock(): void
    {
        foreach ($this->items ?? collect() as $item) {
            if ($item->is_preorder) {
                continue;
            }

            $jumlah = (int) $item->jumlah_barang;

            if ($item->id_ukuran) {
                $ukuran = Ukuran::find($item->id_ukuran);

                if ($ukuran) {
                    $ukuran->stok_ukuran = (int) $ukuran->stok_ukuran + $jumlah;
                    $ukuran->save();
                }
            } else {
                $barang = Barang::find($item->id_barang);

                if ($barang) {
                    $barang->stok = (int) $barang->stok + $jumlah;
                    $barang->save();
                }
            }
        }
    }
}
