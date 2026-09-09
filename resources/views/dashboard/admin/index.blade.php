@extends('layouts.dashboard', ['title' => 'Dashboard Admin — SulapaKarya'])

@section('dashboard-content')
@php
    // Metrik Pengguna Real-time
    $countWarga = \App\Models\User::where('role', 'user')->count();
    $countPenjemput = \App\Models\User::where('role', 'penjemput')->count();
    $countPengrajin = \App\Models\User::where('role', 'pengrajin')->count();

    // Pengecekan Aman Antrean Setoran
    $hasDepositsTable = \Illuminate\Support\Facades\Schema::hasTable('deposits');
    $countPendingSetoran = $hasDepositsTable 
        ? \App\Models\Deposit::where('status', 'pending')->count() 
        : 0;

    // Ambil 5 antrean setoran terbaru untuk tabel ringkasan
    $recentDeposits = $hasDepositsTable
        ? \App\Models\Deposit::with('user')->orderBy('created_at', 'desc')->take(5)->get()
        : collect();

    // Total Master Jenis Sampah
    $countWastePrices = \Illuminate\Support\Facades\Schema::hasTable('waste_prices')
        ? \App\Models\WastePrice::count()
        : 0;

    // Total Produk Kriya
    $countProducts = \Illuminate\Support\Facades\Schema::hasTable('products')
        ? \App\Models\Product::count()
        : 0;
@endphp

