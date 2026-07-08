<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CekKaryawan
{
    public function handle(Request $request, Closure $next)
    {
        if (Session::get('id_role') != 2) {
            return redirect('/login')->with('error', 'Anda tidak memiliki akses ke halaman Karyawan.');
        }

        return $next($request);
    }
}
