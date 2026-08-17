@extends('layouts.dashboard', ['title' => 'Statistik Platform — SulapaKarya'])

@section('dashboard-content')
<div class="space-y-8 animate-fadeIn">

    <div class="bg-gradient-to-r from-ink to-ink/90 p-8 rounded-[2rem] text-white shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 dot-grid text-white/[0.03] pointer-events-none"></div>
        <div class="relative z-10 text-left">
            <h1 class="font-display font-extrabold text-3xl tracking-tight">Statistik Akumulasi Platform</h1>
            <p class="text-sm text-sand/70 font-medium mt-2 max-w-2xl">Ringkasan keseluruhan data operasional ekosistem SulapaKarya Makassar secara real-time.</p>
        </div>
    </div>

    {{-- ========== SECTION 1: PENGGUNA ========== --}}
    <div>
        <h2 class="font-display font-extrabold text-lg text-ink mb-4 flex items-center gap-2 text-left">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="text-forest"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Data Pengguna
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-white border border-ink/5 rounded-2xl p-5 text-center shadow-sm">
                <span class="text-3xl font-extrabold text-ink font-mono block">{{ number_format($totalUsers) }}</span>
                <span class="text-[11px] text-ink-soft font-bold mt-1 block uppercase tracking-wider">Total Akun</span>
            </div>
            <div class="bg-white border border-ink/5 rounded-2xl p-5 text-center shadow-sm">
                <span class="text-3xl font-extrabold text-forest font-mono block">{{ number_format($usersByRole['user'] ?? 0) }}</span>
                <span class="text-[11px] text-ink-soft font-bold mt-1 block uppercase tracking-wider">Warga</span>
            </div>
            <div class="bg-white border border-ink/5 rounded-2xl p-5 text-center shadow-sm">
                <span class="text-3xl font-extrabold text-maritime font-mono block">{{ number_format($usersByRole['penjemput'] ?? 0) }}</span>
                <span class="text-[11px] text-ink-soft font-bold mt-1 block uppercase tracking-wider">Kurir</span>
            </div>
            <div class="bg-white border border-ink/5 rounded-2xl p-5 text-center shadow-sm">
                <span class="text-3xl font-extrabold text-purple-600 font-mono block">{{ number_format($usersByRole['pengrajin'] ?? 0) }}</span>
                <span class="text-[11px] text-ink-soft font-bold mt-1 block uppercase tracking-wider">Pengrajin</span>
            </div>
            <div class="bg-white border border-ink/5 rounded-2xl p-5 text-center shadow-sm">
                <span class="text-3xl font-extrabold text-terracotta font-mono block">{{ number_format($usersByRole['admin'] ?? 0) }}</span>
                <span class="text-[11px] text-ink-soft font-bold mt-1 block uppercase tracking-wider">Admin</span>
            </div>
        </div>
    </div>

    {{-- ========== SECTION 2: SETORAN SAMPAH ========== --}}
    <div>
        <h2 class="font-display font-extrabold text-lg text-ink mb-4 flex items-center gap-2 text-left">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="text-forest"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            Setoran Sampah
        </h2>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
            <div class="bg-gradient-to-br from-white to-forest-light/40 border border-ink/5 border-l-4 border-l-forest rounded-2xl p-5 shadow-sm text-left">
                <span class="text-[11px] text-ink-soft font-bold uppercase tracking-wider block">Total Setoran</span>
                <span class="text-3xl font-extrabold text-forest-dark font-mono mt-1 block">{{ number_format($totalDeposits) }}</span>
                <span class="text-[10px] text-ink-soft font-medium">pengajuan masuk</span>
            </div>
            <div class="bg-gradient-to-br from-white to-forest-light/40 border border-ink/5 border-l-4 border-l-forest rounded-2xl p-5 shadow-sm text-left">
                <span class="text-[11px] text-ink-soft font-bold uppercase tracking-wider block">Berat Terkumpul</span>
                <span class="text-3xl font-extrabold text-forest-dark font-mono mt-1 block">{{ number_format($totalWeightCollected, 1, ',', '.') }}</span>
                <span class="text-[10px] text-ink-soft font-medium">kg (aktual terverifikasi)</span>
            </div>
            <div class="bg-gradient-to-br from-white to-maritime-light/40 border border-ink/5 border-l-4 border-l-maritime rounded-2xl p-5 shadow-sm text-left">
                <span class="text-[11px] text-ink-soft font-bold uppercase tracking-wider block">Estimasi Berat</span>
                <span class="text-3xl font-extrabold text-maritime-dark font-mono mt-1 block">{{ number_format($totalEstimatedWeight, 1, ',', '.') }}</span>
                <span class="text-[10px] text-ink-soft font-medium">kg (semua pengajuan)</span>
            </div>
            <div class="bg-gradient-to-br from-white to-terracotta-light/40 border border-ink/5 border-l-4 border-l-terracotta rounded-2xl p-5 shadow-sm text-left">
                <span class="text-[11px] text-ink-soft font-bold uppercase tracking-wider block">Selesai</span>
                <span class="text-3xl font-extrabold text-forest font-mono mt-1 block">{{ number_format($depositsByStatus['selesai'] ?? 0) }}</span>
                <span class="text-[10px] text-ink-soft font-medium">dari {{ $totalDeposits }} setoran</span>
            </div>
        </div>

        {{-- Status breakdown --}}
        <div class="bg-white border border-ink/5 rounded-2xl p-6 shadow-sm mb-5">
            <h3 class="font-bold text-sm text-ink mb-4 text-left">Sebaran Status Setoran</h3>
            <div class="flex flex-wrap gap-3">
                @php
                    $statusLabels = [
                        'pending' => ['label' => 'Menunggu', 'color' => 'bg-amber-100 text-amber-700'],
                        'menunggu_admin' => ['label' => 'Menunggu Admin', 'color' => 'bg-amber-100 text-amber-700'],
                        'menunggu_penjemput' => ['label' => 'Menunggu Kurir', 'color' => 'bg-maritime/10 text-maritime'],
                        'penjemput_menuju_lokasi' => ['label' => 'Kurir OTW', 'color' => 'bg-blue-100 text-blue-700'],
                        'penjemput_tiba' => ['label' => 'Kurir Tiba', 'color' => 'bg-purple-100 text-purple-700'],
                        'selesai' => ['label' => 'Selesai', 'color' => 'bg-forest/10 text-forest'],
                        'ditolak' => ['label' => 'Ditolak', 'color' => 'bg-terracotta/10 text-terracotta'],
                    ];
                @endphp
                @foreach($depositsByStatus as $status => $count)
                    @php $info = $statusLabels[$status] ?? ['label' => ucfirst($status), 'color' => 'bg-ink/10 text-ink-soft']; @endphp
                    <div class="flex items-center gap-2 {{ $info['color'] }} px-4 py-2.5 rounded-xl text-xs font-bold">
                        <span>{{ $info['label'] }}</span>
                        <span class="font-mono font-extrabold text-base">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Per-category breakdown --}}
        <div class="bg-white border border-ink/5 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                <h3 class="font-bold text-sm text-ink text-left">Akumulasi per Kategori Sampah</h3>
                <a href="{{ route('admin.statistics.export') }}" class="btn btn-sm bg-forest border-none text-white hover:bg-forest-dark rounded-xl normal-case font-bold text-xs px-4 shadow-sm flex items-center gap-1.5">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download Excel
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="table w-full text-sm">
                    <thead>
                        <tr class="bg-cream/60 border-b border-ink/5 text-ink/70 font-bold uppercase tracking-wider text-xs">
                            <th class="py-3 pl-4 text-left">Nama Barang</th>
                            <th class="py-3 text-left">Kategori Sampah</th>
                            <th class="py-3 text-center">Jumlah Setoran</th>
                            <th class="py-3 pr-4 text-right">Total Berat (Kg)</th>
                        </tr>
                    </thead>
                    <tbody class="font-medium">
                        @forelse($depositsByCategory as $cat)
                            <tr class="border-b border-ink/5 hover:bg-cream/20 transition-colors">
                                <td class="py-3.5 pl-4">
                                    <span class="font-bold text-ink">{{ $cat->sub_category ?? '-' }}</span>
                                </td>
                                <td class="py-3.5">
                                    <span class="badge bg-cream border border-ink/10 text-ink-soft font-bold text-[10px] px-2 py-1 rounded-md capitalize">{{ $cat->category }}</span>
                                </td>
                                <td class="py-3.5 text-center font-mono font-bold text-ink">{{ number_format($cat->total) }}</td>
                                <td class="py-3.5 pr-4 text-right font-mono font-bold text-forest">{{ number_format($cat->total_weight, 1, ',', '.') }} kg</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-ink-soft/50 text-xs">Belum ada data setoran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ========== SECTION 3: EKONOMI POIN ========== --}}
    <div>
        <h2 class="font-display font-extrabold text-lg text-ink mb-4 flex items-center gap-2 text-left">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="text-maritime"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/></svg>
            Ekonomi Poin Kriya
        </h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-ink/5 rounded-2xl p-5 shadow-sm text-left">
                <span class="text-[11px] text-ink-soft font-bold uppercase tracking-wider block">Total Poin Didistribusi</span>
                <span class="text-2xl font-extrabold text-forest font-mono mt-1 block">{{ number_format($totalPointsDistributed, 0, ',', '.') }}</span>
                <span class="text-[10px] text-ink-soft font-medium">dari {{ number_format($totalPointTransfers) }} transfer</span>
            </div>
            <div class="bg-white border border-ink/5 rounded-2xl p-5 shadow-sm text-left">
                <span class="text-[11px] text-ink-soft font-bold uppercase tracking-wider block">Poin Beredar</span>
                <span class="text-2xl font-extrabold text-maritime font-mono mt-1 block">{{ number_format($totalPointsCirculating, 0, ',', '.') }}</span>
                <span class="text-[10px] text-ink-soft font-medium">saldo gabungan semua warga</span>
            </div>
            <div class="bg-white border border-ink/5 rounded-2xl p-5 shadow-sm text-left">
                <span class="text-[11px] text-ink-soft font-bold uppercase tracking-wider block">Poin Ditukar Belanja</span>
                <span class="text-2xl font-extrabold text-terracotta font-mono mt-1 block">{{ number_format($totalPointsRedeemed, 0, ',', '.') }}</span>
                <span class="text-[10px] text-ink-soft font-medium">via pembelian produk kriya</span>
            </div>
            <div class="bg-white border border-ink/5 rounded-2xl p-5 shadow-sm text-left">
                <span class="text-[11px] text-ink-soft font-bold uppercase tracking-wider block">Produk di Katalog</span>
                <span class="text-2xl font-extrabold text-purple-600 font-mono mt-1 block">{{ number_format($totalProducts) }}</span>
                <span class="text-[10px] text-ink-soft font-medium">kerajinan UMKM terdaftar</span>
            </div>
        </div>
    </div>

    {{-- ========== SECTION 4: TRANSAKSI MARKETPLACE ========== --}}
    <div>
        <h2 class="font-display font-extrabold text-lg text-ink mb-4 flex items-center gap-2 text-left">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="text-terracotta"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            Transaksi Marketplace
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-ink/5 rounded-2xl p-5 shadow-sm text-left">
                <span class="text-[11px] text-ink-soft font-bold uppercase tracking-wider block">Total Transaksi</span>
                <span class="text-3xl font-extrabold text-ink font-mono mt-1 block">{{ number_format($totalTransactions) }}</span>
                <span class="text-[10px] text-ink-soft font-medium">{{ number_format($successTransactions) }} berhasil</span>
            </div>
            <div class="bg-white border border-ink/5 rounded-2xl p-5 shadow-sm text-left">
                <span class="text-[11px] text-ink-soft font-bold uppercase tracking-wider block">Omzet Kotor</span>
                <span class="text-3xl font-extrabold text-forest font-mono mt-1 block">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                <span class="text-[10px] text-ink-soft font-medium">dari transaksi sukses</span>
            </div>
            <div class="bg-white border border-ink/5 rounded-2xl p-5 shadow-sm text-left">
                <span class="text-[11px] text-ink-soft font-bold uppercase tracking-wider block">Rata-rata per Transaksi</span>
                <span class="text-3xl font-extrabold text-maritime font-mono mt-1 block">Rp {{ $successTransactions > 0 ? number_format($totalRevenue / $successTransactions, 0, ',', '.') : '0' }}</span>
                <span class="text-[10px] text-ink-soft font-medium">nilai pesanan rata-rata</span>
            </div>
        </div>
    </div>

    {{-- ========== SECTION 5: TREN BULANAN ========== --}}
    @if($monthlyDeposits->count() > 0)
    <div>
        <h2 class="font-display font-extrabold text-lg text-ink mb-4 flex items-center gap-2 text-left">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="text-forest"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            Tren Setoran 6 Bulan Terakhir
        </h2>
        <div class="bg-white border border-ink/5 rounded-2xl p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="table w-full text-sm">
                    <thead>
                        <tr class="bg-cream/60 border-b border-ink/5 text-ink/70 font-bold uppercase tracking-wider text-xs">
                            <th class="py-3 pl-4 text-left">Bulan</th>
                            <th class="py-3 text-center">Jumlah Setoran</th>
                            <th class="py-3 pr-4 text-right">Berat Selesai (Kg)</th>
                        </tr>
                    </thead>
                    <tbody class="font-medium">
                        @foreach($monthlyDeposits as $m)
                            <tr class="border-b border-ink/5 hover:bg-cream/20 transition-colors">
                                <td class="py-3.5 pl-4 font-bold text-ink">{{ \Carbon\Carbon::parse($m->bulan . '-01')->translatedFormat('F Y') }}</td>
                                <td class="py-3.5 text-center font-mono font-bold text-ink">{{ number_format($m->total) }}</td>
                                <td class="py-3.5 pr-4 text-right font-mono font-bold text-forest">{{ number_format($m->berat, 1, ',', '.') }} kg</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ========== SECTION 6: TOP 5 WARGA ========== --}}
    @if($topWarga->count() > 0)
    <div>
        <h2 class="font-display font-extrabold text-lg text-ink mb-4 flex items-center gap-2 text-left">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="text-amber-500"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Top 5 Kontributor Sampah
        </h2>
        <div class="bg-white border border-ink/5 rounded-2xl p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="table w-full text-sm">
                    <thead>
                        <tr class="bg-cream/60 border-b border-ink/5 text-ink/70 font-bold uppercase tracking-wider text-xs">
                            <th class="py-3 pl-4 text-left">Peringkat</th>
                            <th class="py-3 text-left">Nama Warga</th>
                            <th class="py-3 text-center">Setoran Selesai</th>
                            <th class="py-3 text-right">Total Berat (Kg)</th>
                            <th class="py-3 pr-4 text-right">Saldo Poin</th>
                        </tr>
                    </thead>
                    <tbody class="font-medium">
                        @foreach($topWarga as $i => $w)
                            <tr class="border-b border-ink/5 hover:bg-cream/20 transition-colors">
                                <td class="py-3.5 pl-4">
                                    @if($i === 0)
                                        <span class="w-7 h-7 rounded-full bg-amber-100 text-amber-600 inline-flex items-center justify-center font-extrabold text-xs shadow-sm">1</span>
                                    @elseif($i === 1)
                                        <span class="w-7 h-7 rounded-full bg-gray-100 text-gray-500 inline-flex items-center justify-center font-extrabold text-xs shadow-sm">2</span>
                                    @elseif($i === 2)
                                        <span class="w-7 h-7 rounded-full bg-orange-100 text-orange-500 inline-flex items-center justify-center font-extrabold text-xs shadow-sm">3</span>
                                    @else
                                        <span class="w-7 h-7 rounded-full bg-ink/5 text-ink-soft inline-flex items-center justify-center font-bold text-xs">{{ $i + 1 }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 font-bold text-ink">{{ $w->name }}</td>
                                <td class="py-3.5 text-center font-mono font-bold text-ink">{{ number_format($w->selesai_count) }}x</td>
                                <td class="py-3.5 text-right font-mono font-bold text-forest">{{ number_format($w->total_berat ?? 0, 1, ',', '.') }} kg</td>
                                <td class="py-3.5 pr-4 text-right font-mono font-bold text-maritime">{{ number_format($w->points_balance, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
