<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeSync - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .no-scrollbar {
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .no-scrollbar::-webkit-scrollbar {
            width: 0;
            display: none;
        }

        /* Transisi untuk Dropdown Menu */
        .dropdown-container {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .dropdown-container.show {
            max-height: 200px;
            /* Sesuaikan dengan tinggi konten */
        }

        .chevron-icon {
            transition: transform 0.2s ease;
        }

        .rotate-chevron {
            transform: rotate(90deg);
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

    // Fungsi Toggle Dropdown Sidebar
    function toggleDropdown(id, element) {
        const dropdown = document.getElementById(id);
        const chevron = element.querySelector('.chevron-icon');

        dropdown.classList.toggle('show');
        if (chevron) {
            chevron.classList.toggle('rotate-chevron');
        }
    }
</script>

<body class="bg-gray-100 min-h-screen text-gray-800">

    <aside style="
    width:224px;
    position:fixed;
    top:0;
    left:0;
    bottom:0;
    background:#1e3f7c;
    color:white;
    display:flex;
    flex-direction:column;
    z-index:50;
">

        <div style="display:flex; align-items:center; gap:12px; padding:16px 20px; border-bottom:1px solid rgba(255,255,255,0.12); flex-shrink:0;">
            <img src="{{ asset('assets/logo.png') }}" alt="logo" style="width:40px; height:40px; object-fit:contain;">
            <span style="font-size:25px; font-weight:600; letter-spacing:0.02em;">LifeSync</span>
        </div>
        <div class="no-scrollbar" style="flex:1; overflow-y:auto; overflow-x:hidden; padding:4px 0;">
            <nav>

                <a href="/dashboard-admin" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('dashboard-admin') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('dashboard-admin') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <svg style="width:16px;height:16px;flex-shrink:0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                    Dashboard
                </a>

                <a href="/data-karyawan" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('data-karyawan*') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'"
                    onmouseout="this.style.background='{{ request()->is('data-karyawan*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <i class="fa-solid fa-user" style="width:16px; flex-shrink:0;"></i>
                    Data Karyawan
                </a>

                <a href="/data-divisi" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('data-divisi*') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('data-divisi*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <i class="fa-solid fa-briefcase" style="width:16px; flex-shrink:0;"></i>
                    Data Divisi
                </a>

                <a href="/absensi" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('absensi*') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('absensi*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <i class="fa-solid fa-check" style="width:16px; flex-shrink:0;"></i>
                    Data Absensi
                </a>

                <a href="/approve-izin" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('approve-izin*') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('approve-izin*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <i class="fa-solid fa-file-shield" style="width:16px; flex-shrink:0;"></i>
                    Approve Izin
                </a>

                <a href="/admin/rekap-kinerja" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('admin/rekap-kinerja*') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('admin/rekap-kinerja*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <i class="fa-solid fa-folder-open" style="width:16px; flex-shrink:0;"></i>
                    Rekap Kinerja
                </a>

                <a href="/admin/backup" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:1px 8px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); text-decoration:none; {{ request()->is('admin/backup*') ? 'background:rgba(255,255,255,0.15); color:white; font-weight:500;' : '' }}"
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('admin/backup*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                    <i class="fa-solid fa-download" style="width:16px; flex-shrink:0;"></i>
                    Backup & Restore
                </a>

                @php
                $isKedisiplinanActive = request()->is('kelola-misi*') || request()->is('approve-misi*');
                @endphp
                <div style="margin: 4px 8px;">
                    <button type="button" onclick="toggleDropdown('dropKedisiplinan', this)" style="display:flex; width:100%; align-items:center; justify-content:space-between; gap:10px; padding:8px 12px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); background:transparent; border:none; cursor:pointer;"
                        onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='transparent'">
                        <div style="display:flex; align-items:center; gap:10px; flex:1;">
                            <i class="fa-solid fa-scale-balanced" style="width:16px; flex-shrink:0;"></i>
                            <span>Kedisiplinan</span>
                        </div>
                        <i class="fa-solid fa-chevron-right chevron-icon text-[10px] {{ $isKedisiplinanActive ? 'rotate-chevron' : '' }}"></i>
                    </button>

                    <div id="dropKedisiplinan" class="dropdown-container {{ $isKedisiplinanActive ? 'show' : '' }}" style="padding-left: 12px;">
                        <a href="/kelola-misi" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:2px 0; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.7); text-decoration:none; {{ request()->is('kelola-misi*') ? 'background:rgba(255,255,255,0.15); color:white;' : '' }}"
                            onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('kelola-misi*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                            <i class="fa-solid fa-file-lines" style="width:16px; flex-shrink:0;"></i>
                            Kelola Misi Harian
                        </a>
                        <a href="/approve-misi" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:2px 0; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.7); text-decoration:none; {{ request()->is('approve-misi*') ? 'background:rgba(255,255,255,0.15); color:white;' : '' }}"
                            onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('approve-misi*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                            <i class="fa-solid fa-list-check" style="width:16px; flex-shrink:0;"></i>
                            Approve Misi Harian
                        </a>
                    </div>
                </div>

                @php
                $isTugasActive = request()->is('kelola-tugas*') || request()->is('approve-tugas*');
                @endphp
                <div style="margin: 4px 8px;">
                    <button type="button" onclick="toggleDropdown('dropTugas', this)" style="display:flex; width:100%; align-items:center; justify-content:space-between; gap:10px; padding:8px 12px; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.8); background:transparent; border:none; cursor:pointer;"
                        onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='transparent'">
                        <div style="display:flex; align-items:center; gap:10px; flex:1;">
                            <i class="fa-solid fa-list-ol" style="width:16px; flex-shrink:0;"></i>
                            <span>Tugas</span>
                        </div>
                        <i class="fa-solid fa-chevron-right chevron-icon text-[10px] {{ $isTugasActive ? 'rotate-chevron' : '' }}"></i>
                    </button>

                    <div id="dropTugas" class="dropdown-container {{ $isTugasActive ? 'show' : '' }}" style="padding-left: 12px;">
                        <a href="/kelola-tugas" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:2px 0; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.7); text-decoration:none; {{ request()->is('kelola-tugas*') ? 'background:rgba(255,255,255,0.15); color:white;' : '' }}"
                            onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('kelola-tugas*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                            <i class="fa-solid fa-clipboard" style="width:16px; flex-shrink:0;"></i>
                            Kelola Tugas
                        </a>
                        <a href="/approve-tugas" style="display:flex; align-items:center; gap:10px; padding:8px 12px; margin:2px 0; border-radius:8px; font-size:13px; color:rgba(255,255,255,0.7); text-decoration:none; {{ request()->is('approve-tugas*') ? 'background:rgba(255,255,255,0.15); color:white;' : '' }}"
                            onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='{{ request()->is('approve-tugas*') ? 'rgba(255,255,255,0.15)' : 'transparent' }}'">
                            <i class="fa-solid fa-square-check" style="width:16px; flex-shrink:0;"></i>
                            Approve Tugas
                        </a>
                    </div>
                </div>

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

    <div style="margin-left:224px; display:flex; flex-direction:column; min-h-screen; width: calc(100% - 224px);">
        <header class="bg-white shadow-sm px-8 pr-4 flex items-center justify-between sticky top-0 z-30 h-[72px] w-full flex-shrink-0">
            @if(!request()->is('dashboard-admin'))
            <form method="GET" action="{{ url()->current() }}" class="w-full max-w-md">
                <div class="flex items-center gap-3 bg-gray-100 rounded-full px-5 py-2.5">
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

        <main class="w-full min-h-[calc(100vh-72px)] p-6 bg-gray-100 flex flex-col justify-start items-stretch">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')

</body>

</html>