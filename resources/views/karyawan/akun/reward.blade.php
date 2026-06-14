@extends('layouts.karyawan')

@section('content')
<div class="px-4 py-4 pb-24 space-y-4">
    <a href="{{ route('karyawan.akun.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-[#1e3f7c]">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Akun
    </a>

    <div>
        <h2 class="text-base font-bold text-gray-800">Riwayat Reward</h2>
        <p class="text-xs text-gray-500 mt-0.5">Penghargaan yang berhasil Anda raih.</p>
    </div>

    @if($daftarReward->isEmpty())
    <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6 text-center">
        <div class="w-12 h-12 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-trophy text-yellow-400 text-xl"></i>
        </div>
        <p class="text-sm font-semibold text-gray-700">Belum ada reward</p>
        <p class="text-xs text-gray-400 mt-1">Terus tingkatkan kinerja Anda untuk meraih penghargaan.</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($daftarReward as $hasil)
        @php $periode = $daftarBulan[$hasil->bulan] . ' ' . $hasil->tahun; @endphp
        <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
            {{-- Header Periode --}}
            <div class="bg-[#1e3f7c] px-4 py-3 flex items-center justify-between">
                <div>
                    <p class="text-xs text-blue-200 font-medium">Periode</p>
                    <p class="text-sm text-white font-bold">{{ $periode }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-blue-200">Nilai Akhir</p>
                    <p class="text-sm text-white font-bold">
                        {{ $hasil->nilai_akhir }} <span class="text-blue-200 font-normal">/100</span>
                    </p>
                </div>
            </div>

            {{-- Daftar Reward --}}
            <div class="divide-y divide-gray-50">
                @foreach($hasil->reward as $reward)
                <div class="px-4 py-3">
                    {{-- Nama reward --}}
                    <div class="mb-3">
                        <p class="text-sm font-semibold text-gray-800">{{ $reward->nama_reward }}</p>
                        <p class="text-xs text-gray-400 capitalize">{{ ucfirst($reward->jenis) }}</p>
                    </div>

                    {{-- Apresiasi: nominal + sertifikat --}}
                    <div class="grid grid-cols-2 gap-2">
                        {{-- Uang Tunai --}}
                        <div class="bg-green-50 rounded-xl px-3 py-2">
                            <p class="text-xs text-green-600 font-medium mb-0.5">Uang Tunai</p>
                            <p class="text-sm font-bold text-green-700">
                                Rp {{ number_format($reward->nominal, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- Sertifikat --}}
                        <a href="{{ route('karyawan.akun.reward.sertifikat', $reward->id_reward) }}"
                            target="_blank"
                            class="bg-blue-50 rounded-xl px-3 py-2 flex flex-col justify-between hover:bg-blue-100 transition">
                            <p class="text-xs text-[#1e3f7c] font-medium mb-0.5">Sertifikat</p>
                            <div class="flex items-center justify-between">
                                <p class="text-xs text-[#1e3f7c] leading-tight">Klik Untuk Lihat</p>
                                <i class="fas fa-download text-[#1e3f7c] text-xs"></i>
                            </div>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection