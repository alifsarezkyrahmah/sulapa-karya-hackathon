@extends('layouts.app', ['title' => 'SulapaKarya — Dari Sampah Jadi Karya'])

@section('content')
<!-- ============ HERO ============ -->
<section id="beranda" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-24 text-center">
  <span class="tag-stitch inline-block text-forest text-xs font-semibold section-eyebrow uppercase px-4 py-1.5 mb-6">
    Gerakan Daur Ulang Komunitas Makassar
  </span>

  <h1 class="font-display font-semibold text-3xl sm:text-4xl lg:text-5xl leading-[1.1] max-w-3xl mx-auto">
    Tukar Sampahmu Jadi Uang,<br>
    Untuk <span class="text-forest">Makassar</span> Tercinta.
  </h1>

  <p class="mt-6 text-ink-soft text-base sm:text-lg max-w-xl mx-auto">
    Jangan biarkan sampah anorganik rumahmu terbuang sia-sia. Kumpulkan, setor ke kami, dan langsung dapatkan uangnya!
  </p>

  <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
    <a href="#cara-kerja" class="btn bg-forest hover:bg-forest-dark text-white border-none rounded-full px-10">
      Lihat Cara Kerja
    </a>
  </div>

  <!-- Hero visual: waste → craft transformation banner -->
  <div class="mt-14 relative rounded-[2rem] bg-gradient-to-br from-forest-light via-sand to-forest-light border border-ink/10 overflow-hidden">
    <div class="dot-grid absolute inset-0 text-forest/10"></div>
    <div class="relative px-6 sm:px-12 py-12 sm:py-16 flex flex-col sm:flex-row items-center justify-center gap-6 sm:gap-10">

      <div class="flex flex-col items-center gap-3">
        <div class="w-20 h-20 rounded-2xl bg-white grid place-items-center shadow-sm text-ink-soft">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m2 0-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6h14ZM10 11v6M14 11v6"/></svg>
        </div>
        <span class="text-sm font-semibold text-ink">Pilah Sampahmu</span>
      </div>

      <svg class="flow-arrow text-forest shrink-0 rotate-90 sm:rotate-0" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>

      <div class="flex flex-col items-center gap-3">
        <div class="w-20 h-20 rounded-2xl bg-white grid place-items-center shadow-sm text-forest">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0-1.4 0L4 15.6V20h4.4l9.3-9.3a1 1 0 0 0 0-1.4l-3-3Z"/><path d="m17.5 9.5 1.5-1.5"/></svg>
        </div>
        <span class="text-sm font-semibold text-ink">Sampah Diambil</span>
      </div>

      <svg class="flow-arrow text-forest shrink-0 rotate-90 sm:rotate-0" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>

      <div class="flex flex-col items-center gap-3">
        <div class="w-20 h-20 rounded-2xl bg-white grid place-items-center shadow-sm text-terracotta">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5 12 4l9 5.5M4 10v9a1 1 0 0 0 1 1h5v-6h4v6h5a1 1 0 0 0 1-1v-9"/></svg>
        </div>
        <span class="text-sm font-semibold text-ink">Terima Uang</span>
      </div>

    </div>
  </div>
</section>

