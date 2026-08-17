<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Diskon;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    public function index()
    {
        $activeIds = Diskon::where('status_diskon', 'aktif')
            ->where('mulai_diskon', '<=', now())
            ->where('akhir_diskon', '>=', now())
            ->pluck('id_diskon')
            ->all();

        $notifications = auth('customer')->user()
            ->notifications()
            ->orderByDesc('created_at')
            ->get()
            ->filter(fn ($n) => in_array($n->data['id_diskon'] ?? null, $activeIds));

        $notifications->markAsRead();

        return view('customer.notifikasi.index', compact('notifications'));
    }

    public function markAllRead(): RedirectResponse
    {
        auth('customer')->user()->unreadNotifications->markAsRead();

        return redirect()->route('notifikasi.index');
    }
}
