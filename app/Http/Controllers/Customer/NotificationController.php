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
            ->where('type', 'discount-available')
            ->whereIn('data->id_diskon', $activeIds)
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $notifications->getCollection()->markAsRead();

        return view('customer.notifikasi.index', compact('notifications'));
    }

    public function markAllRead(): RedirectResponse
    {
        auth('customer')->user()->unreadNotifications->markAsRead();

        return redirect()->route('notifikasi.index');
    }
}
