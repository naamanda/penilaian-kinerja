@extends('layouts.admin')

@section('content')
<div class="pt-2 px-6 pb-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Data Absensi</h1>

        {{-- Filter (hanya tampil di tab Semua) --}}
        @if($tab === 'semua')
        <form method="GET" action="/absensi" class="flex flex-wrap items-center gap-3">
            <input type="hidden" name="tab" value="semua">
            <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                class="w-[180px] h-10 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 shadow-sm focus:border-[#1e3f7c] focus:ring-1 focus:ring-[#1e3f7c] outline-none transition-all">
            <select name="status" class="w-[180px] h-10 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 shadow-sm focus:border-[#1e3f7c] focus:ring-1 focus:ring-[#1e3f7c] outline-none transition-all">
                <option value="">Semua Status</option>
                <option value="hadir" {{ request('status') == 'hadir'      ? 'selected' : '' }}>Hadir</option>
                <option value="terlambat" {{ request('status') == 'terlambat'  ? 'selected' : '' }}>Terlambat</option>
                <option value="tidak_hadir" {{ request('status') == 'tidak_hadir'? 'selected' : '' }}>Tidak Hadir</option>
            </select>
            <button type="submit" class="bg-[#1e3f7c] hover:bg-blue-900 text-white text-sm font-semibold h-10 px-5 rounded-xl shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2l-7 7v5l-4 2v-7L3 6V4z" />
                </svg>
                Filter
            </button>
            @if(request('tanggal') || request('status'))
            <a href="/absensi" class="bg-white border border-gray-200 text-gray-700 text-sm font-semibold h-10 px-5 rounded-xl shadow-sm hover:bg-blue-50/60 transition-all flex items-center justify-center gap-2">
                Reset
            </a>
            @endif
        </form>
        @endif
    </div>

    {{-- ===== TABS ===== --}}
    <div class="flex items-center gap-1 mb-4 bg-gray-100 p-1 rounded-xl w-fit">
        <a href="/absensi?tab=hadir"
            class="px-4 py-2 rounded-lg text-sm font-semibold transition-all
              {{ $tab === 'hadir' ? 'bg-white text-[#1e3f7c] shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            Hadir
            @php $jumlahHadir = \App\Models\Absensi::whereDate('tanggal', $today)->where('status','hadir')->count(); @endphp
            @if($jumlahHadir > 0)
            <span class="ml-1.5 bg-emerald-100 text-emerald-700 text-xs px-2 py-0.5 rounded-full">{{ $jumlahHadir }}</span>
            @endif
        </a>
        <a href="/absensi?tab=terlambat"
            class="px-4 py-2 rounded-lg text-sm font-semibold transition-all
              {{ $tab === 'terlambat' ? 'bg-white text-[#1e3f7c] shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            Terlambat
            @php $jumlahTerlambat = \App\Models\Absensi::whereDate('tanggal', $today)->where('status','terlambat')->count(); @endphp
            @if($jumlahTerlambat > 0)
            <span class="ml-1.5 bg-orange-100 text-orange-700 text-xs px-2 py-0.5 rounded-full">{{ $jumlahTerlambat }}</span>
            @endif
        </a>
        <a href="/absensi?tab=tidak_hadir"
            class="px-4 py-2 rounded-lg text-sm font-semibold transition-all
              {{ $tab === 'tidak_hadir' ? 'bg-white text-[#1e3f7c] shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            Tidak Hadir
            @php
            // 1. Cek terlebih dahulu apakah hari ini libur
            if (isset($hariIniLibur) && $hariIniLibur) {
            $jumlahTidak = 0; // Jika libur, otomatis set counter menjadi 0
            } else {
            // 2. Jika hari kerja biasa, lakukan perhitungan normal
            $sudahIds = \App\Models\Absensi::whereDate('tanggal', $today)->whereIn('status',['hadir','terlambat'])->pluck('id_karyawan');
            $jumlahTidak = \App\Models\Karyawan::where('id_role', 2)->whereNotIn('id_karyawan', $sudahIds)->count();
            }
            @endphp

            {{-- Badge angka hanya akan muncul jika jumlahnya di atas 0 --}}
            @if($jumlahTidak > 0)
            <span class="ml-1.5 bg-rose-100 text-rose-700 text-xs px-2 py-0.5 rounded-full">{{ $jumlahTidak }}</span>
            @endif
        </a>
    </div>

    {{-- ===== ALERT HARI LIBUR ===== --}}
    @if(isset($hariIniLibur) && $hariIniLibur)
    <div class="mb-4 flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl text-sm font-medium shadow-sm">
        <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <div>
            <span class="font-bold">Informasi Sistem:</span> Hari ini Libur atau Tanggal Merah.
        </div>
    </div>
    @endif

    {{-- ===== TABLE CARD ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">

            {{-- ── Tab: Semua & Hadir/Terlambat ── --}}
            @if($tab !== 'tidak_hadir')
            <table class="w-full">
                <thead>
                    <tr class="bg-[#1e3f7c] text-white">
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-wider text-sm">Nama Karyawan</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-wider text-sm whitespace-nowrap">Tanggal</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-wider text-sm whitespace-nowrap">Waktu</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-wider text-sm">Status</th>
                        <th class="px-6 py-4 text-center font-semibold uppercase tracking-wider text-sm">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data as $a)
                    <tr class="hover:bg-blue-50/60 transition-colors duration-200">
                        <td class="px-6 py-3.5 text-base font-medium text-gray-900">{{ $a->karyawan->nama ?? '-' }}</td>
                        <td class="px-6 py-3.5 text-sm text-gray-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('d F Y') }}</td>
                        <td class="px-6 py-3.5 text-sm text-gray-600 whitespace-nowrap">{{ $a->waktu ?? '-' }}</td>
                        <td class="px-6 py-3.5 whitespace-nowrap">
                            @if($a->status == 'hadir')
                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Hadir</span>
                            @elseif($a->status == 'terlambat')
                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">Terlambat</span>
                            @else
                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">Tidak Hadir</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-center whitespace-nowrap">
                            <div class="flex justify-center items-center gap-4">
                                <a href="/absensi/{{ $a->id_absensi }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-800 transition group">
                                    <svg class="w-4 h-4 text-blue-500 group-hover:text-blue-700 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span class="hover:underline decoration-2 underline-offset-4">Lihat Detail</span>
                                </a>
                                <form id="delete-form-{{ $a->id_absensi }}" action="/absensi/hapus/{{ $a->id_absensi }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="openDeleteModal('{{ $a->id_absensi }}', '{{ $a->karyawan->nama ?? '-' }}')" class="text-rose-500 hover:text-rose-600 transition flex items-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 font-medium">
                            @if(isset($hariIniLibur) && $hariIniLibur && $tab !== 'semua')
                            <span class="text-amber-600 font-semibold">Hari ini libur/tanggal merah. Tidak ada jadwal absensi.</span>
                            @else
                            {{ $tab === 'hari_ini' ? 'Belum ada karyawan yang absen hari ini.' : 'Belum ada data absensi.' }}
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- ── Tab: Tidak Hadir ── --}}
            @else
            <table class="w-full">
                <thead>
                    <tr class="bg-[#1e3f7c] text-white">
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-wider text-sm">Nama Karyawan</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-wider text-sm">Divisi</th>
                        <th class="px-6 py-4 text-left font-semibold uppercase tracking-wider text-sm">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($karyawanTidakHadir as $k)
                    <tr class="hover:bg-blue-50/60 transition-colors duration-200">
                        <td class="px-6 py-3.5 text-base font-medium text-gray-900">{{ $k->nama }}</td>
                        <td class="px-6 py-3.5 text-sm text-gray-600">{{ $k->divisi->nama_divisi ?? '-' }}</td>
                        <td class="px-6 py-3.5">
                            <span class="px-3 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">Tidak Hadir</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-gray-400 font-medium">
                            @if(isset($hariIniLibur) && $hariIniLibur)
                            <span class="text-amber-600 font-semibold">Hari ini libur/tanggal merah. Tidak ada jadwal absensi.</span>
                            @else
                            🎉 Semua karyawan sudah hadir hari ini!
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @endif

        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4 flex justify-end">
        @php $paginator = $tab === 'tidak_hadir' ? $karyawanTidakHadir : $data; @endphp

        {{-- PASTIKAN AKTIF HANYA JIKA OBJEK ADALAH PAGINATOR (Bukan Collection biasa) --}}
        @if($paginator instanceof \Illuminate\Pagination\LengthAwarePaginator && $paginator->lastPage() > 1)
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-500">
                Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
            </span>
            <div class="flex items-center gap-1">
                @for($i = 1; $i <= $paginator->lastPage(); $i++)
                    <a href="{{ $paginator->url($i) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-all {{ $paginator->currentPage() == $i ? 'bg-[#1e3f7c] text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-blue-50/60' }}">
                        {{ $i }}
                    </a>
                    @endfor
            </div>
        </div>
        @endif
    </div>

    {{-- Modal Hapus --}}
    <div id="delete-modal" class="fixed inset-0 z-[9999] hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0 font-sans">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-rose-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-bold text-gray-900 leading-6">Konfirmasi Hapus</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 italic">Apakah Anda yakin ingin menghapus data absensi <span id="modal-absensi" class="font-bold text-gray-800 not-italic"></span>? Tindakan ini tidak bisa dibatalkan.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                    <button type="button" id="confirm-delete-btn" class="inline-flex w-full justify-center rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-rose-700 transition-all sm:w-auto">Ya, Hapus</button>
                    <button type="button" onclick="closeDeleteModal()" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-blue-50/60 transition-all sm:mt-0 sm:w-auto">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let activeDeleteFormId = null;

        function openDeleteModal(id, name) {
            activeDeleteFormId = `delete-form-${id}`;
            document.getElementById('modal-absensi').innerText = name;
            document.getElementById('delete-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            activeDeleteFormId = null;
        }
        document.getElementById('confirm-delete-btn').addEventListener('click', function() {
            if (activeDeleteFormId) document.getElementById(activeDeleteFormId).submit();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDeleteModal();
        });
    </script>
</div>
@endsection