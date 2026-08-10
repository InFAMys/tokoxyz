<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // An unauthenticated visitor hitting /admin/... goes to the admin
        // login, /employee/... to the employee login, everything else to
        // the customer login. This replaces the single default 'login'
        // redirect since we now have 3 separate guards/login pages.
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('owner') || $request->is('owner/*')) {
                return route('owner.login');
            }

            if ($request->is('pegawai') || $request->is('pegawai/*')) {
                return route('pegawai.login');
            }

            return route('login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->is('owner') || $request->is('owner/*')) {
                return route('owner.dashboard');
            }

            if ($request->is('pegawai') || $request->is('pegawai/*')) {
                return route('pegawai.dashboard');
            }

            return route('home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();