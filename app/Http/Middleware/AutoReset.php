<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AutoResetService;
use Illuminate\Support\Facades\Session;

class AutoReset
{
    public function handle(Request $request, Closure $next)
    {
        if (Session::get('id_karyawan')) {
            AutoResetService::jalankan();
        }

        return $next($request);
    }
}