<!-- ============ SEKSI JENIS SAMPAH YANG DITERIMA ============ -->
<section id="jenis-sampah" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
  <div class="text-center max-w-2xl mx-auto mb-12">
    <h2 class="font-display font-semibold text-3xl sm:text-4xl text-ink">
      Jenis Sampah yang Bisa Ditukar
    </h2>
    <p class="mt-3 text-ink-soft text-sm sm:text-base">
      Berikut jenis sampah yang bisa kamu tukarkan di <span class="text-forest">SulapaKarya</span>
    </p>
  </div>

  <div class="grid md:grid-cols-3 gap-6 sm:gap-8">
    
    <!-- 1. PLASTIK -->
    <div class="card bg-white border border-ink/10 rounded-3xl p-6 sm:p-8 hover:shadow-xl hover:-translate-y-1 transition-all flex flex-col justify-between">
      <div>
        <div class="w-14 h-14 rounded-2xl bg-forest-light text-forest grid place-items-center mb-6">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10 2h4M10 5h4M9 5v3l-2 3v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V11l-2-3V5H9z"/>
          </svg>
        </div>
        <h3 class="font-display font-semibold text-2xl text-ink mb-2">Sampah Plastik</h3>
        <p class="text-ink-soft text-xs leading-relaxed mb-6">
          Bahan plastik anorganik bersih dari sisa makanan atau cairan.
        </p>
        
        <div class="space-y-2 border-t border-ink/5 pt-4">
          <span class="text-[11px] font-bold text-forest uppercase tracking-wider block mb-2">Contoh Barang:</span>
          <ul class="text-xs text-ink-soft space-y-1.5 list-disc list-inside">
            <li>Botol & gelas plastik minuman</li>
            <li>Wadah deterjen, shampoo & jeriken</li>
            <li>Ember, baskom & perabotan plastik</li>
            <li>Tutup botol & galon bekas</li>
          </ul>
        </div>
      </div>
      
      <div class="mt-6 pt-4 border-t border-ink/5">
        <span class="tag-stitch w-full text-center block text-forest text-xs font-semibold px-3 py-1.5">
          ✓ Diterima dalam Kondisi Kering
        </span>
      </div>
    </div>

    <!-- 2. KERTAS -->
    <div class="card bg-white border border-ink/10 rounded-3xl p-6 sm:p-8 hover:shadow-xl hover:-translate-y-1 transition-all flex flex-col justify-between">
      <div>
        <div class="w-14 h-14 rounded-2xl bg-sand/80 text-terracotta grid place-items-center mb-6">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
          </svg>
        </div>
        <h3 class="font-display font-semibold text-2xl text-ink mb-2">Sampah Kertas</h3>
        <p class="text-ink-soft text-xs leading-relaxed mb-6">
          Segala jenis olahan serat kertas dan karton yang tidak basah/berminyak.
        </p>
        
        <div class="space-y-2 border-t border-ink/5 pt-4">
          <span class="text-[11px] font-bold text-terracotta uppercase tracking-wider block mb-2">Contoh Barang:</span>
          <ul class="text-xs text-ink-soft space-y-1.5 list-disc list-inside">
            <li>Kardus & box kemasan bekas</li>
            <li>Kertas HVS kantor / sekolah</li>
            <li>Koran, majalah & brosur</li>
            <li>Buku bekas & kemasan paperboard</li>
          </ul>
        </div>
      </div>

      <div class="mt-6 pt-4 border-t border-ink/5">
        <span class="tag-stitch w-full text-center block text-terracotta border-terracotta/40 text-xs font-semibold px-3 py-1.5">
          ✓ Dilipat & Ditalikan Lebih Baik
        </span>
      </div>
    </div>

    <!-- 3. KAIN -->
    <div class="card bg-white border border-ink/10 rounded-3xl p-6 sm:p-8 hover:shadow-xl hover:-translate-y-1 transition-all flex flex-col justify-between">
      <div>
        <div class="w-14 h-14 rounded-2xl bg-maritime/10 text-maritime grid place-items-center mb-6">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/>
          </svg>
        </div>
        <h3 class="font-display font-semibold text-2xl text-ink mb-2">Sampah Kain</h3>
        <p class="text-ink-soft text-xs leading-relaxed mb-6">
          Limbah tekstil rumah tangga dan konveksi yang layak daur ulang.
        </p>

        <div class="space-y-2 border-t border-ink/5 pt-4">
          <span class="text-[11px] font-bold text-maritime uppercase tracking-wider block mb-2">Contoh Barang:</span>
          <ul class="text-xs text-ink-soft space-y-1.5 list-disc list-inside">
            <li>Pakaian bekas tak terpakai</li>
            <li>Kain perca sisa jaitan/konveksi</li>
            <li>Sprei, sarung bantal & gorden bekas</li>
            <li>Tas kain & bahan tekstil sintetis</li>
          </ul>
        </div>
      </div>

      <div class="mt-6 pt-4 border-t border-ink/5">
        <span class="tag-stitch w-full text-center block text-maritime border-maritime/40 text-xs font-semibold px-3 py-1.5">
          ✓ Bersih & Tidak Lembap
        </span>
      </div>
    </div>

  </div>
</section>

