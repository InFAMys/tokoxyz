<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    public const KEYS = [
        'nama_toko' => 'Nama Toko',
        'tagline' => 'Tagline',
        'deskripsi' => 'Deskripsi',
        'alamat' => 'Alamat',
        'no_telp' => 'No. Telepon',
        'email' => 'Email',
        'jam_buka' => 'Jam Operasional',
        'instagram' => 'Instagram',
        'facebook' => 'Facebook',
        'tiktok' => 'TikTok',
    ];

    protected $primaryKey = 'id_pengaturan';

    public $timestamps = false;

    protected $fillable = ['nama_kunci', 'nilai'];

    public static function allAsArray(): array
    {
        return self::pluck('nilai', 'nama_kunci')->toArray();
    }

    public static function nilai(string $key, string $default = ''): string
    {
        return self::where('nama_kunci', $key)->value('nilai') ?? $default;
    }

    public static function upsertNilai(array $values): void
    {
        foreach ($values as $key => $value) {
            self::updateOrCreate(['nama_kunci' => $key], ['nilai' => $value]);
        }
    }
}
