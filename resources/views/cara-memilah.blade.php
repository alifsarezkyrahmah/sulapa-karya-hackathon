@extends('layouts.app', ['title' => 'Panduan & Standar QC Sampah — SulapaKarya'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 space-y-12">

    <!-- Header Edukasi -->
    <div class="text-center max-w-3xl mx-auto space-y-4">
        <span class="badge bg-forest/10 border-none text-forest text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
            Standar Kelayakan Setor Sampah
        </span>
        <h1 class="text-3xl sm:text-4xl font-black text-ink tracking-tight font-display">
            Panduan Pemilahan & Standar Quality Control (QC)
        </h1>
        <p class="text-ink-soft text-sm sm:text-base leading-relaxed">
            Pastikan sampah anorganik yang kamu setorkan telah memenuhi syarat standar kelayakan agar poin konversi (40%) dapat langsung dicairkan oleh kurir dan admin ke akunmu.
        </p>
    </div>

    <!-- 3 Langkah Wajib Lolos QC (Single QC Standard) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-3xl p-6 border border-ink/5 shadow-sm space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-maritime/10 text-maritime flex items-center justify-center text-xl font-bold font-mono">1</div>
            <h3 class="font-bold text-ink text-base">Bersihkan (Clean)</h3>
            <p class="text-xs text-ink-soft leading-relaxed">Bilas sisa minuman, makanan, atau minyak dari botol, gelas, dan kaleng dengan air bersih secukupnya.</p>
        </div>
        <div class="bg-white rounded-3xl p-6 border border-ink/5 shadow-sm space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-forest/10 text-forest flex items-center justify-center text-xl font-bold font-mono">2</div>
            <h3 class="font-bold text-ink text-base">Keringkan (Dry)</h3>
            <p class="text-xs text-ink-soft leading-relaxed">Jemur atau angin-anginkan hingga kering total. Sampah kertas, kardus, atau kain tidak boleh dalam keadaan basah/lembab.</p>
        </div>
        <div class="bg-white rounded-3xl p-6 border border-ink/5 shadow-sm space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-terracotta/10 text-terracotta flex items-center justify-center text-xl font-bold font-mono">3</div>
            <h3 class="font-bold text-ink text-base">Kemas & Rapikan (Compact)</h3>
            <p class="text-xs text-ink-soft leading-relaxed">Pipihkan botol/gelas, ikat kardus dan koran secara rapi, pisahkan sesuai kategori agar mudah ditimbang kurir.</p>
        </div>
    </div>

    <!-- Daftar Detail Jenis Sampah & Syarat QC Spesifik -->
    <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-ink/10 pb-4">
            <h2 class="text-2xl font-bold text-ink font-display">Daftar Jenis Sampah & Kriteria QC</h2>
            <span class="text-xs font-semibold text-ink-soft font-mono">{{ $wastePrices->count() }} Jenis Terdaftar</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($wastePrices as $wp)
            <div class="bg-white rounded-3xl p-6 border border-ink/5 shadow-sm flex flex-col justify-between space-y-4 hover:border-forest/30 transition-all">
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="font-bold text-ink text-base leading-tight">{{ $wp->name }}</h3>
                        <span class="badge bg-forest/10 text-forest font-mono font-bold text-xs shrink-0 py-2">
                            {{ number_format($wp->point_per_kg, 0, ',', '.') }} Poin/kg
                        </span>
                    </div>
                    <p class="text-xs font-mono font-semibold text-ink-soft mt-1">Tarif: Rp {{ number_format($wp->price_per_kg, 0, ',', '.') }}/{{ $wp->unit }}</p>

                    <!-- Syarat QC Khusus -->
                    <div class="mt-4 p-3 rounded-2xl bg-cream/30 border border-ink/5 space-y-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-forest block">✓ Syarat Lolos Verifikasi QC:</span>
                        <ul class="text-xs text-ink space-y-1">
                            <li class="flex items-center gap-1.5">
                                <span class="text-forest font-bold">✔</span> Bebas dari cairan / sisa minyak
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="text-forest font-bold">✔</span> Bersih, kering, tidak berjamur
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="text-forest font-bold">✔</span> Terpilah rapi saat diserahkan
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="pt-2 border-t border-ink/5 flex items-center justify-between">
                    <span class="text-[11px] text-ink-soft">Standar Konversi Poin</span>
                    <span class="text-xs font-bold text-maritime">40% Nilai Barang</span>
                </div>
            </div>
            @empty
            <div class="col-span-3 py-12 text-center text-ink-soft/60">
                Belum ada data panduan sampah.
            </div>
            @endforelse
        </div>
    </div>

    <!-- CTA Setor Sampah -->
    <div class="bg-forest rounded-3xl p-8 sm:p-12 text-white text-center space-y-6 shadow-xl">
        <h2 class="text-2xl sm:text-3xl font-black font-display">Sudah Memilah Sampah Sesuai Standar QC?</h2>
        <p class="text-white/80 text-xs sm:text-sm max-w-xl mx-auto">
            Ajukan penjemputan sekarang. Kurir kami akan datang menimbang sampahmu langsung di lokasi penjemputan.
        </p>
        <div>
            @if(session()->has('user_id'))
                <a href="/setor-sampah" class="btn bg-cream hover:bg-white text-forest font-extrabold rounded-full px-8 py-3 border-none shadow-md">
                    Setor Sampah Sekarang 🚀
                </a>
            @else
                <a href="{{ route('login') }}" class="btn bg-cream hover:bg-white text-forest font-extrabold rounded-full px-8 py-3 border-none shadow-md">
                    Masuk & Setor Sampah 🚀
                </a>
            @endif
        </div>
    </div>

</div>
@endsection