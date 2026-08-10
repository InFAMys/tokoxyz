<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Notifications\Notifiable;

#[Table(key: 'id_owner')]
class Owner extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'owners';

    protected $fillable = [
        'username',
        'password',
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
}