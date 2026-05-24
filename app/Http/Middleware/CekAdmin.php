<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CekAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Cukup cek apakah id_role yang login saat ini adalah 1 (Admin)
        if (Session::get('id_role') != 1) {
            return redirect('/login')->with('error', 'Anda tidak memiliki akses ke halaman Admin.');
        }

        return $next($request);
    }
}
