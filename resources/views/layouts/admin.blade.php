<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeSync - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<script>
    function konfirmasiLogout() {
        Swal.fire({
            title: 'Keluar dari LifeSync?',
            text: 'Sesi kamu akan diakhiri.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1e3f7c',
            cancelButtonColor: '#e5e7eb',
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            customClass: {
                cancelButton: '!text-gray-700',
                popup: '!rounded-2xl',
                confirmButton: '!rounded-xl',
                cancelButton: '!rounded-xl',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/logout';
            }
        });
    }
</script>

<body class="bg-gray-100 min-h-screen text-gray-800">

    <aside style="width:224px; height:100vh; background:#1e3f7c; color:white; display:flex; flex-direction:column; position:fixed; top:0; left:0; z-index:50;">

        <div style="display:flex; align-items:center; gap:12px; padding:16px 20px; border-bottom:1px solid rgba(255,255,255,0.12); flex-shrink:0;">
            <img src="{{ asset('assets/logo.png') }}" alt="logo" style="width:40px; height:40px; object-fit:contain;">
            <span style="font-size:25px; font-weight:600; letter-spacing:0.02em;">LifeSync</span>
        </div>

        <div class="no-scrollbar" style="flex:1; overflow-y:auto; padding:8px 0;">
            <nav>
                <p style="font-size:12px; color:rgba(147,186,232,0.85); text-transform:uppercase; letter-spacing:0.08em; padding:12px 16px 4px; font-weight:500;">Menu Utama</p>

                <a href="/dashboard-admin" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('dashboard-admin') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('dashboard-admin') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                    Dashboard
                </a>

                <a href="/data-karyawan" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('data-karyawan*') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('data-karyawan*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                    </svg>
                    Data User
                </a>

                <a href="/data-divisi" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('data-divisi*') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('data-divisi*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <rect x="2" y="7" width="20" height="14" rx="2" />
                        <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                    </svg>
                    Data Divisi
                </a>

                <a href="/absensi" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('absensi*') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('absensi*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Data Absensi
                </a>

                <p style="font-size:12px; color:rgba(147,186,232,0.85); text-transform:uppercase; letter-spacing:0.08em; padding:16px 16px 4px; font-weight:500;">Kedisiplinan</p>

                <a href="/kelola-misi" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('kelola-misi*') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('kelola-misi*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    Kelola Misi Harian
                </a>

                <a href="/approve-misi" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('approve-misi*') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('approve-misi*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <polyline points="9 11 12 14 22 4" />
                    </svg>
                    Approve Misi Harian
                </a>

                <p style="font-size:12px; color:rgba(147,186,232,0.85); text-transform:uppercase; letter-spacing:0.08em; padding:16px 16px 4px; font-weight:500;">Tugas Mingguan</p>

                <a href="/kelola-tugas" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('kelola-tugas*') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('kelola-tugas*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <rect x="9" y="3" width="13" height="13" rx="2" />
                        <path d="M5 7H3a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-2" />
                    </svg>
                    Kelola Tugas
                </a>

                <a href="/approve-tugas" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('approve-tugas*') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('approve-tugas*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg>
                    Approve Laporan
                </a>
            </nav>
        </div>

        <div style="border-top:1px solid rgba(255,255,255,0.12); padding:12px 0; flex-shrink:0;">
            <a href="#" onclick="konfirmasiLogout()" style="display:flex; align-items:center; gap:10px; padding:10px 12px; margin:0 8px; border-radius:8px; font-size:13px; color:#fca5a5; text-decoration:none;"
                onmouseover="this.style.background='rgba(220,38,38,0.2)'; this.style.color='#fecaca';"
                onmouseout="this.style.background='transparent'; this.style.color='#fca5a5';">
                <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" y1="12" x2="9" y2="12" />
                </svg>
                Logout
            </a>
        </div>
    </aside>

    <div style="margin-left:224px; min-height:100vh; display:flex; flex-direction:column;">
        <!-- Bagian Header yang diperbarui -->
        <header class="bg-white shadow-sm px-8 pr-4 flex items-center justify-between sticky top-0 z-30 h-[72px] w-full flex-shrink-0">
            <!-- w-md atau w-lg untuk melebarkan search bar ke samping -->
            @if(!request()->is('dashboard-admin'))
            <form method="GET" action="{{ url()->current() }}" class="w-full max-w-md">
                <div class="flex items-center gap-3 bg-gray-100 rounded-full px-5 py-2.5">
                    <span class="text-gray-400 text-base">🔍</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari data..."
                        class="bg-transparent outline-none text-base text-gray-600 w-full placeholder:text-gray-400">
                </div>
            </form>
            @else
            <div class="w-full max-w-md"></div>
            @endif

            <div class="font-bold text-[#1e3f7c] text-base flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-xs">👤</div>
                @php
                $userLogin = \App\Models\Karyawan::find(Session::get('id_karyawan'));
                @endphp
                {{ $userLogin->nama ?? 'Admin' }}
            </div>
        </header>

        <main class="flex-1 px-6 pb-6 pt-5 bg-gray-100">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>