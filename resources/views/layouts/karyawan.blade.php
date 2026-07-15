<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>LifeSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="bg-gray-100 text-gray-800 antialiased">

    <div class="min-h-screen w-full flex flex-col md:flex-row">

        {{-- 1. SIDEBAR LAPTOP (Hanya muncul di Laptop, tersembunyi di HP) --}}
        <aside class="hidden md:flex md:w-64 bg-[#1e3f7c] text-white flex-col fixed h-full z-50 border-r border-blue-900 shadow-xl">
            <div class="h-16 flex items-center px-6 text-xl font-bold border-b border-blue-800 tracking-wide">
                LifeSync
            </div>
            <nav class="flex-1 p-4 space-y-1">
                <a href="/dashboard-karyawan" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->is('dashboard-karyawan') ? 'bg-white text-[#1e3f7c] font-bold shadow-md' : 'text-gray-200 hover:bg-blue-800' }}">
                    Beranda
                </a>
                <a href="/absensi-karyawan" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->is('absensi-karyawan') ? 'bg-white text-[#1e3f7c] font-bold shadow-md' : 'text-gray-200 hover:bg-blue-800' }}">
                    Absensi
                </a>
                <a href="/izin-karyawan" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->is('izin-karyawan') ? 'bg-white text-[#1e3f7c] font-bold shadow-md' : 'text-gray-200 hover:bg-blue-800' }}">
                    Izin
                </a>
                <a href="/aktivitas-misi" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->is('aktivitas-misi') ? 'bg-white text-[#1e3f7c] font-bold shadow-md' : 'text-gray-200 hover:bg-blue-800' }}">
                    Aktivitas
                </a>
                <a href="/tugas-mingguan" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->is('tugas-mingguan') ? 'bg-white text-[#1e3f7c] font-bold shadow-md' : 'text-gray-200 hover:bg-blue-800' }}">
                    Tugas Mingguan
                </a>
                <a href="/akun-karyawan" class="flex items-center px-4 py-3 rounded-xl transition {{ request()->is('akun-karyawan') ? 'bg-white text-[#1e3f7c] font-bold shadow-md' : 'text-gray-200 hover:bg-blue-800' }}">
                    Akun
                </a>
            </nav>
        </aside>

        {{-- 2. AREA KONTEN UTAMA --}}
        <main class="flex-1 w-full md:pl-64 pb-20 md:pb-0 min-h-screen flex flex-col">

            <div class="w-full flex-1 flex flex-col md:bg-gray-50">

                {{-- TOPBAR KHUSUS DESKTOP (Dinamis Berdasarkan Session Login) --}}
                <header class="hidden md:flex h-16 w-full bg-white border-b border-gray-200 items-center justify-between px-8 shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-400">Halaman /</span>
                        <span class="text-sm font-bold text-gray-800 capitalize">
                            {{ str_replace('-', ' ', request()->segment(1) ?? 'Beranda') }}
                        </span>
                    </div>

                    @php
                    // Ambil id_karyawan dari Session
                    $idKaryawanLog = Session::get('id_karyawan');
                    // Tarik data nama asli karyawan dari Database
                    $karyawanLog = \App\Models\Karyawan::find($idKaryawanLog);

                    // Atur fallback jika seandainya session kosong / tidak ditemukan
                    $nama = $karyawanLog ? $karyawanLog->nama : 'Karyawan';
                    $inisial = $karyawanLog ? strtoupper(substr($karyawanLog->nama, 0, 1)) : 'K';
                    @endphp

                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-600">{{ $nama }}</span>
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm uppercase">
                            {{ $inisial }}
                        </div>
                    </div>
                </header>

                {{-- WRAPPER KONTEN --}}
                <div class="w-full max-w-full md:p-8 flex flex-col flex-1">

                    <div class="w-full flex flex-col flex-1 bg-transparent md:bg-white md:rounded-2xl md:shadow-sm md:p-6 md:border md:border-gray-200">
                        @yield('content')
                    </div>

                </div>

            </div>

        </main>

        {{-- 3. NAVIGASI HP: Bottom Nav (Hanya muncul di HP) --}}
        <nav class="md:hidden fixed bottom-0 left-0 right-0 w-full bg-white border-t border-gray-200 z-50 shadow-[0_-4px_10px_rgba(0,0,0,0.03)]">
            <div class="flex justify-around items-center py-2.5">
                <a href="/dashboard-karyawan" class="flex flex-col items-center gap-1 px-2 {{ request()->is('dashboard-karyawan') ? 'text-[#1e3f7c]' : 'text-gray-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="9 22 9 12 15 12 15 22" />
                    </svg>
                    <span class="text-[10px] {{ request()->is('dashboard-karyawan') ? 'font-bold' : 'font-medium' }}">Beranda</span>
                </a>
                <a href="/absensi-karyawan" class="flex flex-col items-center gap-1 px-2 {{ request()->is('absensi-karyawan') ? 'text-[#1e3f7c]' : 'text-gray-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="20 6 9 17 4 12" />
                    </svg>
                    <span class="text-[10px] {{ request()->is('absensi-karyawan') ? 'font-bold' : 'font-medium' }}">Absensi</span>
                </a>
                <a href="/izin-karyawan" class="flex flex-col items-center gap-1 px-2 {{ request()->is('izin-karyawan') ? 'text-[#1e3f7c]' : 'text-gray-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M9 8h1M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span class="text-[10px] {{ request()->is('izin-karyawan') ? 'font-bold' : 'font-medium' }}">Izin</span>
                </a>
                <a href="/aktivitas-misi" class="flex flex-col items-center gap-1 px-2 {{ request()->is('aktivitas-misi') ? 'text-[#1e3f7c]' : 'text-gray-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="text-[10px] {{ request()->is('aktivitas-misi') ? 'font-bold' : 'font-medium' }}">Aktivitas</span>
                </a>
                <a href="/tugas-mingguan" class="flex flex-col items-center gap-1 px-2 {{ request()->is('tugas-mingguan') ? 'text-[#1e3f7c]' : 'text-gray-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                    </svg>
                    <span class="text-[10px] {{ request()->is('tugas-mingguan') ? 'font-bold' : 'font-medium' }}">Tugas</span>
                </a>
                <a href="/akun-karyawan" class="flex flex-col items-center gap-1 px-2 {{ request()->is('akun-karyawan') ? 'text-[#1e3f7c]' : 'text-gray-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-[10px] {{ request()->is('akun-karyawan') ? 'font-bold' : 'font-medium' }}">Akun</span>
                </a>
            </div>
        </nav>

    </div>

    @stack('scripts')
</body>

</html>