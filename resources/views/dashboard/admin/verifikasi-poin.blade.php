@extends('layouts.dashboard', ['title' => 'Verifikasi & Mutasi Poin — SulapaKarya'])

@section('dashboard-content')
@php
    $isFiltering = request()->hasAny(['filter_date', 'filter_month', 'filter_year', 'filter_status', 'page']);
    $todayCompleted = $verifiedHistory->filter(fn($h) => $h->updated_at && $h->updated_at->isToday());
@endphp

<div class="space-y-6 text-left">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-ink/5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">Audit & Mutasi Poin</h1>
            <p class="text-xs text-ink-soft mt-0.5">Audit usulan poin setoran warga dan pantau perputaran mutasi poin reward.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center whitespace-nowrap bg-forest/10 text-forest border border-forest/20 text-[11px] font-semibold px-3 py-1.5 rounded-xl">
                SLA Auto-Release 24 Jam Aktif
            </span>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if($errors->any())
        <div class="alert alert-error bg-terracotta/10 border border-terracotta/20 text-terracotta rounded-2xl text-xs font-semibold p-4 shadow-none">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success bg-forest/10 border border-forest/20 text-forest rounded-2xl text-xs font-semibold p-4 shadow-none">
            {{ session('success') }}
        </div>
    @endif

    <!-- NAVIGASI 2 TAB -->
    <div class="flex items-center gap-2 border-b border-ink/5 pb-2">
        <button type="button" onclick="switchMainTab('today')" id="tab_btn_today"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $isFiltering ? 'bg-cream/40 text-ink-soft hover:bg-cream' : 'bg-forest text-white shadow-xs' }}">
            <span>Hari Ini</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono {{ $isFiltering ? 'bg-ink/5 text-ink-soft' : 'bg-white/20 text-white' }}">
                {{ $pendingTransfers->count() }} Antrean
            </span>
        </button>

        <button type="button" onclick="switchMainTab('all')" id="tab_btn_all"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $isFiltering ? 'bg-forest text-white shadow-xs' : 'bg-cream/40 text-ink-soft hover:bg-cream' }}">
            <span>Semua Riwayat (Buku Mutasi)</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono {{ $isFiltering ? 'bg-white/20 text-white' : 'bg-ink/5 text-ink-soft' }}">
                {{ $verifiedHistory->total() }} Data
            </span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: AKTIVITAS HARI INI (ANTREAN AUDIT & MUTASI HARI INI) -->
    <!-- ========================================================================= -->
    <div id="tab_content_today" class="space-y-6 {{ $isFiltering ? 'hidden' : '' }}">
        
        <!-- 1. Antrean Pending Audit Segera -->
        <div class="bg-white border border-ink/5 rounded-2xl p-5 sm:p-6 shadow-none space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-ink/5">
                <div>
                    <h2 class="text-sm font-bold text-ink flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        Antrean Perlu Verifikasi Segera
                    </h2>
                    <p class="text-[11px] text-ink-soft">Tinjau kesesuaian timbangan lapangan kurir sebelum dicairkan sistem (SLA 24 Jam).</p>
                </div>
                <span class="inline-flex items-center whitespace-nowrap text-[11px] font-mono font-bold bg-amber-50 text-amber-700 px-2.5 py-1 rounded-lg border border-amber-200">
                    {{ $pendingTransfers->count() }} Menunggu
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full text-xs">
                    <thead>
                        <tr class="bg-cream/40 text-ink-soft border-b border-ink/5 font-semibold text-[10px] uppercase">
                            <th class="py-3 pl-3">Kode & Waktu</th>
                            <th class="py-3">Warga Pemohon</th>
                            <th class="py-3">Kurir Pemeriksa</th>
                            <th class="py-3 text-center">Timbangan (Aktual / Est.)</th>
                            <th class="py-3 text-center">Usulan Poin</th>
                            <th class="py-3 text-center min-w-[130px]">Sisa Batas Waktu</th>
                            <th class="py-3 pr-3 text-right">Aksi Audit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/5 font-medium">
                        @forelse($pendingTransfers as $t)
                            @php
                                $autoApproveAt = ($t->updated_at ?? $t->created_at)->addHours(24);
                                $totalMenit = (int) now()->diffInMinutes($autoApproveAt, false);
                                $sisaJam = intdiv(max($totalMenit, 0), 60);
                                $sisaMenit = max($totalMenit, 0) % 60;
                                $isExpiringSoon = $totalMenit > 0 && $totalMenit <= 240;

                                $est = (float) ($t->estimated_weight ?? 0);
                                $act = (float) ($t->actual_weight ?? 0);
                                $selisih = $act - $est;
                            @endphp
                            <tr class="hover:bg-cream/20 transition-colors">
                                <td class="py-3.5 pl-3">
                                    <span class="font-mono font-bold text-ink block text-xs">{{ $t->deposit_code ?? '-' }}</span>
                                    <span class="text-[10px] text-ink-soft font-mono">{{ ($t->updated_at ?? $t->created_at)->translatedFormat('d M Y, H:i') }} WITA</span>
                                </td>
                                <td class="py-3.5">
                                    <span class="font-bold text-ink block text-xs truncate max-w-[140px]">{{ $t->user->name ?? 'Warga' }}</span>
                                    <span class="text-[10px] text-ink-soft font-mono">{{ $t->user->phone ?? '-' }}</span>
                                </td>
                                <td class="py-3.5">
                                    <span class="font-semibold text-maritime block">{{ $t->penjemput->name ?? 'Kurir Lapangan' }}</span>
                                    <span class="text-[10px] text-ink-soft">Armada Lapangan</span>
                                </td>
                                <td class="py-3.5 text-center font-mono">
                                    <span class="font-bold text-ink text-xs">{{ number_format($act, 2) }} kg</span>
                                    <span class="text-[10px] text-ink-soft block font-sans">
                                        Est: {{ number_format($est, 2) }} kg
                                        @if($selisih != 0)
                                            <span class="{{ $selisih > 0 ? 'text-forest' : 'text-terracotta' }} font-bold">
                                                ({{ $selisih > 0 ? '+' : '' }}{{ number_format($selisih, 2) }})
                                            </span>
                                        @endif
                                    </span>
                                </td>
                                <td class="py-3.5 text-center font-mono">
                                    <span class="text-forest font-bold text-sm block">+{{ number_format($t->points_earned ?? 0) }}</span>
                                    <span class="text-[10px] text-ink-soft font-sans uppercase">{{ $t->category }}</span>
                                </td>
                                <td class="py-3.5 text-center min-w-[130px]">
                                    @if($totalMenit > 0)
                                        <span class="inline-flex items-center whitespace-nowrap text-[10px] font-mono font-bold px-2.5 py-1 rounded-md {{ $isExpiringSoon ? 'bg-terracotta/10 text-terracotta' : 'bg-amber-50 text-amber-700' }}">
                                            {{ $sisaJam }}j {{ $sisaMenit }}m
                                        </span>
                                    @else
                                        <span class="inline-flex items-center whitespace-nowrap text-[10px] font-mono font-bold bg-forest/10 text-forest px-2.5 py-1 rounded-md">
                                            Auto-Release
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 pr-3 text-right">
                                    <button type="button" onclick="document.getElementById('audit_modal_{{ $t->id }}').showModal()" 
                                        class="btn btn-xs bg-forest hover:bg-forest-dark text-white border-none rounded-lg text-[10px] font-semibold px-3 py-1.5 shadow-none whitespace-nowrap">
                                        Periksa & Audit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-ink-soft/60 text-xs font-medium">
                                    Semua setoran hari ini telah diverifikasi. Tidak ada antrean pending.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Ringkasan yang Selesai Diverifikasi Hari Ini -->
        <div class="bg-white border border-ink/5 rounded-2xl p-5 sm:p-6 shadow-none space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-ink/5">
                <div>
                    <h2 class="text-sm font-bold text-ink">Mutasi Poin Tuntas Hari Ini</h2>
                    <p class="text-[11px] text-ink-soft">Daftar pencairan poin yang disetujui atau ditolak pada tanggal {{ now()->translatedFormat('d F Y') }}.</p>
                </div>
                <span class="text-[11px] font-mono text-ink-soft font-semibold">{{ $todayCompleted->count() }} Transaksi</span>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full text-xs">
                    <thead>
                        <tr class="bg-cream/20 text-ink-soft border-b border-ink/5 font-semibold text-[10px] uppercase">
                            <th class="py-2.5 pl-3">Waktu</th>
                            <th class="py-2.5">Warga</th>
                            <th class="py-2.5">Komoditas & Berat</th>
                            <th class="py-2.5">Poin Reward</th>
                            <th class="py-2.5 pr-3 text-right">Hasil Audit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/5 font-medium">
                        @forelse($todayCompleted as $tc)
                            @php $isOk = in_array($tc->status, ['berhasil_dikirim', 'completed', 'selesai']); @endphp
                            <tr class="hover:bg-cream/10">
                                <td class="py-3 pl-3 font-mono text-ink-soft">{{ $tc->updated_at->format('H:i') }} WITA</td>
                                <td class="py-3 font-bold text-ink">{{ $tc->user->name ?? 'Warga' }}</td>
                                <td class="py-3 capitalize">{{ $tc->category }} ({{ number_format($tc->actual_weight ?? 0, 2) }} kg)</td>
                                <td class="py-3 font-mono font-bold {{ $isOk ? 'text-forest' : 'text-terracotta' }}">
                                    {{ $isOk ? '+' . number_format($tc->points_earned ?? 0) : '0' }} Poin
                                </td>
                                <td class="py-3 pr-3 text-right">
                                    <span class="inline-flex items-center whitespace-nowrap px-2 py-0.5 rounded text-[10px] font-bold {{ $isOk ? 'bg-forest/10 text-forest' : 'bg-terracotta/10 text-terracotta' }}">
                                        {{ $isOk ? 'Dicairkan' : 'Ditolak' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-ink-soft/60 text-xs">Belum ada verifikasi poin yang diselesaikan hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: KESELURUHAN BUKU MUTASI & FILTER LENGKAP -->
    <!-- ========================================================================= -->
    <div id="tab_content_all" class="space-y-5 {{ $isFiltering ? '' : 'hidden' }}">
        
        <div class="bg-white border border-ink/5 rounded-2xl p-5 sm:p-6 shadow-none space-y-5">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b border-ink/5">
                <div>
                    <h2 class="text-sm font-bold text-ink">Buku Riwayat Mutasi Keseluruhan</h2>
                    <p class="text-[11px] text-ink-soft">Gunakan filter di bawah untuk menelusuri data audit per tanggal, bulan, tahun, atau status.</p>
                </div>
                <div class="bg-forest/5 border border-forest/20 px-3 py-1.5 rounded-xl text-right">
                    <span class="text-[9px] uppercase font-bold text-ink-soft block">Poin Terdistribusi</span>
                    <span class="font-mono font-black text-sm text-forest">+{{ number_format($totalPointsReleased ?? 0) }}</span>
                </div>
            </div>

            <!-- FORM FILTER -->
            <form method="GET" action="{{ route('admin.pending-points') }}" class="bg-cream/30 p-4 rounded-2xl border border-ink/5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end text-xs">
                
                <!-- 1. Filter Tanggal Spesifik -->
                <div>
                    <label class="block text-[11px] font-semibold text-ink mb-1">Tanggal</label>
                    <input type="date" name="filter_date" value="{{ request('filter_date') }}" 
                        class="input input-bordered input-sm w-full rounded-xl text-xs bg-white focus:outline-none focus:border-forest text-ink">
                </div>

                <!-- 2. Filter Bulan -->
                <div>
                    <label class="block text-[11px] font-semibold text-ink mb-1">Bulan</label>
                    <select name="filter_month" class="select select-bordered select-sm w-full rounded-xl text-xs bg-white text-ink focus:outline-none focus:border-forest">
                        <option value="">Semua Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ request('filter_month') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- 3. Filter Tahun -->
                <div>
                    <label class="block text-[11px] font-semibold text-ink mb-1">Tahun</label>
                    <select name="filter_year" class="select select-bordered select-sm w-full rounded-xl text-xs bg-white text-ink focus:outline-none focus:border-forest">
                        <option value="">Semua Tahun</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ request('filter_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Filter Status -->
                <div>
                    <label class="block text-[11px] font-semibold text-ink mb-1">Status Audit</label>
                    <select name="filter_status" class="select select-bordered select-sm w-full rounded-xl text-xs bg-white text-ink focus:outline-none focus:border-forest">
                        <option value="">Semua Hasil</option>
                        <option value="approved" {{ request('filter_status') === 'approved' ? 'selected' : '' }}>Poin Dicairkan</option>
                        <option value="rejected" {{ request('filter_status') === 'rejected' ? 'selected' : '' }}>Poin Ditolak</option>
                    </select>
                </div>

                <!-- Tombol Terapkan & Reset -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-sm flex-1 bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold h-9 shadow-none">
                        Terapkan Filter
                    </button>
                    @if($isFiltering)
                        <a href="{{ route('admin.pending-points') }}" class="btn btn-sm bg-white hover:bg-cream text-ink border border-ink/10 rounded-xl text-xs font-semibold h-9 shadow-none">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- Tabel Keseluruhan Mutasi -->
            <div class="overflow-x-auto">
                <table class="table w-full text-xs">
                    <thead>
                        <tr class="bg-cream/40 text-ink-soft border-b border-ink/5 font-semibold text-[10px] uppercase">
                            <th class="py-3 pl-3">Waktu Verifikasi & Ref</th>
                            <th class="py-3">Warga Pemohon</th>
                            <th class="py-3">Komoditas & Bobot</th>
                            <th class="py-3">Poin Reward</th>
                            <th class="py-3">Status Mutasi</th>
                            <th class="py-3 pr-3 text-right">Auditor / Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/5 font-medium">
                        @forelse($verifiedHistory as $h)
                            @php
                                $isApproved = in_array($h->status, ['berhasil_dikirim', 'completed', 'selesai']);
                            @endphp
                            <tr class="hover:bg-cream/20 transition-colors">
                                <td class="py-3.5 pl-3">
                                    <span class="font-mono font-bold text-ink block text-xs">{{ $h->deposit_code }}</span>
                                    <span class="text-[10px] text-ink-soft font-mono">{{ $h->updated_at->translatedFormat('d M Y, H:i') }} WITA</span>
                                </td>
                                <td class="py-3.5">
                                    <span class="font-bold text-ink block text-xs">{{ $h->user->name ?? 'Warga' }}</span>
                                    <span class="text-[10px] text-ink-soft font-mono">{{ $h->user->phone ?? '-' }}</span>
                                </td>
                                <td class="py-3.5">
                                    <span class="font-semibold text-ink block capitalize">{{ $h->category }}</span>
                                    <span class="text-[10px] text-ink-soft font-mono">{{ number_format($h->actual_weight ?? 0, 2) }} kg</span>
                                </td>
                                <td class="py-3.5 font-mono">
                                    @if($isApproved)
                                        <span class="text-forest font-bold text-xs block">+{{ number_format($h->points_earned ?? 0) }} Poin</span>
                                        <span class="text-[9px] text-ink-soft block font-sans">Masuk saldo</span>
                                    @else
                                        <span class="text-terracotta font-semibold text-xs block">0 Poin</span>
                                        <span class="text-[9px] text-ink-soft block font-sans">Tidak cair</span>
                                    @endif
                                </td>
                                <td class="py-3.5">
                                    @if($isApproved)
                                        <span class="inline-flex items-center whitespace-nowrap bg-forest/10 text-forest border border-forest/20 text-[10px] font-bold px-2 py-0.5 rounded">
                                            Dicairkan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center whitespace-nowrap bg-terracotta/10 text-terracotta border border-terracotta/20 text-[10px] font-bold px-2 py-0.5 rounded">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 pr-3 text-right max-w-[200px]">
                                    <span class="text-[11px] text-ink font-semibold block truncate">
                                        {{ $h->verifier->name ?? (str_contains($h->qc_notes ?? '', 'Auto-Release') ? 'Sistem Auto-Release' : 'Admin') }}
                                    </span>
                                    @if($h->qc_notes)
                                        <span class="text-[10px] text-ink-soft block truncate" title="{{ $h->qc_notes }}">
                                            {{ $h->qc_notes }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-ink-soft/60 text-xs font-medium">
                                    Tidak ada catatan mutasi poin yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($verifiedHistory->hasPages())
                <div class="pt-3 border-t border-ink/5">
                    {{ $verifiedHistory->links() }}
                </div>
            @endif

        </div>

    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL AUDIT VERIFIKASI POIN (DILETAKKAN DI LUAR TABEL) -->
<!-- ========================================================================= -->
@foreach($pendingTransfers as $t)
    @php
        $autoApproveAt = ($t->updated_at ?? $t->created_at)->addHours(24);
        $totalMenit = (int) now()->diffInMinutes($autoApproveAt, false);
        $sisaJam = intdiv(max($totalMenit, 0), 60);
        $sisaMenit = max($totalMenit, 0) % 60;

        $est = (float) ($t->estimated_weight ?? 0);
        $act = (float) ($t->actual_weight ?? 0);
        $selisih = $act - $est;
    @endphp
    
    <dialog id="audit_modal_{{ $t->id }}" class="modal modal-middle">
        <div class="modal-box w-11/12 max-w-lg bg-white rounded-3xl border border-ink/10 p-6 sm:p-7 text-left shadow-2xl overflow-y-auto max-h-[88vh] my-auto">
            
            <div class="flex items-start justify-between pb-3 border-b border-ink/5">
                <div>
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-forest bg-forest/10 px-2 py-0.5 rounded">
                        Audit Transaksi
                    </span>
                    <h3 class="font-bold text-base sm:text-lg text-ink mt-1">Verifikasi & Finalisasi Poin</h3>
                    <p class="text-xs text-ink-soft font-mono">Kode: <span class="font-bold text-ink">{{ $t->deposit_code }}</span></p>
                </div>
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost text-ink-soft hover:bg-cream">✕</button>
                </form>
            </div>

            <div class="space-y-4 pt-3.5">
                
                <!-- Identitas Warga & Kurir -->
                <div class="grid grid-cols-2 gap-3 bg-cream/30 p-3.5 rounded-2xl border border-ink/5">
                    <div>
                        <span class="text-[10px] text-ink-soft block font-bold uppercase tracking-wider">Pemohon / Warga</span>
                        <span class="font-bold text-ink block text-xs mt-0.5 truncate" title="{{ $t->user->name ?? 'Warga' }}">{{ $t->user->name ?? 'Warga' }}</span>
                        <span class="text-[10px] text-ink-soft font-mono">{{ $t->user->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-ink-soft block font-bold uppercase tracking-wider">Kurir Pemeriksa</span>
                        <span class="font-bold text-maritime block text-xs mt-0.5">{{ $t->penjemput->name ?? 'Kurir Lapangan' }}</span>
                        <span class="text-[10px] text-ink-soft font-mono">{{ ($t->updated_at ?? now())->format('d M Y, H:i') }} WITA</span>
                    </div>
                </div>

                <!-- Komparasi Timbangan & Deviasi -->
                <div class="border border-ink/5 rounded-2xl p-3.5 bg-white space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-ink">Hasil Timbangan Lapangan</span>
                        <span class="text-[10px] text-ink-soft font-medium uppercase">{{ $t->category }} ({{ $t->sub_category ?? '-' }})</span>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-2.5 text-center pt-1 font-mono">
                        <div class="p-2.5 rounded-xl bg-cream/40 border border-ink/5">
                            <span class="text-[9px] text-ink-soft font-sans block uppercase font-bold">Estimasi</span>
                            <span class="font-bold text-xs text-ink mt-0.5 block">{{ number_format($est, 2) }} kg</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-forest/10 border border-forest/10">
                            <span class="text-[9px] text-forest font-sans block uppercase font-bold">Aktual</span>
                            <span class="font-bold text-xs text-forest mt-0.5 block">{{ number_format($act, 2) }} kg</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-cream/40 border border-ink/5">
                            <span class="text-[9px] text-ink-soft font-sans block uppercase font-bold">Selisih</span>
                            <span class="font-bold text-xs mt-0.5 block {{ $selisih >= 0 ? 'text-forest' : 'text-terracotta' }}">
                                {{ $selisih > 0 ? '+' : '' }}{{ number_format($selisih, 2) }} kg
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Catatan Fisik 3C -->
                <div class="p-3 bg-cream/20 border border-ink/5 rounded-2xl text-xs space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-ink text-xs">Evaluasi SOP 3C:</span>
                        <span class="badge bg-forest/15 text-forest border-none text-[9px] font-bold px-2 py-0.5 rounded">Lolos 3C</span>
                    </div>
                    <p class="text-[11px] text-ink-soft italic leading-relaxed pt-0.5">
                        "{{ $t->qc_notes ?? 'Sampah dalam kondisi bersih, kering, dan terpilah rapi saat ditimbang oleh kurir.' }}"
                    </p>
                </div>

                <!-- Poin yang Dicairkan -->
                <div class="bg-forest/[0.04] border border-forest/20 p-4 rounded-2xl flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-ink-soft font-bold uppercase tracking-wider block">Total Poin Dicairkan</span>
                        <div class="text-[11px] text-ink-soft mt-0.5">
                            Auto-cair dlm: <strong class="font-mono text-ink">{{ $sisaJam }}j {{ $sisaMenit }}m</strong>
                        </div>
                    </div>
                    <div class="text-right font-mono">
                        <span class="text-2xl font-black text-forest">+{{ number_format($t->points_earned ?? 0) }}</span>
                        <span class="text-[10px] text-forest font-bold block font-sans">Poin Reward</span>
                    </div>
                </div>

                <!-- Tombol Aksi Finalisasi Admin -->
                <div class="pt-2 flex items-center gap-2.5">
                    <form action="{{ route('admin.points.approve', $t->id) }}" method="POST" class="flex-1 m-0 p-0">
                        @csrf
                        <input type="hidden" name="keputusan" value="setujui">
                        <button type="submit" class="btn btn-sm w-full bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-bold h-11 shadow-sm transition-all">
                            Setujui & Cairkan Poin Warga
                        </button>
                    </form>

                    <form action="{{ route('admin.points.approve', $t->id) }}" method="POST" class="m-0 p-0">
                        @csrf
                        <input type="hidden" name="keputusan" value="tolak">
                        <button type="submit" onclick="return confirm('Yakin ingin menolak pencairan poin untuk setoran ini?')"
                            class="btn btn-sm bg-white hover:bg-terracotta/10 text-terracotta border border-terracotta/30 rounded-xl text-xs font-bold h-11 px-4 shadow-none transition-all">
                            Tolak Poin
                        </button>
                    </form>
                </div>

            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-ink/40 backdrop-blur-xs"><button>close</button></form>
    </dialog>
@endforeach

<script>
    function switchMainTab(tabName) {
        const tabToday = document.getElementById('tab_content_today');
        const tabAll = document.getElementById('tab_content_all');
        const btnToday = document.getElementById('tab_btn_today');
        const btnAll = document.getElementById('tab_btn_all');

        if (tabName === 'today') {
            tabToday.classList.remove('hidden');
            tabAll.classList.add('hidden');

            btnToday.className = "px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-forest text-white shadow-xs";
            btnAll.className = "px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-cream/40 text-ink-soft hover:bg-cream";
        } else {
            tabToday.classList.add('hidden');
            tabAll.classList.remove('hidden');

            btnAll.className = "px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-forest text-white shadow-xs";
            btnToday.className = "px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-cream/40 text-ink-soft hover:bg-cream";
        }
    }
</script>
@endsection