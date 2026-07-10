@extends('layouts.dashboard', ['title' => 'Dashboard Admin — SulapaKarya Macca'])

@section('dashboard-content')
@php
    // Mengambil metrik data pengguna secara real-time dari database
    $countWarga = \App\Models\User::where('role', 'user')->count();
    $countPenjemput = \App\Models\User::where('role', 'penjemput')->count();
    $countPengrajin = \App\Models\User::where('role', 'pengrajin')->count();

    // Pengecekan aman untuk antrean setoran sampah (mencegah error jika tabel belum dimigrasi)
    $countPendingSetoran = \Illuminate\Support\Facades\Schema::hasTable('waste_deposits') 
        ? \App\Models\WasteDeposit::where('status', 'pending')->count() 
        : 0;
@endphp

<div class="space-y-8 animate-fadeIn">
    
    <!-- ============ HEADER SAMBUTAN PANEL ADMIN ============ -->
    <div class="bg-gradient-to-r from-white to-cream p-6 rounded-[1.5rem] border border-ink/5 shadow-md shadow-ink/[0.01] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="text-left">
            <h1 class="font-display font-extrabold text-2xl text-ink leading-tight">Selamat Datang di Panel Utama, Admin!</h1>
            <p class="text-xs text-ink-soft font-semibold mt-1">Pantau perputaran koin kriya, verifikasi setoran sampah, dan kelola ekosistem SulapaKarya Macca kota Makassar.</p>
        </div>
        <div class="shrink-0 bg-forest/10 text-forest px-4 py-2 rounded-xl border border-forest/10 text-xs font-bold font-mono">
            Mode Akses: Root Admin Utama
        </div>
    </div>

    <!-- ============ KARTU RINGKASAN METRIK UTAMA ============ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Antrean Setoran Sampah (Aksen Oranye Terracotta) -->
        <div class="bg-gradient-to-br from-white via-white to-terracotta-light/40 border border-ink/5 border-l-4 border-l-terracotta rounded-2xl p-5 flex items-center gap-4 shadow-sm transition-all hover:shadow-md">
            <div class="w-12 h-12 rounded-xl bg-terracotta text-white flex items-center justify-center shrink-0 shadow-md shadow-terracotta/20">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
            </div>
            <div class="text-left">
                <span class="text-xs text-ink-soft font-bold tracking-wide block">Antrean Setoran</span>
                <p class="text-3xl font-extrabold text-terracotta-dark mt-0.5">{{ $countPendingSetoran }} <span class="text-xs font-bold text-ink-soft">Berkas</span></p>
                <span class="text-[10px] bg-terracotta/10 text-terracotta font-extrabold px-2 py-0.5 rounded mt-1.5 inline-block">Butuh Verifikasi</span>
            </div>
        </div>

        <!-- Card 2: Total Warga Terdaftar (Aksen Hijau Kebun) -->
        <div class="bg-gradient-to-br from-white via-white to-forest-light/60 border border-ink/5 border-l-4 border-l-forest rounded-2xl p-5 flex items-center gap-4 shadow-sm transition-all hover:shadow-md">
            <div class="w-12 h-12 rounded-xl bg-forest text-white flex items-center justify-center shrink-0 shadow-md shadow-forest/20">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <div class="text-left">
                <span class="text-xs text-ink-soft font-bold tracking-wide block">Anggota Warga</span>
                <p class="text-3xl font-extrabold text-forest-dark mt-0.5">{{ $countWarga }} <span class="text-xs font-bold text-ink-soft">Jiwa</span></p>
                <span class="text-[10px] bg-forest/10 text-forest font-extrabold px-2 py-0.5 rounded mt-1.5 inline-block">Pilah Sampah Aktif</span>
            </div>
        </div>

        <!-- Card 3: Armada Penjemput / Kurir (Aksen Biru Maritim) -->
        <div class="bg-gradient-to-br from-white via-white to-maritime-light/60 border border-ink/5 border-l-4 border-l-maritime rounded-2xl p-5 flex items-center gap-4 shadow-sm transition-all hover:shadow-md">
            <div class="w-12 h-12 rounded-xl bg-maritime text-white flex items-center justify-center shrink-0 shadow-md shadow-maritime/20">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <div class="text-left">
                <span class="text-xs text-ink-soft font-bold tracking-wide block">Kurir Penjemput</span>
                <p class="text-3xl font-extrabold text-maritime-dark mt-0.5">{{ $countPenjemput }} <span class="text-xs font-bold text-ink-soft">Akun</span></p>
                <span class="text-[10px] bg-maritime/10 text-maritime font-extrabold px-2 py-0.5 rounded mt-1.5 inline-block">Armada Lapangan</span>
            </div>
        </div>
            

    </div>

    <!-- ============ PANEL AKSES CEPAT TINDAKAN UTAMA ============ -->
    <div class="bg-white border border-ink/5 rounded-[1.5rem] p-6 shadow-sm">
        <div class="mb-5 text-left">
            <h2 class="font-display font-extrabold text-xl text-ink">Pusat Kendali Akses Cepat</h2>
            <p class="text-xs text-ink-soft font-medium">Gunakan pintasan di bawah ini untuk mengelola operasional harian bank sampah SulapaKarya.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <!-- Tombol Pintasan Kelola User -->
            <a href="/kelola-pengguna" class="flex items-center justify-between p-5 bg-cream/40 border border-ink/5 rounded-2xl hover:bg-cream hover:border-forest/30 transition-all group">
                <div class="flex items-center gap-4 text-left">
                    <div class="w-10 h-10 rounded-xl bg-forest/10 text-forest flex items-center justify-center font-bold">
                        👥
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-ink group-hover:text-forest transition-colors">Manajemen Hak Akses Akun</h3>
                        <p class="text-[11px] text-ink-soft font-medium mt-0.5">Ubah role akun warga biasa menjadi kurir lapangan atau pengrajin kriya.</p>
                    </div>
                </div>
                <span class="text-xs text-ink-soft font-extrabold group-hover:translate-x-1 transition-transform">Buka &rarr;</span>
            </a>

            <!-- Tombol Pintasan Verifikasi Sampah -->
            <a href="/verifikasi-setoran" class="flex items-center justify-between p-5 bg-cream/40 border border-ink/5 rounded-2xl hover:bg-cream hover:border-terracotta/30 transition-all group">
                <div class="flex items-center gap-4 text-left">
                    <div class="w-10 h-10 rounded-xl bg-terracotta/10 text-terracotta flex items-center justify-center font-bold">
                        ⚖️
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-ink group-hover:text-terracotta transition-colors">Verifikasi Setoran Sampah Warga</h3>
                        <p class="text-[11px] text-ink-soft font-medium mt-0.5">Validasi berat timbangan sampah masuk untuk otomatis mentransfer poin hadiah.</p>
                    </div>
                </div>
                <span class="text-xs text-ink-soft font-extrabold group-hover:translate-x-1 transition-transform">Buka &rarr;</span>
            </a>

        </div>
    </div>

</div>
@endsection