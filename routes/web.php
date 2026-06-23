<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\MisiController;
use App\Http\Controllers\ApproveMisiController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\PengumpulanController;
use App\Http\Controllers\KaryawanBerandaController;
use App\Http\Controllers\KaryawanAbsensiController;
use App\Http\Controllers\KaryawanMisiController;
use App\Http\Controllers\KaryawanTugasController;
use App\Http\Controllers\DashboardAtasanController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\KaryawanAkunController;
use App\Http\Controllers\AdminRekapController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'login'])
    ->name('login');

Route::post('/login-proses', [AuthController::class, 'loginProses'])
    ->name('login.proses');

Route::get('/logout', [AuthController::class, 'logout'])
    ->name('logout');


Route::get('/dashboard-admin', [DashboardController::class, 'index']);

Route::get('/data-karyawan', [KaryawanController::class, 'index']);
Route::get('/data-karyawan/tambah', [KaryawanController::class, 'create']);
Route::post('/data-karyawan/tambah', [KaryawanController::class, 'store']);
Route::get('/data-karyawan/edit/{id}', [KaryawanController::class, 'edit']);
Route::put('/data-karyawan/edit/{id}', [KaryawanController::class, 'update']);
Route::delete('/data-karyawan/hapus/{id}', [KaryawanController::class, 'destroy']);

Route::get('/data-divisi', [DivisiController::class, 'index']);
Route::get('/data-divisi/tambah', [DivisiController::class, 'create']);
Route::post('/data-divisi/tambah', [DivisiController::class, 'store']);
Route::get('/data-divisi/edit/{id}', [DivisiController::class, 'edit']);
Route::put('/data-divisi/edit/{id}', [DivisiController::class, 'update']);
Route::delete('/data-divisi/hapus/{id}', [DivisiController::class, 'destroy']);

Route::get('/absensi', [AbsensiController::class, 'index']);
Route::delete('/absensi/hapus/{id}', [AbsensiController::class, 'destroy']);
Route::get('/absensi/{id}', [AbsensiController::class, 'show']);

// Kelola Misi
Route::get('/kelola-misi', [MisiController::class, 'index']);
Route::get('/kelola-misi/tambah', [MisiController::class, 'create']);
Route::post('/kelola-misi/tambah', [MisiController::class, 'store']);
Route::get('/kelola-misi/edit/{id}', [MisiController::class, 'edit']);
Route::put('/kelola-misi/edit/{id}', [MisiController::class, 'update']);
Route::delete('/kelola-misi/hapus/{id}', [MisiController::class, 'destroy']);

// Approve Misi
Route::get('/approve-misi', [ApproveMisiController::class, 'index']);
Route::get('/approve-misi/{id}', [ApproveMisiController::class, 'show']);
Route::post('/approve-misi/{id}/approve', [ApproveMisiController::class, 'approve']);
Route::post('/approve-misi/{id}/reject', [ApproveMisiController::class, 'reject']);
Route::delete('/approve-misi/hapus/{id}', [ApproveMisiController::class, 'destroy']);

// Kelola Tugas
Route::get('/kelola-tugas', [TugasController::class, 'index']);
Route::get('/kelola-tugas/tambah', [TugasController::class, 'create']);
Route::post('/kelola-tugas/tambah', [TugasController::class, 'store']);
Route::get('/kelola-tugas/edit/{id}', [TugasController::class, 'edit']);
Route::put('/kelola-tugas/edit/{id}', [TugasController::class, 'update']);
Route::delete('/kelola-tugas/hapus/{id}', [TugasController::class, 'destroy']);

// Approve Tugas / Pengumpulan
Route::get('/approve-tugas', [PengumpulanController::class, 'index']);
Route::get('/approve-tugas/{id}', [PengumpulanController::class, 'show']);
Route::post('/approve-tugas/{id}/approve', [PengumpulanController::class, 'approve']);
Route::post('/approve-tugas/{id}/reject', [PengumpulanController::class, 'reject']);
Route::delete('/approve-tugas/hapus/{id}', [PengumpulanController::class, 'destroy']);
Route::get('/approve-tugas/{id}/file', [PengumpulanController::class, 'lihatFile']);

