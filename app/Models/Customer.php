<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(key: 'id_cst')]
class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'customers';

    protected $fillable = [
        'nama',
        'username',
        'email',
        'no_telp',
        'password',
    ];

    protected $hidden = [
        'password',
        'member',
        'member_since',
        'remember_token',
  
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function alamats(): HasMany
    {
        return $this->hasMany(Alamat::class, 'id_cst');
    }

    public function keranjangs(): HasMany
    {
        return $this->hasMany(Keranjang::class, 'id_cst', 'id_cst');
    }
}
