<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function loginProses(Request $request)
    {
        // 1. Cari user berdasarkan username & password
        $user = Karyawan::where('username', $request->username)
            ->where('password', md5($request->password))
            ->first();

        if ($user) {
            // 2. Simpan data ke session
            Session::put('id_karyawan', $user->id_karyawan);
            Session::put('nama', $user->nama);
            Session::put('id_role', $user->id_role);

            // Menjalankan service auto reset
            if (class_exists('\App\Services\AutoResetService')) {
                \App\Services\AutoResetService::jalankan();
            }

            // 3. Redirect langsung berdasarkan id_role-nya
            if ($user->id_role == 1) {
                return redirect('/dashboard-admin');
            } elseif ($user->id_role == 2) {
                return redirect('/dashboard-karyawan');
            } elseif ($user->id_role == 3) {
                return redirect('/dashboard-atasan');
            }
        }

        // Jika user tidak ditemukan
        return back()->with('error', 'Username atau password salah');
    }

    public function logout()
    {
        // Hapus semua data login yang disimpan di session
        Session::forget('id_karyawan');
        Session::forget('nama');
        Session::forget('id_role');

        return redirect('/login');
    }
}