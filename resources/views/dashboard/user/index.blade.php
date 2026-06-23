@extends('layouts.dashboard', ['title' => 'Dashboard User — SulapaKarya Macca'])

@section('dashboard-content')
@php
    // SOLUSI UTAMA: Mengambil data user secara real-time dari database berdasarkan session login Anda
    $currentUser = \App\Models\User::find(session('user_id'));
@endphp

<div class="space-y-8 animate-fadeIn">
    
    <!-- ============ HEADER SAMBUTAN & QR CODE ============ -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gradient-to-r from-white to-cream p-6 rounded-[1.5rem] border border-ink/5 shadow-md shadow-ink/[0.01]">
        <div class="flex items-center gap-4">
            
            <!-- Avatar Lingkaran Besar Kini Menarik Foto Profil Riil Database -->
            <div class="avatar {{ $currentUser && $currentUser->foto_profil ? '' : 'placeholder' }}">
                <div class="bg-gradient-to-tr from-forest to-forest-dark text-white rounded-full w-16 h-16 shadow-lg shadow-forest/20 ring-4 ring-white overflow-hidden flex items-center justify-center">
                    @if($currentUser && $currentUser->foto_profil)
                        <!-- Menampilkan Foto Profil dari Storage Lokal -->
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($currentUser->foto_profil) }}?v={{ time() }}" alt="Foto Profil {{ $currentUser->name }}" class="w-full h-full object-cover" />
                    @else
                        <!-- Fallback: Inisial Huruf Nama jika Foto Belum Diunggah -->
                        <span class="text-xl font-bold font-display">{{ strtoupper(substr(session('name', 'A'), 0, 1)) }}</span>
                    @endif
                </div>
            </div>
            
            <div>
                <h1 class="font-display font-extrabold text-2xl text-ink leading-tight">Halo, {{ session('name', 'Andi') }}</h1>
                <p class="text-xs text-ink-soft font-semibold mt-1">Mari pilah sampah bersama untuk lestarikan lingkungan Makassar!</p>
            </div>
        </div>

        <!-- PERBAIKAN: Kartu Akses Cepat QR Code Kini Bisa Diklik & Memicu Modal Dialog -->
        <div onclick="qr_code_modal.showModal()" 
             class="flex items-center gap-3 bg-white border border-forest/10 p-3 rounded-2xl w-full sm:w-auto shadow-sm cursor-pointer hover:border-forest/40 hover:bg-forest/[0.01] active:scale-95 transition-all duration-200 group">
            <div class="bg-forest-light text-forest p-2 rounded-xl border border-forest/10 group-hover:bg-forest group-hover:text-white transition-all">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><path d="M7 7h.01M17 7h.01M7 17h.01M17 17h.01"/></svg>
            </div>
            <div class="text-left">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-ink-soft/60 block">ID Digital Saya</span>
                <span class="text-xs font-mono font-bold text-forest truncate block w-28 group-hover:text-forest-dark">Lihat QR Code</span>
            </div>
        </div>
    </div>

    <!-- ============ SPANDUK PERINGATAN ALAMAT (BULLETPROOF CHECK) ============ -->
    @if(empty($currentUser) || empty($currentUser->address))
        <div class="alert bg-terracotta-light/70 border border-terracotta/20 rounded-2xl p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 shadow-sm">
            <div class="flex gap-3 items-start text-sm text-terracotta font-semibold">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0 mt-0.5"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 14h.01M12 8v5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <div>
                    <p class="text-ink font-extrabold">Lokasi Penjemputan Belum Diatur!</p>
                    <p class="text-xs text-ink-soft/90 font-medium mt-0.5">Lengkapi alamat rumah Anda di Makassar agar armada penjemput kami bisa memproses setoran sampah Anda.</p>
                </div>
            </div>
            <a href="/profile" class="btn btn-sm bg-terracotta border-none text-white hover:bg-terracotta-dark rounded-xl normal-case w-full sm:w-auto shadow-md shadow-terracotta/10 font-bold px-4">Lengkapi Profil</a>
        </div>
    @endif

    <!-- ============ RINGKASAN KEUANGAN & METRIK ============ -->
    <div>
        <div class="mb-4">
            <h2 class="font-display font-extrabold text-xl text-ink">Ringkasan Keuangan</h2>
            <p class="text-xs text-ink-soft font-medium">Pantau poin akumulasi dan pendapatan tunai dari hasil aksi pilah sampahmu.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Card 1: Total Setor (Aksen Hijau Kebun) -->
            <div class="bg-gradient-to-br from-white via-white to-forest-light/60 border border-ink/5 border-l-4 border-l-forest rounded-2xl p-5 flex items-center gap-4 shadow-sm transition-all hover:shadow-md">
                <div class="w-12 h-12 rounded-xl bg-forest text-white flex items-center justify-center shrink-0 shadow-md shadow-forest/20">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                </div>
                <div>
                    <span class="text-xs text-ink-soft font-bold tracking-wide block">Total Setor Sampah</span>
                    <p class="text-3xl font-extrabold text-forest-dark mt-0.5">45 <span class="text-sm font-bold text-ink-soft">Kg</span></p>
                    <span class="text-[10px] bg-forest/10 text-forest font-extrabold px-2.5 py-0.5 rounded-full mt-2 inline-block">Riwayat Akumulasi</span>
                </div>
            </div>

            <!-- Card 2: Saldo Poin (Dinamis dari Kolom points_balance) -->
            <div class="bg-gradient-to-br from-white via-white to-maritime-light/60 border border-ink/5 border-l-4 border-l-maritime rounded-2xl p-5 flex items-center gap-4 shadow-sm transition-all hover:shadow-md">
                <div class="w-12 h-12 rounded-xl bg-maritime text-white flex items-center justify-center shrink-0 shadow-md shadow-maritime/20">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="8"/><path d="M12 7v10M9 12h6"/></svg>
                </div>
                <div>
                    <span class="text-xs text-ink-soft font-bold tracking-wide block">Saldo Poin Kriya</span>
                    <p class="text-3xl font-extrabold text-maritime-dark mt-0.5">
                        {{ number_format($currentUser->points_balance ?? 0, 0, ',', '.') }} 
                        <span class="text-xs font-bold text-ink-soft">Poin</span>
                    </p>
                    <span class="text-[10px] text-maritime font-bold mt-2 block">Setara Rp {{ number_format(($currentUser->points_balance ?? 0) * 10, 0, ',', '.') }} potongan kriya</span>
                </div>
            </div>

            <!-- Card 3: Total Uang Tunai (Dinamis dari Kolom cash_received_total) -->
            <div class="bg-gradient-to-br from-white via-white to-terracotta-light/60 border border-ink/5 border-l-4 border-l-terracotta rounded-2xl p-5 flex items-center gap-4 shadow-sm transition-all hover:shadow-md">
                <div class="w-12 h-12 rounded-xl bg-terracotta text-white flex items-center justify-center shrink-0 shadow-md shadow-terracotta/20">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
                </div>
                <div>
                    <span class="text-xs text-ink-soft font-bold tracking-wide block">Total Uang Tunai</span>
                    <p class="text-3xl font-extrabold text-terracotta-dark mt-0.5">
                        <span class="text-sm font-extrabold text-ink-soft">Rp</span> 
                        {{ number_format($currentUser->cash_received_total ?? 0, 0, ',', '.') }}
                    </p>
                    <span class="text-[10px] bg-terracotta/10 text-terracotta font-extrabold px-2.5 py-0.5 rounded-full mt-2 inline-block">Sudah Dicairkan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ TOMBOL UTAMA SETOR SAMPAH (ACTION BANNER EMAS/HIJAU) ============ -->
    <div class="bg-gradient-to-r from-forest via-forest to-forest-dark p-6 rounded-[1.5rem] text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-xl shadow-forest/10 relative overflow-hidden group">
        <div class="absolute inset-0 dot-grid text-white/[0.03] pointer-events-none"></div>
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-sand/10 blur-2xl rounded-full group-hover:scale-125 transition-all duration-700"></div>

        <div class="relative z-10">
            <h3 class="font-display font-bold text-xl text-cream">Ingin melakukan penyetoran hari ini?</h3>
            <p class="text-xs text-cream/75 mt-1 font-medium">Akses cepat untuk menjadwalkan penjemputan baru oleh kurir armada SulapaKarya Macca.</p>
        </div>
        <a href="/setor-sampah" class="btn bg-white hover:bg-cream text-forest border-none px-6 rounded-xl font-extrabold text-sm normal-case shadow-md shrink-0 w-full sm:w-auto relative z-10 active:scale-95 transition-all">
            Setor Sampah Baru
        </a>
    </div>

    <!-- ============ KATALOG KERAJINAN PREVIEW ============ -->
    <div>
        <div class="flex justify-between items-end mb-4">
            <div>
                <h2 class="font-display font-extrabold text-xl text-ink">Katalog Kerajinan Pilihan</h2>
                <p class="text-xs text-ink-soft font-medium">Tukarkan poin akumulasi Anda dengan produk UMKM kriya binaan kota kita.</p>
            </div>
            <a href="/katalog" class="btn btn-sm btn-outline border-forest/20 text-forest hover:bg-forest hover:text-white rounded-xl text-xs normal-case font-bold px-4 transition-all shadow-sm">Lihat Katalog</a>
        </div>

        <!-- 4 Grid Placeholder Kartu Produk Kriya -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @for ($i = 1; $i <= 4; $i++)
                <div class="bg-white border border-ink/5 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all group">
                    <div class="aspect-video bg-sand/20 border-b border-ink/5 flex items-center justify-center text-ink-soft/30 relative">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                    <div class="p-4 text-left">
                        <div class="h-3.5 bg-ink/10 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-ink/5 rounded w-1/2"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- ============ RIWAYAT SETORAN SAMPAH ============ -->
    <div class="bg-white border border-ink/5 rounded-[1.5rem] p-6 shadow-sm">
        <div class="mb-5 text-left">
            <h2 class="font-display font-extrabold text-xl text-ink">Riwayat Setoran</h2>
            <p class="text-xs text-ink-soft font-medium">Daftar pengiriman sampah Anda yang telah diverifikasi beserta status keuntungannya.</p>
        </div>

        <!-- Tabel Riwayat Data -->
        <div class="overflow-x-auto rounded-xl border border-ink/5">
            <table class="table w-full text-sm">
                <thead>
                    <tr class="border-b border-ink/5 text-ink/70 font-bold uppercase tracking-wider text-xs bg-cream/60">
                        <th class="py-3.5 pl-5">Tanggal</th>
                        <th class="py-3.5">Jenis Sampah</th>
                        <th class="py-3.5">Berat</th>
                        <th class="py-3.5">Status</th>
                        <th class="py-3.5 pr-5 text-right">Hadiah / Profit</th>
                    </tr>
                </thead>
                <tbody class="font-medium text-ink/90">
                    <tr class="hover:bg-cream/10 border-b border-ink/5 transition-colors">
                        <td class="py-4 pl-5 text-ink-soft font-semibold">17 Juni 2026</td>
                        <td class="py-4">
                            <span class="inline-flex items-center gap-1.5 font-semibold">
                                <span class="w-2.5 h-2.5 rounded-full bg-maritime shadow-sm"></span> Kertas & Kardus
                            </span>
                        </td>
                        <td class="py-4 font-mono font-bold text-ink">10 kg</td>
                        <td class="py-4">
                            <span class="badge bg-forest/10 border-none text-forest text-xs font-bold px-3 py-2.5 rounded-lg shadow-inner">Selesai</span>
                        </td>
                        <td class="py-4 pr-5 text-right font-bold text-forest font-mono tracking-wide">+1.450 Poin</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============ MODAL POPUP DIALOG: ID DIGITAL QR CODE ============ -->
    <dialog id="qr_code_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white max-w-sm rounded-[2rem] border border-ink/5 p-6 text-center flex flex-col items-center relative shadow-2xl">
            <!-- Tombol Silang Pojok Kanan Atas -->
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft/70 hover:text-ink">✕</button>
            </form>
            
            <h3 class="font-display font-extrabold text-xl text-ink mt-3">ID Digital Anda</h3>
            <p class="text-xs text-ink-soft font-semibold mt-1 px-4">Tunjukkan kode QR ini kepada kurir armada SulapaKarya saat melakukan penjemputan sampah.</p>
            
            <!-- Wadah Bingkai Gambar QR Code -->
            <div class="bg-cream p-4 rounded-3xl border border-ink/5 my-6 shadow-inner flex items-center justify-center">
                @if($currentUser && $currentUser->qr_code)
                    <!-- Menggunakan API qrserver untuk generate gambar dari UUID secara real-time -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($currentUser->qr_code) }}&color=241F18&bgcolor=FAF6EF" 
                         alt="QR Code ID {{ $currentUser->name }}" 
                         class="w-44 h-44 rounded-xl object-contain shadow-sm" />
                @else
                    <div class="w-44 h-44 rounded-xl bg-sand/30 flex items-center justify-center text-xs text-terracotta font-bold">
                        QR Code Gagal Dimuat
                    </div>
                @endif
            </div>
            
            <!-- String UUID Token di bagian bawah -->
            <div class="text-center w-full bg-cream/50 py-2.5 px-4 rounded-xl border border-ink/5 font-mono text-[11px] font-extrabold text-forest select-all" title="Klik 3x untuk menyalin">
                {{ $currentUser->qr_code ?? 'Belum tergenerasi' }}
            </div>
        </div>
        <!-- Klik Area Luar untuk Menutup Modal -->
        <form method="dialog" class="modal-backdrop bg-ink/40 backdrop-blur-sm">
            <button>close</button>
        </form>
    </dialog>

</div>
@endsection