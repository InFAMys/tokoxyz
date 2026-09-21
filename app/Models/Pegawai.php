<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Table(key: 'id_pegawai')]
class Pegawai extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'pegawais';

    protected $fillable = [
        'nama_pegawai',
        'username_pegawai',
        'password',
        'akses',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function canInventory(): bool
    {
        return $this->akses === 'inventaris';
    }

    public function aksesLabel(): string
    {
        return $this->canInventory() ? 'Pesanan + Inventaris' : 'Hanya Pesanan';
    }
}
