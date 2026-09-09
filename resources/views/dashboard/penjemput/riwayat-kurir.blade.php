@extends('layouts.dashboard', ['title' => 'Riwayat Setoran Kurir — SulapaKarya'])

@section('dashboard-content')
<div class="space-y-6 text-left">

    <!-- Header Halaman Riwayat -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-ink/5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">Riwayat & Log Setoran</h1>
            <p class="text-xs text-ink-soft mt-0.5">Rekam data seluruh penjemputan sampah warga dan mitra PRO yang telah Anda tuntaskan.</p>
        </div>
        <a href="{{ route('penjemput.dashboard') }}" class="btn btn-sm bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold px-4 shadow-sm">
            &larr; Kembali ke Misi Aktif
        </a>
    </div>

    <!-- 4 Kartu Ringkasan Kinerja Kurir -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Sampah Terkumpul -->
        <div class="bg-white rounded-2xl p-5 border border-ink/5 shadow-none flex flex-col justify-between">
            <span class="text-xs font-semibold text-ink-soft">Total Sampah Terkumpul</span>
            <div class="mt-3">
                <div class="text-2xl font-bold text-ink tracking-tight font-mono">{{ number_format($totalWeightCollected, 1) }} <span class="text-xs font-normal text-ink-soft">kg</span></div>
                <span class="text-[11px] text-forest font-medium mt-1 block">Timbangan lolos QC</span>
            </div>
        </div>

        <!-- Total Poin Disalurkan -->
        <div class="bg-white rounded-2xl p-5 border border-ink/5 shadow-none flex flex-col justify-between">
            <span class="text-xs font-semibold text-ink-soft">Total Poin Disalurkan</span>
            <div class="mt-3">
                <div class="text-2xl font-bold text-forest tracking-tight font-mono">+{{ number_format($totalPointsGiven) }}</div>
                <span class="text-[11px] text-ink-soft font-medium mt-1 block">Telah diaudit & dicairkan</span>
            </div>
        </div>

        <!-- Penjemputan Berhasil -->
        <div class="bg-white rounded-2xl p-5 border border-ink/5 shadow-none flex flex-col justify-between">
            <span class="text-xs font-semibold text-ink-soft">Setoran Lolos QC</span>
            <div class="mt-3">
                <div class="text-2xl font-bold text-ink tracking-tight font-mono">{{ $totalSuccessCount }} <span class="text-xs font-normal text-ink-soft">titik</span></div>
                <span class="text-[11px] text-forest font-medium mt-1 block">Sukses ditimbang</span>
            </div>
        </div>

        <!-- Penjemputan Ditolak -->
        <div class="bg-white rounded-2xl p-5 border border-ink/5 shadow-none flex flex-col justify-between">
            <span class="text-xs font-semibold text-ink-soft">Setoran Ditolak QC</span>
            <div class="mt-3">
                <div class="text-2xl font-bold text-terracotta tracking-tight font-mono">{{ $totalRejectedCount }} <span class="text-xs font-normal text-ink-soft">titik</span></div>
                <span class="text-[11px] text-terracotta font-medium mt-1 block">Kotor / basah / tak layak</span>
            </div>
        </div>

    </div>

    <!-- Toolbar Filter & Pencarian -->
    <div class="bg-white border border-ink/5 rounded-2xl p-4 shadow-none">
        <form method="GET" action="{{ route('penjemput.history') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            
            <div class="sm:col-span-5 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pemohon, toko, atau kode setoran..."
                    class="input input-bordered input-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 pl-8 text-ink">
                <svg class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-ink-soft/60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>

            <div class="sm:col-span-3">
                <select name="deposit_type" onchange="this.form.submit()" class="select select-bordered select-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 font-semibold text-ink">
                    <option value="">Semua Tipe Setoran</option>
                    <option value="personal" {{ request('deposit_type') === 'personal' ? 'selected' : '' }}>Warga Reguler</option>
                    <option value="business" {{ request('deposit_type') === 'business' ? 'selected' : '' }}>Mitra Bisnis PRO</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <select name="status" onchange="this.form.submit()" class="select select-bordered select-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 font-semibold text-ink">
                    <option value="">Semua Status QC</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Lolos QC</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak QC</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="btn btn-sm bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold flex-1">
                    Filter
                </button>
                @if(request()->filled('search') || request()->filled('status') || request()->filled('deposit_type'))
                    <a href="{{ route('penjemput.history') }}" class="btn btn-sm bg-terracotta/10 hover:bg-terracotta/20 text-terracotta border-none rounded-xl text-xs font-semibold px-3">
                        ✕ Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Tabel Riwayat Transaksi Lengkap -->
    <div class="bg-white border border-ink/5 rounded-2xl p-6 shadow-none">
        <div class="overflow-x-auto">
            <table class="table w-full text-xs">
                <thead>
                    <tr class="bg-cream/40 text-ink-soft border-b border-ink/5 font-semibold text-[10px] uppercase">
                        <th class="py-3 pl-3">Waktu Selesai</th>
                        <th class="py-3">Pemohon / Mitra</th>
                        <th class="py-3">Tipe</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3">Berat Riil</th>
                        <th class="py-3">Poin Reward</th>
                        <th class="py-3 pr-3 text-right">Hasil QC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/5 font-medium">
                    @forelse($allLogs as $log)
                        @php 
                            $pemohon = \App\Models\User::find($log->user_id); 
                            $isBiz = ($log->deposit_type === 'business');
                        @endphp
                        <tr class="hover:bg-cream/20 transition-colors">
                            <td class="py-3.5 pl-3 font-mono text-[11px] text-ink-soft">
                                {{ $log->updated_at ? $log->updated_at->translatedFormat('d M Y, H:i') : '-' }} WITA
                            </td>
                            <td class="py-3.5">
                                <span class="font-bold text-ink block text-xs">
                                    {{ $isBiz && $pemohon->business_name ? $pemohon->business_name : ($pemohon->name ?? 'Pengguna') }}
                                </span>
                                <span class="text-[10px] text-ink-soft font-mono">{{ $log->deposit_code }}</span>
                            </td>
                            <td class="py-3.5">
                                @if($isBiz)
                                    <span class="inline-flex items-center bg-amber-400/20 text-amber-900 border border-amber-400/30 text-[9px] font-bold px-1.5 py-0.5 rounded font-mono">
                                        PRO B2B
                                    </span>
                                @else
                                    <span class="text-ink-soft text-[10px] font-mono">Warga</span>
                                @endif
                            </td>
                            <td class="py-3.5">
                                <span class="badge bg-cream border border-ink/10 text-ink font-semibold text-[10px] px-2 py-0.5 rounded">
                                    {{ strtoupper($log->category) }}
                                </span>
                            </td>
                            <td class="py-3.5 font-mono font-semibold text-ink">
                                {{ $log->actual_weight ? number_format($log->actual_weight, 1) . ' kg' : '-' }}
                            </td>
                            <td class="py-3.5 font-mono font-bold">
                                @if(($log->points_earned ?? 0) > 0)
                                    <span class="text-forest">+{{ number_format($log->points_earned) }} Poin</span>
                                @else
                                    <span class="text-ink-soft/40">0 Poin</span>
                                @endif
                            </td>
                            <td class="py-3.5 pr-3 text-right">
                                @if($log->status === 'ditolak_qc' || $log->status === 'rejected' || $log->status === 'ditolak')
                                    <span class="badge bg-terracotta/10 text-terracotta border-none text-[10px] font-bold px-2 py-0.5 rounded">
                                        Ditolak QC
                                    </span>
                                    @if($log->qc_notes)
                                        <p class="text-[10px] text-terracotta mt-1 max-w-xs ml-auto">{{ $log->qc_notes }}</p>
                                    @endif
                                @else
                                    <span class="badge bg-forest/10 text-forest border-none text-[10px] font-bold px-2 py-0.5 rounded">
                                        Lolos QC
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-ink-soft/60 text-xs font-medium">
                                Tidak ada rekam data riwayat penjemputan yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($allLogs->hasPages())
            <div class="mt-4 pt-3 border-t border-ink/5">
                {{ $allLogs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection