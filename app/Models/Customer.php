<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notifiable;

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
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'member_since' => 'datetime',
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

    public function checkouts(): HasMany
    {
        return $this->hasMany(Checkout::class, 'id_cst', 'id_cst');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class, 'id_cst', 'id_cst');
    }

    /**
     * Unread discount notifications whose discount is still active (not expired).
     *
     * @return Collection<int, DatabaseNotification>
     */
    public function unreadActiveDiscountNotifications(): Collection
    {
        $activeIds = Diskon::where('status_diskon', 'aktif')
            ->where('mulai_diskon', '<=', now())
            ->where('akhir_diskon', '>=', now())
            ->pluck('id_diskon');

        return $this->notifications()
            ->where('type', 'discount-available')
            ->whereNull('read_at')
            ->get()
            ->filter(fn ($n) => $activeIds->contains((int) ($n->data['id_diskon'] ?? 0)))
            ->values();
    }
}
