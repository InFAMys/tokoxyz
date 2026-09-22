<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Diskon;

class DiskonController extends Controller
{
    public function index()
    {
        $diskons = Diskon::where('status_diskon', 'aktif')
            ->where('mulai_diskon', '<=', now())
            ->where('akhir_diskon', '>=', now())
            ->orderByDesc('id_diskon')
            ->get();

        $user = auth('customer')->user();

        $unread = $user->unreadActiveDiscountNotifications();

        $notifiedIds = $unread->map(fn ($n) => (int) ($n->data['id_diskon'] ?? 0))->all();

        $unread->each->markAsRead();

        return view('customer.diskon.index', compact('diskons', 'notifiedIds'));
    }
}
