<?php

namespace App\Notifications;

use App\Models\Diskon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DiscountAvailable extends Notification
{
    use Queueable;

    public function __construct(public Diskon $diskon) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function databaseType(object $notifiable): string
    {
        return 'discount-available';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id_diskon' => $this->diskon->id_diskon,
            'nama_diskon' => $this->diskon->nama_diskon,
            'kode_diskon' => $this->diskon->kode_diskon,
            'jumlah_diskon' => $this->diskon->jumlah_diskon,
            'mulai_diskon' => $this->diskon->mulai_diskon,
            'akhir_diskon' => $this->diskon->akhir_diskon,
        ];
    }
}