<!-- ============ IMPACT TRACKER ============ -->
<section class="bg-ink text-cream py-12 sm:py-16 lg:py-24">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <h2 class="font-display font-semibold text-3xl sm:text-4xl mt-3 mb-12">
      Live counters dampak lingkungan &amp; pemberdayaan pengrajin
    </h2>

    <div class="stats stats-vertical sm:stats-horizontal bg-transparent shadow-none w-full divide-cream/15">
      <div class="stat">
        <div class="stat-value font-mono text-white text-4xl">1.450 <span class="text-xl">Kg</span></div>
        <div class="stat-desc text-cream/60 mt-2 uppercase tracking-wide text-xs">Total Sampah Diselamatkan</div>
      </div>
      <div class="stat">
        <div class="stat-value font-mono text-white text-4xl">420 <span class="text-xl">Pcs</span></div>
        <div class="stat-desc text-cream/60 mt-2 uppercase tracking-wide text-xs">Produk Karya Terjual</div>
      </div>
      <div class="stat">
        <div class="stat-value font-mono text-white text-4xl">15 <span class="text-xl">Orang</span></div>
        <div class="stat-desc text-cream/60 mt-2 uppercase tracking-wide text-xs">Pengrajin Lokal Diberdayakan</div>
      </div>
    </div>
  </div>
</section>

<!-- ============ LAYANAN NYATA KITA ============ -->
<section id="cara-kerja" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 lg:py-24">
  <div class="text-center max-w-2xl mx-auto mb-12">
    <h2 class="font-display font-semibold text-3xl sm:text-4xl mt-3 mb-4">Layanan Nyata Kita</h2>
    <p class="text-ink-soft">Lifecycle sederhana untuk pengguna: rapi, mudah, dan transparan.</p>
    @if (!session('user_id')) <!-- jika pengguna belum login diarahkan kehalaman login -->
      <a href="/login" class="btn bg-forest hover:bg-forest-dark text-white border-none rounded-full px-7 mt-7">
        Mulai Setor Sampah
      </a>
    @else <!-- jika pengguna sudah login, maka diarahkan ke halaman setor sampah -->
      <a href="/setor-sampah" class="btn bg-forest hover:bg-forest-dark text-white border-none rounded-full px-7 mt-7">
        Mulai Setor Sampah
      </a>
    @endif
  </div>

  <div class="grid sm:grid-cols-2 gap-6">
    <div class="card bg-white border border-ink/10 rounded-2xl hover:shadow-lg hover:-translate-y-0.5 transition-all">
      <div class="card-body p-6">
        <div class="flex items-center gap-3 mb-1">
          <span class="w-9 h-9 rounded-full bg-forest-light text-forest font-mono text-sm font-semibold grid place-items-center">01</span>
          <h3 class="font-display font-semibold text-lg">Setor Sampah</h3>
        </div>
        <p class="text-ink-soft text-sm leading-relaxed">Pilih jenis sampah yang ingin disetor, lalu jadwalkan waktu jemput sesuai zona Anda.</p>
        <span class="tag-stitch inline-block w-fit text-forest text-xs font-medium px-3 py-1 mt-3">Mudah & cepat</span>
      </div>
    </div>

    <div class="card bg-white border border-ink/10 rounded-2xl hover:shadow-lg hover:-translate-y-0.5 transition-all">
      <div class="card-body p-6">
        <div class="flex items-center gap-3 mb-1">
          <span class="w-9 h-9 rounded-full bg-forest-light text-forest font-mono text-sm font-semibold grid place-items-center">02</span>
          <h3 class="font-display font-semibold text-lg">Verifikasi & Jemput</h3>
        </div>
        <p class="text-ink-soft text-sm leading-relaxed">Tim kami memverifikasi berat serta kategori dan menjemput sampah secara langsung.</p>
        <span class="tag-stitch inline-block w-fit text-forest text-xs font-medium px-3 py-1 mt-3">Tim terpercaya</span>
      </div>
    </div>

    <div class="card bg-white border border-ink/10 rounded-2xl hover:shadow-lg hover:-translate-y-0.5 transition-all">
      <div class="card-body p-6">
        <div class="flex items-center gap-3 mb-1">
          <span class="w-9 h-9 rounded-full bg-forest-light text-forest font-mono text-sm font-semibold grid place-items-center">03</span>
          <h3 class="font-display font-semibold text-lg">Kreasi Pengrajin</h3>
        </div>
        <p class="text-ink-soft text-sm leading-relaxed">Pengrajin lokal mengolah bahan terpilih menjadi karya kriya bernilai jual tinggi.</p>
        <span class="tag-stitch inline-block w-fit text-forest text-xs font-medium px-3 py-1 mt-3">Tangan terampil</span>
      </div>
    </div>

    <div class="card bg-white border border-ink/10 rounded-2xl hover:shadow-lg hover:-translate-y-0.5 transition-all">
      <div class="card-body p-6">
        <div class="flex items-center gap-3 mb-1">
          <span class="w-9 h-9 rounded-full bg-forest-light text-forest font-mono text-sm font-semibold grid place-items-center">04</span>
          <h3 class="font-display font-semibold text-lg">Marketplace Ramah Lingkungan</h3>
        </div>
        <p class="text-ink-soft text-sm leading-relaxed">Karya dipasarkan sebagai produk berkelanjutan, langsung ke tangan pembeli.</p>
        <span class="tag-stitch inline-block w-fit text-forest text-xs font-medium px-3 py-1 mt-3">Karya ramah lingkungan</span>
      </div>
    </div>
  </div>
