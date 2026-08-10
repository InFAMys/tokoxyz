<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;


#[Table(key: 'id_brand')]
class Brand extends Model
{
    use HasFactory, Notifiable, SoftDeletes;
    
    protected $table = 'brands';

    protected $fillable = [
        'nama_brand',
        'logo',
    ];
}