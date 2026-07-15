@extends('layouts.karyawan')

@section('content')
<div class="w-full flex flex-col gap-6">

    <div>
        <h1 class="text-xl font-bold text-gray-800">Pengajuan Izin</h1>
        <p class="text-sm text-gray-500">Unggah surat izin jika kamu tidak dapat hadir hari ini.</p>
    </div>

    {{-- STATUS HARI INI --}}
    @if ($sudahAdaCatatanHariIni)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center gap-3">
            <i class="fa-solid fa-circle-info text-[#1e3f7c]"></i>
            <div>
                <p class="text-sm font-semibold text-gray-800">Sudah ada catatan untuk hari ini</p>
                <p class="text-xs text-gray-500">
                    Status:
                    <span class="font-semibold capitalize">{{ str_replace('_', ' ', $statusHariIni) }}</span>
                </p>
            </div>
        </div>
    @else
        {{-- FORM PENGAJUAN IZIN --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <form id="formIzin" class="flex flex-col gap-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Izin</label>
                    <div class="w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm text-gray-600 flex items-center gap-2">
                        <i class="fa-regular fa-calendar text-gray-400"></i>
                        {{ \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Surat Izin</label>
                    <input type="file" name="file_izin" required accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full text-sm text-gray-600 border border-gray-300 rounded-xl px-4 py-2.5 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-[#1e3f7c] file:text-white file:text-xs">
                    <p class="text-xs text-gray-400 mt-1">Format PDF/JPG/PNG, maksimal 2MB.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan (opsional)</label>
                    <textarea name="keterangan" rows="3" maxlength="255"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3f7c]"
                        placeholder="Contoh: Sakit demam, ada surat dokter terlampir"></textarea>
                </div>

                <div id="pesanIzin" class="hidden text-sm rounded-xl px-4 py-2.5"></div>

                <button type="submit" id="btnSubmitIzin"
                    class="bg-[#1e3f7c] text-white font-semibold text-sm rounded-xl py-3 hover:bg-blue-900 transition">
                    Kirim Pengajuan Izin
                </button>
            </form>
        </div>
    @endif

    {{-- RIWAYAT PENGAJUAN --}}
    <div>
        <h2 class="text-sm font-bold text-gray-700 mb-3">Riwayat Pengajuan</h2>

        @if ($riwayatIzin->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 text-center text-sm text-gray-400">
                Belum ada riwayat pengajuan izin.
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach ($riwayatIzin as $izin)
                    @php
                        $badge = match($izin->status) {
                            'disetujui' => 'bg-green-100 text-green-700',
                            'ditolak'   => 'bg-red-100 text-red-700',
                            default     => 'bg-yellow-100 text-yellow-700',
                        };
                    @endphp
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($izin->tanggal_izin)->translatedFormat('d F Y') }}
                            </p>
                            @if ($izin->keterangan)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $izin->keterangan }}</p>
                            @endif
                            <a href="{{ asset('uploads/izin/' . $izin->file_izin) }}" target="_blank"
                                class="text-xs text-[#1e3f7c] font-medium hover:underline inline-flex items-center gap-1 mt-1">
                                <i class="fa-solid fa-paperclip"></i> Lihat berkas
                            </a>
                        </div>
                        <span class="text-xs font-semibold capitalize px-3 py-1 rounded-full {{ $badge }}">
                            {{ $izin->status }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    const formIzin = document.getElementById('formIzin');
    if (formIzin) {
        formIzin.addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn = document.getElementById('btnSubmitIzin');
            const pesan = document.getElementById('pesanIzin');
            btn.disabled = true;
            btn.innerText = 'Mengirim...';

            try {
                const res = await fetch('/izin-karyawan/simpan', {
                    method: 'POST',
                    body: new FormData(formIzin),
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                pesan.classList.remove('hidden', 'bg-green-50', 'bg-red-50', 'text-green-700', 'text-red-700');

                if (res.ok) {
                    pesan.classList.add('bg-green-50', 'text-green-700');
                    pesan.innerText = data.message;
                    setTimeout(() => location.reload(), 1200);
                } else {
                    pesan.classList.add('bg-red-50', 'text-red-700');
                    pesan.innerText = data.message ?? 'Terjadi kesalahan, silakan coba lagi.';
                    btn.disabled = false;
                    btn.innerText = 'Kirim Pengajuan Izin';
                }
            } catch (err) {
                pesan.classList.remove('hidden');
                pesan.classList.add('bg-red-50', 'text-red-700');
                pesan.innerText = 'Gagal terhubung ke server.';
                btn.disabled = false;
                btn.innerText = 'Kirim Pengajuan Izin';
            }
        });
    }
</script>
@endpush
@endsection