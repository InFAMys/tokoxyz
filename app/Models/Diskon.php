<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

#[Table(key: 'id_diskon')]
class Diskon extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'diskons';

    protected $fillable = [
        'nama_diskon',
        'jumlah_diskon',
        'kode_diskon',
        'mulai_diskon',
        'akhir_diskon',
        'status_diskon',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_diskon' => 'decimal:2',
        ];
    }

    public static function statusOptions(): array
    {
        return self::enumOptions('status_diskon');
    }

    protected static function enumOptions(string $column): array
    {
        $table = (new self)->getTable();
        $row = DB::selectOne('SHOW COLUMNS FROM `'.$table.'` WHERE `Field` = ?', [$column]);

        if (! $row || ! preg_match("/^enum\((.*)\)$/", $row->Type, $matches)) {
            return [];
        }

        return collect(explode(',', $matches[1]))
            ->map(fn ($value) => trim($value, "'"))
            ->values()
            ->all();
    }
}
