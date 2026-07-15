@extends('layouts.admin')

@section('content')
<div class="w-full flex flex-col gap-6">

    <div>
        <h1 class="text-xl font-bold text-gray-800">Approve Izin</h1>
        <p class="text-sm text-gray-500">Kelola pengajuan izin karyawan yang tidak dapat hadir.</p>
    </div>

    {{-- TABS --}}
    <div class="flex items-center gap-2 border-b border-gray-200">
        @php
            $tabs = [
                'menunggu'  => 'Menunggu',
                'disetujui' => 'Disetujui',
                'ditolak'   => 'Ditolak',
                'semua'     => 'Semua',
            ];
        @endphp
        @foreach ($tabs as $key => $label)
            <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
                class="px-4 py-2.5 text-sm font-semibold border-b-2 transition {{ $tab === $key ? 'border-[#1e3f7c] text-[#1e3f7c]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- TABEL --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-500 uppercase text-xs">
                    <th class="px-5 py-3">Karyawan</th>
                    <th class="px-5 py-3">Tanggal Izin</th>
                    <th class="px-5 py-3">Keterangan</th>
                    <th class="px-5 py-3">Berkas</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($data as $izin)
                    @php
                        $badge = match($izin->status) {
                            'disetujui' => 'bg-green-100 text-green-700',
                            'ditolak'   => 'bg-red-100 text-red-700',
                            default     => 'bg-yellow-100 text-yellow-700',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-semibold text-gray-800">
                            {{ $izin->karyawan->nama ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            {{ \Carbon\Carbon::parse($izin->tanggal_izin)->translatedFormat('d F Y') }}
                        </td>
                        <td class="px-5 py-3 text-gray-600 max-w-xs truncate">
                            {{ $izin->keterangan ?? '-' }}
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ asset('uploads/izin/' . $izin->file_izin) }}" target="_blank"
                                class="text-[#1e3f7c] font-medium hover:underline inline-flex items-center gap-1">
                                <i class="fa-solid fa-paperclip"></i> Lihat
                            </a>
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-semibold capitalize px-3 py-1 rounded-full {{ $badge }}">
                                {{ $izin->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                @if ($izin->status === 'menunggu')
                                    <form method="POST" action="{{ route('izin.approve', $izin->id_izin) }}"
                                        class="form-konfirmasi"
                                        data-title="Setujui pengajuan izin?"
                                        data-text="Izin {{ $izin->karyawan->nama ?? '' }} akan disetujui."
                                        data-icon="question" data-color="#16a34a" data-confirm="Ya, Setujui">
                                        @csrf
                                        <button type="submit"
                                            class="bg-green-600 text-white text-xs font-semibold rounded-lg px-3 py-1.5 hover:bg-green-700 transition">
                                            Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('izin.reject', $izin->id_izin) }}"
                                        class="form-konfirmasi"
                                        data-title="Tolak pengajuan izin?"
                                        data-text="Izin {{ $izin->karyawan->nama ?? '' }} akan ditolak dan dihitung sebagai tidak hadir."
                                        data-icon="warning" data-color="#dc2626" data-confirm="Ya, Tolak">
                                        @csrf
                                        <button type="submit"
                                            class="bg-red-600 text-white text-xs font-semibold rounded-lg px-3 py-1.5 hover:bg-red-700 transition">
                                            Tolak
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('izin.destroy', $izin->id_izin) }}"
                                    class="form-konfirmasi"
                                    data-title="Hapus data izin ini?"
                                    data-text="Berkas dan riwayat pengajuan akan dihapus permanen."
                                    data-icon="warning" data-color="#dc2626" data-confirm="Ya, Hapus">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-gray-100 text-gray-500 text-xs font-semibold rounded-lg px-3 py-1.5 hover:bg-gray-200 transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                            Tidak ada data pengajuan izin.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $data->links() }}
    </div>

</div>

@push('scripts')
<script>
    document.querySelectorAll('.form-konfirmasi').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: form.dataset.title,
                text: form.dataset.text,
                icon: form.dataset.icon,
                showCancelButton: true,
                confirmButtonColor: form.dataset.color,
                cancelButtonColor: '#e5e7eb',
                confirmButtonText: form.dataset.confirm,
                cancelButtonText: 'Batal',
                customClass: {
                    cancelButton: '!text-gray-700',
                    popup: '!rounded-2xl',
                    confirmButton: '!rounded-xl',
                    cancelButton: '!rounded-xl',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection