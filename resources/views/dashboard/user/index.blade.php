@extends('layouts.dashboard', ['title' => 'Dashboard — SulapaKarya'])

@section('dashboard-content')
@php
    $userId = session('user_id') ?? (auth()->check() ? auth()->id() : null);
    $currentUser = \App\Models\User::find($userId);
    $safePointsBalance = ($currentUser && $currentUser->points_balance > 0) ? $currentUser->points_balance : 0;

    // 1. Ambil setoran aktif yang masih dalam alur penjemputan armada
    $activeDeposits = \App\Models\WasteDeposit::where('user_id', $userId)
        ->whereIn('status', ['pending', 'menunggu_penjemput', 'penjemput_menuju_lokasi', 'penjemput_tiba'])
        ->orderBy('created_at', 'desc')
        ->get();

    // 2. Hitung total berat riil (Kg) dari sampah yang berhasil dituntaskan
    $totalWeightCollected = (float) \App\Models\WasteDeposit::where('user_id', $userId)
        ->whereIn('status', ['berhasil_dikirim', 'completed', 'selesai'])
        ->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(actual_weight, estimated_weight, 0)'));

    // 3. Ambil 4 produk kriya terbaru/unggulan untuk pratinjau katalog
    $previewProducts = \App\Models\Product::where('status', 'available')
        ->orderBy('is_featured', 'desc')
        ->orderBy('created_at', 'desc')
        ->take(4)
        ->get();
@endphp

