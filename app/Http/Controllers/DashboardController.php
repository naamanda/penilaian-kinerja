<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Divisi;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKaryawan = Karyawan::count();
        $totalDivisi = Divisi::count();

        return view('admin.dashboard', compact('totalKaryawan', 'totalDivisi'));
    }
}