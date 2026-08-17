<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(key: 'id_membership')]
class Membership extends Model
{
    protected $table = 'member';

    protected $fillable = [
        'id_cst',
        'order_id',
        'nominal',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'id_cst', 'id_cst');
    }
}