</section>

<!-- ============ PRODUK UNGGULAN ============ -->
<section id="produk" class="bg-sand/60 py-12 sm:py-16 lg:py-24">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto mb-12">
      <h2 class="font-display font-semibold text-3xl sm:text-4xl mt-3 mb-7">Produk Unggulan</h2>
      <a href="/katalog" class="btn btn-outline border-terracotta text-terracotta hover:bg-terracotta hover:text-white rounded-full px-7">
        Lihat Katalog
      </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
      <div class="card bg-white rounded-2xl border border-ink/10 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="aspect-square bg-forest-light/60 grid place-items-center text-forest/50">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
        </div>
        <div class="card-body p-4">
          <h3 class="text-sm font-semibold leading-snug">Tas Rajut Plastik Daur Ulang</h3>
          <span class="tag-stitch inline-block w-fit text-terracotta border-terracotta/50 font-mono text-xs font-semibold px-3 py-1 mt-2">Rp 95.000</span>
        </div>
      </div>
      <div class="card bg-white rounded-2xl border border-ink/10 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="aspect-square bg-forest-light/60 grid place-items-center text-forest/50">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
        </div>
        <div class="card-body p-4">
          <h3 class="text-sm font-semibold leading-snug">Vas Bunga Botol Kaca</h3>
          <span class="tag-stitch inline-block w-fit text-terracotta border-terracotta/50 font-mono text-xs font-semibold px-3 py-1 mt-2">Rp 65.000</span>
        </div>
      </div>
      <div class="card bg-white rounded-2xl border border-ink/10 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="aspect-square bg-forest-light/60 grid place-items-center text-forest/50">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
        </div>
        <div class="card-body p-4">
          <h3 class="text-sm font-semibold leading-snug">Dompet Kulit Sintetis Bekas</h3>
          <span class="tag-stitch inline-block w-fit text-terracotta border-terracotta/50 font-mono text-xs font-semibold px-3 py-1 mt-2">Rp 75.000</span>
        </div>
      </div>
      <div class="card bg-white rounded-2xl border border-ink/10 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="aspect-square bg-forest-light/60 grid place-items-center text-forest/50">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
        </div>
        <div class="card-body p-4">
          <h3 class="text-sm font-semibold leading-snug">Lampu Hias Kaleng Bekas</h3>
          <span class="tag-stitch inline-block w-fit text-terracotta border-terracotta/50 font-mono text-xs font-semibold px-3 py-1 mt-2">Rp 110.000</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ CLOSING TAGLINE ============ -->
<section class="py-12 sm:py-16">
  <p class="text-center font-display italic text-lg sm:text-xl text-ink-soft max-w-2xl mx-auto px-6">
    “Ubah sampah jadi karya, berdayakan pengrajin lokal bersama SulapaKarya.”
  </p>
</section>
@endsection