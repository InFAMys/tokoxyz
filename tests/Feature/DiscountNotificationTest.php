<?php

use App\Models\Customer;
use App\Models\Diskon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

it('notifies customers once per active discount', function () {
    DB::beginTransaction();

    try {
        $u = substr(uniqid(), -6);
        $customer = Customer::create([
            'nama' => 'Notif Test',
            'username' => 'notiftest'.$u,
            'email' => 'notif'.$u.'@test.dev',
            'no_telp' => '081'.$u,
            'password' => 'password',
        ]);

        $diskon = Diskon::create([
            'nama_diskon' => 'Ramadan',
            'jumlah_diskon' => 20,
            'kode_diskon' => 'RAN'.$u,
            'mulai_diskon' => now()->subDay(),
            'akhir_diskon' => now()->addWeek(),
            'status_diskon' => 'aktif',
        ]);

        Artisan::call('notify:discounts');

        expect(DB::table('notifications')->where('notifiable_id', $customer->id_cst)->count())->toBe(1);
        expect($diskon->refresh()->notified_at)->not->toBeNull();

        Artisan::call('notify:discounts');

        expect(DB::table('notifications')->where('notifiable_id', $customer->id_cst)->count())->toBe(1);
    } finally {
        DB::rollBack();
    }
});

it('skips non-active discounts', function () {
    DB::beginTransaction();

    try {
        $u = substr(uniqid(), -6);
        $customer = Customer::create([
            'nama' => 'Notif Test 2',
            'username' => 'notiftest2'.$u,
            'email' => 'notif2'.$u.'@test.dev',
            'no_telp' => '082'.$u,
            'password' => 'password',
        ]);

        Diskon::create([
            'nama_diskon' => 'Kadaluarsa',
            'jumlah_diskon' => 10,
            'kode_diskon' => 'EXP'.$u,
            'mulai_diskon' => now()->subMonth(),
            'akhir_diskon' => now()->subDay(),
            'status_diskon' => 'aktif',
        ]);

        Artisan::call('notify:discounts');

        expect(DB::table('notifications')->where('notifiable_id', $customer->id_cst)->count())->toBe(0);
    } finally {
        DB::rollBack();
    }
});
