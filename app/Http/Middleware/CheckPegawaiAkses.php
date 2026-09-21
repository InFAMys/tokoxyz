<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPegawaiAkses
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('pegawai')->user()?->canInventory()) {
            abort(403, 'Anda tidak memiliki akses ke inventaris.');
        }

        return $next($request);
    }
}
