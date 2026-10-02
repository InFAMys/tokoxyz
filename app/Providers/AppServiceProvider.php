<?php

namespace App\Providers;

use App\Models\Pengaturan;
use App\Services\FakeKlikresiApi;
use App\Services\KlikresiApi;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(KlikresiApi::class, function ($app) {
            return $app['config']->get('services.klikresi.tracking_fake', false)
                ? new FakeKlikresiApi
                : new KlikresiApi;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::share('namaToko', Pengaturan::nilai('nama_toko') ?: 'Toko XYZ');
    }
}
