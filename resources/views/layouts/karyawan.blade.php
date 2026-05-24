<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>LifeSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="min-h-screen w-full max-w-sm mx-auto bg-white relative flex flex-col">

        @yield('content')

        {{-- Bottom Nav --}}
        <nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-sm bg-white border-t border-gray-100 z-50">
            <div class="flex justify-around items-center py-2">

                <a href="/dashboard-karyawan"
                   class="flex flex-col items-center gap-0.5 px-3 py-1 {{ request()->is('dashboard-karyawan') ? 'text-[#1e3f7c]' : 'text-gray-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <span class="text-[10px] {{ request()->is('dashboard-karyawan') ? 'font-bold' : 'font-medium' }}">Beranda</span>
                </a>

                <a href="/absensi-karyawan"
                   class="flex flex-col items-center gap-0.5 px-3 py-1 {{ request()->is('absensi-karyawan') ? 'text-[#1e3f7c]' : 'text-gray-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="20 6 9 17 4 12"/>
                    </svg>
                    <span class="text-[10px] {{ request()->is('absensi-karyawan') ? 'font-bold' : 'font-medium' }}">Absensi</span>
                </a>

                <a href="/aktivitas-misi"
                   class="flex flex-col items-center gap-0.5 px-3 py-1 {{ request()->is('aktivitas-misi') ? 'text-[#1e3f7c]' : 'text-gray-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="text-[10px] {{ request()->is('aktivitas-misi') ? 'font-bold' : 'font-medium' }}">Aktivitas</span>
                </a>

                <a href="/tugas-mingguan"
                   class="flex flex-col items-center gap-0.5 px-3 py-1 {{ request()->is('tugas-mingguan') ? 'text-[#1e3f7c]' : 'text-gray-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                    </svg>
                    <span class="text-[10px] {{ request()->is('tugas-mingguan') ? 'font-bold' : 'font-medium' }}">Tugas Mingguan</span>
                </a>

                <a href="/akun-karyawan"
                   class="flex flex-col items-center gap-0.5 px-3 py-1 {{ request()->is('akun-karyawan') ? 'text-[#1e3f7c]' : 'text-gray-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-[10px] {{ request()->is('akun-karyawan') ? 'font-bold' : 'font-medium' }}">Akun</span>
                </a>

            </div>
        </nav>

    </div>

    @stack('scripts')
</body>
</html>