Route::get('/dashboard-karyawan', [KaryawanBerandaController::class, 'beranda']);
Route::get('/absensi-karyawan', [KaryawanAbsensiController::class, 'index']);
Route::post('/absensi-karyawan/simpan', [KaryawanAbsensiController::class, 'simpan']);

Route::get('/aktivitas-misi', [KaryawanMisiController::class, 'index']);
Route::get('/aktivitas-misi/{id}', [KaryawanMisiController::class, 'show']);
Route::post('/aktivitas-misi/{id}/upload', [KaryawanMisiController::class, 'upload']);

Route::get('/tugas-mingguan', [KaryawanTugasController::class, 'index']);
Route::get('/tugas-mingguan/{id}', [KaryawanTugasController::class, 'show']);
Route::post('/tugas-mingguan/{id}/upload', [KaryawanTugasController::class, 'upload']);
Route::get('/tugas-mingguan/{id}/file', [KaryawanTugasController::class, 'lihatFile']);

// Dashboard Atasan
Route::get('/dashboard-atasan', [DashboardAtasanController::class, 'index'])->name('atasan.dashboard');

// =====================================================
// REWARD — urutan PENTING: spesifik dulu, {id} belakang
// =====================================================
Route::get('/reward-atasan', [RewardController::class, 'index'])->name('reward.index');
Route::get('/reward-atasan/tambah', [RewardController::class, 'create'])->name('reward.create');
Route::post('/reward-atasan/tambah', [RewardController::class, 'store'])->name('reward.store');
Route::get('/reward-atasan/edit/{id}', [RewardController::class, 'edit'])->name('reward.edit');
Route::put('/reward-atasan/edit/{id}', [RewardController::class, 'update'])->name('reward.update');
Route::delete('/reward-atasan/hapus/{id}', [RewardController::class, 'destroy'])->name('reward.destroy');
Route::get('/reward-atasan/{id}', [RewardController::class, 'detail'])->name('reward.detail'); // ← paling bawah

Route::get('/pelanggaran-atasan', [PelanggaranController::class, 'index'])->name('pelanggaran.index');
Route::post('/pelanggaran-atasan/{id}/upload-sp', [PelanggaranController::class, 'uploadSp'])->name('pelanggaran.uploadSp');
Route::delete('/pelanggaran-atasan/{id}/delete-sp', [PelanggaranController::class, 'deleteSp'])->name('pelanggaran.deleteSp');

Route::get('/akun-karyawan', [KaryawanAkunController::class, 'index'])->name('karyawan.akun.index');
Route::get('/akun-karyawan/unduh', [KaryawanAkunController::class, 'unduh'])->name('karyawan.akun.unduh');
Route::get('/akun-karyawan/reward', [KaryawanAkunController::class, 'reward'])->name('karyawan.akun.reward');
Route::get('/akun-karyawan/pelanggaran', [KaryawanAkunController::class, 'pelanggaran'])->name('karyawan.akun.pelanggaran');

Route::get('/karyawan/akun/reward/sertifikat/{id}', [KaryawanAkunController::class, 'unduhSertifikat'])->name('karyawan.akun.reward.sertifikat');
Route::get('/karyawan/akun/pelanggaran', [KaryawanAkunController::class, 'pelanggaran'])->name('karyawan.akun.pelanggaran');
Route::post('/karyawan/akun/pelanggaran/{id}/upload', [KaryawanAkunController::class, 'uploadSpKaryawan'])->name('karyawan.akun.pelanggaran.upload');

Route::get('/akun-karyawan/cetak-pdf', [KaryawanAkunController::class, 'cetakPdf'])->name('karyawan.akun.cetak_pdf');
Route::get('/akun-karyawan/cetak-excel', [KaryawanAkunController::class, 'cetakExcel'])->name('karyawan.akun.cetak_excel');

Route::get('/admin/rekap-kinerja', [AdminRekapController::class, 'index'])->name('admin.rekap.index');
Route::get('/admin/rekap-kinerja/download', [AdminRekapController::class, 'downloadPdf'])->name('admin.rekap.download');