<div class="space-y-6 text-left">
    
    <!-- Header Ringkas -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-ink/5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">Panel Administrasi</h1>
            <p class="text-xs text-ink-soft mt-0.5">Monitoring operasional setoran sampah, verifikasi poin warga, dan tata kelola akun.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.waste-prices.index') }}" class="btn btn-sm bg-white hover:bg-cream/50 text-ink border border-ink/10 rounded-xl text-xs font-semibold px-3 shadow-none">
                Katalog Tarif Sampah
            </a>
            <a href="/verifikasi-setoran" class="btn btn-sm bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold px-3.5 shadow-sm">
                Buka Verifikasi
            </a>
        </div>
    </div>

    <!-- 4 Kartu Metrik Utama -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Card 1: Antrean Setoran -->
        <div class="bg-white rounded-2xl p-5 border border-ink/5 shadow-none flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-ink-soft">Antrean Verifikasi</span>
                <span class="w-2 h-2 rounded-full {{ $countPendingSetoran > 0 ? 'bg-terracotta' : 'bg-forest' }}"></span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-ink tracking-tight font-mono">{{ $countPendingSetoran }}</div>
                <div class="text-[11px] mt-1">
                    @if($countPendingSetoran > 0)
                        <span class="text-terracotta font-medium">Memerlukan validasi</span>
                    @else
                        <span class="text-forest font-medium">Semua berkas beres</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 2: Total Warga -->
        <div class="bg-white rounded-2xl p-5 border border-ink/5 shadow-none flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-ink-soft">Warga Terdaftar</span>
                <svg class="w-4 h-4 text-ink-soft/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-ink tracking-tight font-mono">{{ $countWarga }}</div>
                <div class="text-[11px] text-ink-soft mt-1">Nasabah aktif pilah sampah</div>
            </div>
        </div>

        <!-- Card 3: Kurir Lapangan -->
        <div class="bg-white rounded-2xl p-5 border border-ink/5 shadow-none flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-ink-soft">Armada Kurir</span>
                <svg class="w-4 h-4 text-ink-soft/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-ink tracking-tight font-mono">{{ $countPenjemput }}</div>
                <div class="text-[11px] text-maritime font-medium mt-1">Petugas verifikasi penjemputan</div>
            </div>
        </div>

        <!-- Card 4: Katalog & Produk -->
        <div class="bg-white rounded-2xl p-5 border border-ink/5 shadow-none flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-ink-soft">Katalog Komoditas</span>
                <svg class="w-4 h-4 text-ink-soft/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-ink tracking-tight font-mono">{{ $countWastePrices }} <span class="text-xs font-normal text-ink-soft font-sans">kategori</span></div>
                <div class="text-[11px] text-forest font-medium mt-1">Formula 40% poin aktif</div>
            </div>
        </div>

    </div>

    <!-- Tabel Monitoring Setoran Terbaru -->
    <div class="bg-white rounded-2xl border border-ink/5 p-5 shadow-none">
        <div class="flex items-center justify-between pb-4 border-b border-ink/5 mb-4">
            <div>
                <h2 class="text-sm font-bold text-ink">Aktivitas Penjemputan Terbaru</h2>
                <p class="text-[11px] text-ink-soft">Daftar transaksi setoran yang baru diajukan oleh warga.</p>
            </div>
            <a href="/verifikasi-setoran" class="text-xs font-semibold text-forest hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-xs">
                <thead>
                    <tr class="border-b border-ink/5 text-ink-soft/70 font-semibold uppercase text-[10px]">
                        <th class="py-2.5 pl-3">Warga / Pemohon</th>
                        <th class="py-2.5">Estimasi Berat</th>
                        <th class="py-2.5">Lokasi Jemput</th>
                        <th class="py-2.5">Waktu Pengajuan</th>
                        <th class="py-2.5">Status</th>
                        <th class="py-2.5 pr-3 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/5 font-medium">
                    @forelse($recentDeposits as $deposit)
                    <tr class="hover:bg-cream/20 transition-colors">
                        <td class="py-3 pl-3">
                            <span class="font-bold text-ink block">{{ $deposit->user->name ?? 'Warga' }}</span>
                            <span class="text-[10px] text-ink-soft font-mono">{{ $deposit->user->email ?? '-' }}</span>
                        </td>
                        <td class="py-3 font-mono font-semibold">
                            {{ $deposit->weight ?? $deposit->estimated_weight ?? 0 }} kg
                        </td>
                        <td class="py-3 max-w-[180px] truncate text-ink-soft">
                            {{ $deposit->address ?? 'Makassar' }}
                        </td>
                        <td class="py-3 text-ink-soft font-mono text-[11px]">
                            {{ $deposit->created_at ? $deposit->created_at->diffForHumans() : '-' }}
                        </td>
                        <td class="py-3">
                            @if($deposit->status === 'pending')
                                <span class="badge badge-xs bg-terracotta/10 text-terracotta border-none font-bold px-2 py-1 rounded">Pending</span>
                            @elseif($deposit->status === 'verified' || $deposit->status === 'completed')
                                <span class="badge badge-xs bg-forest/10 text-forest border-none font-bold px-2 py-1 rounded">Selesai</span>
                            @else
                                <span class="badge badge-xs bg-maritime/10 text-maritime border-none font-bold px-2 py-1 rounded">{{ ucfirst($deposit->status) }}</span>
                            @endif
                        </td>
                        <td class="py-3 pr-3 text-right">
                            <a href="/verifikasi-setoran" class="btn btn-xs bg-white hover:bg-cream border border-ink/10 rounded-lg text-[10px] font-bold">
                                Periksa
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-ink-soft/60">
                            Belum ada aktivitas setoran terbaru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modul Navigasi Aksi Cepat & Info QC -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
        
        <!-- 4 Modul Aksi Utama -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-ink/5 p-5 shadow-none">
            <div class="flex items-center justify-between pb-3 border-b border-ink/5 mb-3">
                <h2 class="text-sm font-bold text-ink">Manajemen Sistem</h2>
                <span class="text-[11px] text-ink-soft">Akses Cepat</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Modul 1 -->
                <a href="/verifikasi-setoran" class="p-3.5 rounded-xl border border-ink/5 hover:border-forest/40 bg-cream/10 hover:bg-cream/30 transition-colors flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-forest/10 text-forest flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-ink">Verifikasi Setoran</div>
                        <p class="text-[11px] text-ink-soft mt-0.5 leading-relaxed">Validasi timbangan dan pencairan poin konversi.</p>
                    </div>
                </a>

                <!-- Modul 2 -->
                <a href="{{ route('admin.waste-prices.index') }}" class="p-3.5 rounded-xl border border-ink/5 hover:border-forest/40 bg-cream/10 hover:bg-cream/30 transition-colors flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-forest/10 text-forest flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-ink">Tarif & Poin Sampah</div>
                        <p class="text-[11px] text-ink-soft mt-0.5 leading-relaxed">Kelola katalog 14 jenis sampah & rasio poin 40%.</p>
                    </div>
                </a>

                <!-- Modul 3 -->
                <a href="/kelola-pengguna" class="p-3.5 rounded-xl border border-ink/5 hover:border-forest/40 bg-cream/10 hover:bg-cream/30 transition-colors flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-forest/10 text-forest flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-ink">Manajemen Pengguna</div>
                        <p class="text-[11px] text-ink-soft mt-0.5 leading-relaxed">Atur perizinan akun warga, kurir, dan pengrajin.</p>
                    </div>
                </a>

                <!-- Modul 4 -->
                <a href="/kelola-produk" class="p-3.5 rounded-xl border border-ink/5 hover:border-forest/40 bg-cream/10 hover:bg-cream/30 transition-colors flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-forest/10 text-forest flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-ink">Katalog Produk Kriya</div>
                        <p class="text-[11px] text-ink-soft mt-0.5 leading-relaxed">Kelola ketersediaan karya upcycling dan kuota stok.</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Standar QC & SOP Validasi -->
        <div class="bg-white rounded-2xl border border-ink/5 p-5 shadow-none space-y-3">
            <div class="flex items-center justify-between pb-3 border-b border-ink/5">
                <span class="text-xs font-bold text-ink">Standar Kualitas (QC)</span>
                <span class="text-[10px] font-mono text-ink-soft bg-cream px-2 py-0.5 rounded">SOP 3C</span>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="p-2.5 rounded-xl bg-cream/20 border border-ink/5">
                    <span class="font-semibold text-ink block text-[11px]">1. Kebersihan (Clean)</span>
                    <p class="text-[10px] text-ink-soft mt-0.5 leading-relaxed">Bebas dari residu cairan organik, minyak, dan zat berbau tajam.</p>
                </div>

                <div class="p-2.5 rounded-xl bg-cream/20 border border-ink/5">
                    <span class="font-semibold text-ink block text-[11px]">2. Kekeringan (Dry)</span>
                    <p class="text-[10px] text-ink-soft mt-0.5 leading-relaxed">Kardus, kertas, dan kain perca tidak boleh dalam keadaan basah.</p>
                </div>

                <div class="p-2.5 rounded-xl bg-cream/20 border border-ink/5">
                    <span class="font-semibold text-ink block text-[11px]">3. Kerapian (Compact)</span>
                    <p class="text-[10px] text-ink-soft mt-0.5 leading-relaxed">Botol/gelas dipipihkan dan kardus terikat rapi untuk mempermudah timbang.</p>
                </div>
            </div>

            <a href="{{ route('cara-memilah') }}" class="btn btn-sm w-full bg-cream hover:bg-cream/80 text-ink border border-ink/10 rounded-xl text-xs font-semibold normal-case shadow-none mt-1">
                Buku Panduan Standar QC
            </a>
        </div>

    </div>

</div>
@endsection