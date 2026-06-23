<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeSync - Atasan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Menghilangkan scrollbar secara visual untuk Chrome, Safari dan Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Menghilangkan scrollbar untuk IE, Edge dan Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }
    </style>
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

    <!-- SIDEBAR ATASAN -->
    <aside style="width:224px; height:100vh; background:#1e3f7c; color:white; display:flex; flex-direction:column; position:fixed; top:0; left:0; z-index:50;">

        <!-- LOGO COMPANY -->
        <div style="display:flex; align-items:center; gap:12px; padding:16px 20px; border-bottom:1px solid rgba(255,255,255,0.12); flex-shrink:0;">
            <img src="{{ asset('assets/logo.png') }}" alt="logo" style="width:40px; height:40px; object-fit:contain;">
            <span style="font-size:25px; font-weight:600; letter-spacing:0.02em;">LifeSync</span>
        </div>

        <!-- NAVIGATION MENU -->
        <div class="no-scrollbar" style="flex:1; overflow-y:auto; padding:8px 0;">
            <nav>
                <p style="font-size:12px; color:rgba(147,186,232,0.85); text-transform:uppercase; letter-spacing:0.08em; padding:12px 16px 4px; font-weight:500;">Menu Atasan</p>

                <!-- Dashboard Atasan -->
                <a href="/dashboard-atasan" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('dashboard-atasan') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('dashboard-atasan') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                    Dashboard Atasan
                </a>

                <p style="font-size:12px; color:rgba(147,186,232,0.85); text-transform:uppercase; letter-spacing:0.08em; padding:16px 16px 4px; font-weight:500;">Manajemen SDM</p>

                <!-- Kelola Reward -->
                <a href="/reward-atasan" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('reward-atasan*') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('reward-atasan*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                    Kelola Reward
                </a>

                <!-- Pelanggaran Karyawan -->
                <a href="/pelanggaran-atasan" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('pelanggaran-atasan*') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('pelanggaran-atasan*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        <line x1="12" y1="9" x2="12" y2="13" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                    </svg>
                    Unggah Surat Peringatan
                </a>
            </nav>
        </div>

        <!-- LOGOUT SECTION -->
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

    <!-- MAIN CONTENT CONTAINER -->
    <div style="margin-left:224px; min-height:100vh; display:flex; flex-direction:column;">
        
        <!-- HEADER -->
        <header class="bg-white shadow-sm px-8 py-4 flex items-center justify-between sticky top-0 z-30">
            
            <!-- SEARCH BAR (SINKRON DENGAN FITUR CARI) -->
            <form method="GET" action="{{ url()->current() }}" class="w-full max-w-md">
                <div class="flex items-center gap-3 bg-gray-100 rounded-full px-5 py-2.5">
                    <span class="text-gray-400 text-base">🔍</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari data karyawan..."
                        class="bg-transparent outline-none text-base text-gray-600 w-full placeholder:text-gray-400">
                </div>
            </form>

            <!-- PROFILE ATASAN -->
            <div class="font-bold text-[#1e3f7c] text-base flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-xs">👤</div>
                @php
                    $userLogin = \App\Models\Karyawan::find(Session::get('id_karyawan'));
                @endphp
                {{ $userLogin->nama ?? 'Atasan' }}
            </div>
        </header>

        <!-- AREA CONTENT DYNAMIC -->
        <main class="flex-1 p-6 bg-gray-100">
            @yield('content')
        </main>
    </div>

    <!-- JS DEPENDENCIES -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>