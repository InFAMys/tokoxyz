<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Table(key: 'id_barang')]
class Barang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'barangs';

    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'id_brand',
        'id_kategori',
        'kode_barang',
        'nama_barang',
        'deskripsi',
        'foto',
        'thumbnail',
        'harga',
        'berat',
        'stok',
        'status',
        'preorder',
        'estimasi_preorder',
    ];

    protected function casts(): array
    {
        return [
            'foto' => 'array',
            'harga' => 'decimal:2',
            'berat' => 'decimal:3',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'id_brand', 'id_brand');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function ukurans(): HasMany
    {
        return $this->hasMany(Ukuran::class, 'id_barang', 'id_barang');     // (Foreign Key,Primary Key)
    }

    public function keranjangs(): HasMany
    {
        return $this->hasMany(Keranjang::class, 'id_barang', 'id_barang');
    }

    public function stokReady(): int
    {
        return $this->ukurans->isNotEmpty()
            ? $this->ukurans->sum('stok_ukuran')
            : $this->stok;
    }

    public function thumbnailPath(): ?string
    {
        return $this->thumbnail ?? $this->foto[0] ?? null;
    }
}