<div class="space-y-6 text-left">
    
    <!-- ============ HEADER SAMBUTAN & STATUS ============ -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white border border-ink/5 p-5 sm:p-6 rounded-2xl shadow-none">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-forest text-white font-bold text-base flex items-center justify-center shrink-0 overflow-hidden ring-1 ring-forest/20 shadow-xs">
                @if($currentUser && $currentUser->foto_profil)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($currentUser->foto_profil) }}?v={{ time() }}" alt="Foto Profil" class="w-full h-full object-cover" />
                @else
                    {{ strtoupper(substr($currentUser->name ?? session('name', 'W'), 0, 1)) }}
                @endif
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">Halo, {{ $currentUser->name ?? session('name', 'Warga') }}</h1>
                <p class="text-xs text-ink-soft mt-0.5">Kelola setoran sampah kering dan kumpulkan Poin Kriya Anda di wilayah Makassar.</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 bg-cream/30 border border-ink/5 px-3.5 py-2 rounded-xl text-xs w-full sm:w-auto justify-between sm:justify-start">
            <span class="text-ink-soft font-medium">Antrean Jemput Aktif:</span>
            <span class="font-mono font-bold text-forest text-sm">{{ $activeDeposits->count() }} Setoran</span>
        </div>
    </div>

    <!-- ============ PERINGATAN ALAMAT BELUM LENGKAP ============ -->
    @if(empty($currentUser) || empty($currentUser->address) || empty($currentUser->kecamatan) || empty($currentUser->kelurahan))
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-xs">
            <div class="space-y-0.5">
                <p class="font-bold text-amber-900">Alamat Penjemputan Belum Lengkap</p>
                <p class="text-amber-800/80 leading-relaxed">Lengkapi kecamatan, kelurahan, dan patokan rumah Anda agar kurir armada dapat menuju lokasi penjemputan.</p>
            </div>
            <a href="/profile" class="btn btn-xs bg-amber-700 hover:bg-amber-800 text-white border-none rounded-lg font-semibold px-3 py-1.5 shadow-none shrink-0 w-full sm:w-auto">
                Lengkapi Alamat Profil &rarr;
            </a>
        </div>
    @endif

    <!-- ============ RINGKASAN METRIK AKTIVITAS ============ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Card 1: Total Sampah Ditimbang -->
        <div class="bg-white border border-ink/5 rounded-2xl p-5 shadow-none flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[10px] uppercase font-bold text-ink-soft tracking-wider block">Total Sampah Terkumpul</span>
                <p class="text-2xl sm:text-3xl font-bold font-mono text-ink">
                    {{ number_format($totalWeightCollected, 2, ',', '.') }} <span class="text-xs font-sans font-medium text-ink-soft">kg</span>
                </p>
                <span class="text-[10px] text-forest font-semibold block">Telah berhasil didaur ulang</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-forest/10 text-forest flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
            </div>
        </div>

        <!-- Card 2: Saldo Poin Reward Kriya -->
        <div class="bg-white border border-ink/5 rounded-2xl p-5 shadow-none flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-[10px] uppercase font-bold text-ink-soft tracking-wider block">Saldo Poin Kriya</span>
                <p class="text-2xl sm:text-3xl font-bold font-mono text-forest">
                    {{ number_format($safePointsBalance, 0, ',', '.') }} <span class="text-xs font-sans font-medium text-forest">Poin</span>
                </p>
                <a href="/katalog" class="text-[10px] text-maritime font-semibold hover:underline block">
                    Gunakan untuk belanja kriya &rarr;
                </a>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-forest/10 text-forest flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="8"/><path d="m9 12 2 2 4-4"/></svg>
            </div>
        </div>
    </div>

    <!-- ============ BANNER AKSI SETOR SAMPAH ============ -->
    <div class="bg-forest text-white p-5 sm:p-6 rounded-2xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-none">
        <div>
            <h2 class="font-bold text-base sm:text-lg tracking-tight">Punya sampah daur ulang yang sudah dipilah?</h2>
            <p class="text-xs text-white/80 mt-0.5 max-w-xl">Panggil armada penjemput SulapaKarya langsung ke rumah Anda dan dapatkan poin insentif otomatis.</p>
        </div>
        <a href="/setor-sampah" class="btn btn-sm bg-white hover:bg-cream text-forest border-none px-5 rounded-xl font-bold text-xs h-10 shadow-none shrink-0 w-full sm:w-auto">
            Setor Sampah Baru
        </a>
    </div>

    <!-- ============ QR CODE SETORAN AKTIF ============ -->
    @if($activeDeposits->count() > 0)
        <div class="bg-white border border-ink/5 rounded-2xl p-5 sm:p-6 shadow-none space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-ink/5">
                <div>
                    <h2 class="text-sm font-bold text-ink">Setoran Menunggu Penjemputan</h2>
                    <p class="text-[11px] text-ink-soft">Tunjukkan QR Code berikut kepada kurir saat tiba di rumah untuk verifikasi timbangan.</p>
                </div>
                <a href="/riwayat-setoran" class="text-[11px] font-semibold text-forest hover:underline">
                    Lihat Semua Riwayat &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                @foreach($activeDeposits as $dep)
                    <div class="border border-ink/10 rounded-2xl p-4 bg-cream/20 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="font-mono text-xs font-bold text-ink block">{{ $dep->deposit_code }}</span>
                                <span class="text-[10px] text-ink-soft capitalize block">{{ $dep->category }} &bull; Est. {{ number_format($dep->estimated_weight, 1) }} kg</span>
                                <span class="text-[10px] text-ink-soft font-mono block">{{ $dep->created_at->translatedFormat('d M Y') }}</span>
                            </div>

                            <div>
                                @if($dep->status === 'pending')
                                    <span class="inline-flex items-center whitespace-nowrap bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-2 py-0.5 rounded">Menunggu Admin</span>
                                @elseif($dep->status === 'menunggu_penjemput')
                                    <span class="inline-flex items-center whitespace-nowrap bg-maritime/10 text-maritime text-[10px] font-bold px-2 py-0.5 rounded">Menunggu Kurir</span>
                                @elseif($dep->status === 'penjemput_menuju_lokasi')
                                    <span class="inline-flex items-center whitespace-nowrap bg-blue-50 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded">Kurir Menuju Lokasi</span>
                                @elseif($dep->status === 'penjemput_tiba')
                                    <span class="inline-flex items-center whitespace-nowrap bg-purple-50 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded">Kurir Tiba</span>
                                @endif
                            </div>
                        </div>

                        <div class="bg-white p-3 rounded-xl border border-ink/5 flex flex-col items-center justify-center">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($dep->deposit_code) }}&color=241F18&bgcolor=FFFFFF"
                                 alt="QR {{ $dep->deposit_code }}"
                                 class="w-32 h-32 rounded-lg object-contain shadow-2xs" loading="lazy" />
                            <span class="text-[10px] text-ink-soft font-mono mt-2 font-bold">{{ $dep->deposit_code }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ============ PRATINJAU KATALOG KERAJINAN KRIYA ============ -->
    <div class="bg-white border border-ink/5 rounded-2xl p-5 sm:p-6 shadow-none space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-ink/5">
            <div>
                <h2 class="text-sm font-bold text-ink">Katalog Karya Daur Ulang</h2>
                <p class="text-[11px] text-ink-soft">Tukarkan poin reward Anda dengan produk hasil karya mitra artisan Makassar.</p>
            </div>
            <a href="/katalog" class="btn btn-xs bg-cream hover:bg-forest hover:text-white text-ink border border-ink/10 rounded-lg text-[10px] font-semibold px-2.5 shadow-none transition-colors">
                Buka Katalog Lengkap &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            @forelse($previewProducts as $item)
                <div class="border border-ink/5 rounded-2xl overflow-hidden bg-white hover:border-forest/30 transition-all flex flex-col">
                    <div class="aspect-square bg-cream/30 border-b border-ink/5 overflow-hidden relative">
                        @if(!empty($item->photo_path))
                            <img src="{{ asset('storage/' . $item->photo_path) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-ink-soft/40 font-mono text-[10px]">No Photo</div>
                        @endif

                        @if($item->is_featured)
                            <span class="absolute top-2 right-2 badge bg-amber-50 text-amber-800 border-none text-[9px] font-bold px-1.5 py-0.5 rounded">Unggulan</span>
                        @endif
                    </div>
                    <div class="p-3 text-left flex-1 flex flex-col justify-between space-y-1.5">
                        <div>
                            <span class="text-[10px] text-ink-soft block capitalize">{{ $item->product_category }}</span>
                            <span class="font-bold text-xs text-ink block truncate" title="{{ $item->name }}">{{ $item->name }}</span>
                        </div>
                        <div class="pt-1 border-t border-ink/5">
                            <span class="text-xs font-mono font-bold text-forest block">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 sm:col-span-4 py-8 text-center text-xs text-ink-soft/60">
                    Katalog karya kriya akan segera diperbarui.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection