<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;


#[Table(key: 'id_kategori')]
class Kategori extends Model
{
    use HasFactory, Notifiable, SoftDeletes;
    
    protected $table = 'kategoris';
    
    protected $fillable = [
        'nama_kategori',
    ];
}