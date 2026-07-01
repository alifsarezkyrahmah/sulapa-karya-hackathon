@extends('layouts.app', ['title' => 'SulapaKarya Macca — Dari Sampah Jadi Karya'])

@section('content')
<!-- ============ HERO ============ -->
<section id="beranda" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12 text-center">
  <span class="tag-stitch inline-block text-forest text-xs font-semibold section-eyebrow uppercase px-4 py-1.5 mb-6">
    Gerakan Daur Ulang Komunitas Makassar
  </span>

  <h1 class="font-display font-semibold text-4xl sm:text-5xl lg:text-6xl leading-[1.1] max-w-3xl mx-auto">
    Dari Sampah Jadi Karya,<br>
    Untuk <span class="text-forest">Makassar</span> Tercinta.
  </h1>

  <p class="mt-6 text-ink-soft text-base sm:text-lg max-w-xl mx-auto">
    Kami menghubungkan warga, pengepul, dan pengrajin lokal dalam satu siklus —
    sampah yang Anda setorkan hari ini, kembali sebagai karya kriya bernilai esok hari.
  </p>

  <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
    <a href="#cara-kerja" class="btn bg-forest hover:bg-forest-dark text-white border-none rounded-full px-7">
      Mulai Setor Sampah
    </a>
    <a href="#cara-kerja" class="btn btn-outline border-ink/30 text-ink hover:bg-ink hover:text-cream hover:border-ink rounded-full px-7">
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
        <span class="text-sm font-semibold text-ink">Sampah Terpilah</span>
      </div>

      <svg class="flow-arrow text-forest shrink-0 rotate-90 sm:rotate-0" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>

      <div class="flex flex-col items-center gap-3">
        <div class="w-20 h-20 rounded-2xl bg-white grid place-items-center shadow-sm text-forest">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0-1.4 0L4 15.6V20h4.4l9.3-9.3a1 1 0 0 0 0-1.4l-3-3Z"/><path d="m17.5 9.5 1.5-1.5"/></svg>
        </div>
        <span class="text-sm font-semibold text-ink">Diolah Pengrajin</span>
      </div>

      <svg class="flow-arrow text-forest shrink-0 rotate-90 sm:rotate-0" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>

      <div class="flex flex-col items-center gap-3">
        <div class="w-20 h-20 rounded-2xl bg-white grid place-items-center shadow-sm text-terracotta">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5 12 4l9 5.5M4 10v9a1 1 0 0 0 1 1h5v-6h4v6h5a1 1 0 0 0 1-1v-9"/></svg>
        </div>
        <span class="text-sm font-semibold text-ink">Karya Siap Pakai</span>
      </div>

    </div>
  </div>
</section>


<!-- ============ TENTANG KAMI ============ -->
<section id="tentang" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
  <div class="grid md:grid-cols-2 gap-12 items-center">
    
    <!-- SISI KIRI: DESKRIPSI TEKS -->
    <div>
      <span class="text-forest text-xs font-bold section-eyebrow uppercase tracking-widest">Tentang Kami</span>
      <h2 class="font-display font-semibold text-3xl sm:text-4xl mt-3 mb-5 leading-tight">
        Every kilogram of waste has a second story.
      </h2>
      <p class="text-ink-soft leading-relaxed text-sm sm:text-base">
        Platform ini hadir untuk mempertemukan sisi sampah Anda dengan tangan-tangan kreatif
        para pengrajin di Makassar. Sampah anorganik yang Anda setorkan tidak hanya bersih
        dari lingkungan, tapi kembali menjadi produk kriya yang indah dan bermakna —
        sembari menggerakkan roda ekonomi komunitas kami.
      </p>
      <div class="mt-6 flex items-center gap-3 text-xs font-bold">
        <span class="tag-stitch px-3 py-1 text-forest border-forest/40">Tanpa Perantara</span>
        <span class="tag-stitch px-3 py-1 text-maritime border-maritime/40">Terverifikasi</span>
      </div>
    </div>

    <!-- SISI KANAN: DUA KARTU VISUAL (GRID) -->
    <div class="grid grid-cols-2 gap-4">
      
      <!-- KARTU 1: VISUAL PROSES (Menggunakan img1.png dengan Gradient Overlay) -->
      <div class="aspect-[3/4] rounded-[1.5rem] overflow-hidden relative border border-ink/5 group shadow-sm">
        <img src="{{ asset('images/img1.png') }}" alt="Visual Proses Kriya" 
          class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105" />
        
        <!-- Lapisan Hitam Transparan bawah agar Teks Putih Kontras & Terbaca -->
        <div class="absolute inset-0 bg-gradient-to-t from-ink/90 via-ink/30 to-transparent flex flex-col justify-end p-5 text-left">
          <span class="text-xs font-bold text-cream tracking-wide">Visual Proses Kami</span>
          <span class="text-[10px] text-cream/70 mt-1 leading-tight font-medium">Dari pemilahan hingga produk kriya jadi</span>
        </div>
      </div>
      

      <!-- KARTU 2: FILOSOFI (Menggunakan bg-forest & Tampilan LOGO FULL) -->
      <div class="aspect-[3/4] rounded-[1.5rem] bg-sand-40 text-white flex flex-col items-center justify-center p-6 text-center relative overflow-hidden shadow-md shadow-forest/5">
        <!-- Ornamen Pola Dot Kriya Halus khas SulapaKarya -->
        <div class="absolute inset-0 dot-grid text-white/[0.03] pointer-events-none"></div>
    
        <!-- Logo Aplikasi Resmi Tampil Full & Proporsional -->
        <div class="relative bg-sand-40 z-10 mb-6 w-full flex justify-center transition-transform duration-300 hover:scale-105">
          <img src="{{ asset('images/logo.png') }}" alt="Logo SulapaKarya Macca Full" 
            class="w-30 h-30 sm:w-40 sm:h-40 object-contain drop-shadow-[0_4px_12px_rgba(0,0,0,0.12)]" />
        </div>
                <div class="absolute inset-0 bg-gradient-to-t from-ink/90 via-ink/30 to-transparent flex flex-col justify-end p-5 text-left">
        </div>
        
        <!-- Kalimat Kutipan Filosofi -->
        <p class="font-display font-semibold text-base sm:text-lg leading-snug text-cream relative z-10 px-1">
          “Setiap sampah punya kisah kedua.”
        </p>
        <span class="text-[9px] font-extrabold mt-4 text-sand uppercase tracking-widest relative z-10 bg-white/5 px-2.5 py-1 rounded-md border border-white/5">
          Filosofi SulapaKarya
        </span>
      </div>

    </div>
  </div>
</section>

<!-- ============ IMPACT TRACKER ============ -->
<section class="bg-ink text-cream py-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <span class="text-forest text-xs font-bold section-eyebrow uppercase">Impact Tracker</span>
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
<section id="cara-kerja" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
  <div class="text-center max-w-2xl mx-auto mb-12">
    <span class="text-forest text-xs font-bold section-eyebrow uppercase">Cara Kerja</span>
    <h2 class="font-display font-semibold text-3xl sm:text-4xl mt-3 mb-4">Layanan Nyata Kita</h2>
    <p class="text-ink-soft">Lifecycle sederhana untuk pengguna: rapi, mudah, dan transparan.</p>
    <a href="#" class="btn bg-forest hover:bg-forest-dark text-white border-none rounded-full px-7 mt-7">
      Mulai Setor Sampah
    </a>
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
        <div class="flex items-center gap-4 mt-5 pt-4 border-t border-ink/10 text-ink-soft">
          <button aria-label="Bagikan" class="hover:text-maritime transition-colors">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/></svg>
          </button>
          <button aria-label="Simpan" class="hover:text-terracotta transition-colors">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21 12 16l-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
          </button>
          <button aria-label="Info selengkapnya" class="hover:text-forest transition-colors">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 16v-4M12 8h.01"/></svg>
          </button>
        </div>
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
        <div class="flex items-center gap-4 mt-5 pt-4 border-t border-ink/10 text-ink-soft">
          <button aria-label="Bagikan" class="hover:text-maritime transition-colors">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/></svg>
          </button>
          <button aria-label="Simpan" class="hover:text-terracotta transition-colors">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21 12 16l-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
          </button>
          <button aria-label="Info selengkapnya" class="hover:text-forest transition-colors">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 16v-4M12 8h.01"/></svg>
          </button>
        </div>
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
        <div class="flex items-center gap-4 mt-5 pt-4 border-t border-ink/10 text-ink-soft">
          <button aria-label="Bagikan" class="hover:text-maritime transition-colors">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/></svg>
          </button>
          <button aria-label="Simpan" class="hover:text-terracotta transition-colors">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21 12 16l-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
          </button>
          <button aria-label="Info selengkapnya" class="hover:text-forest transition-colors">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 16v-4M12 8h.01"/></svg>
          </button>
        </div>
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
        <div class="flex items-center gap-4 mt-5 pt-4 border-t border-ink/10 text-ink-soft">
          <button aria-label="Bagikan" class="hover:text-maritime transition-colors">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/></svg>
          </button>
          <button aria-label="Simpan" class="hover:text-terracotta transition-colors">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21 12 16l-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
          </button>
          <button aria-label="Info selengkapnya" class="hover:text-forest transition-colors">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 16v-4M12 8h.01"/></svg>
          </button>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ============ PRODUK UNGGULAN ============ -->
<section id="produk" class="bg-sand/60 py-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto mb-12">
      <span class="text-terracotta text-xs font-bold section-eyebrow uppercase">Marketplace</span>
      <h2 class="font-display font-semibold text-3xl sm:text-4xl mt-3 mb-7">Produk Unggulan</h2>
      <a href="#" class="btn btn-outline border-terracotta text-terracotta hover:bg-terracotta hover:text-white rounded-full px-7">
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
<section class="py-14">
  <p class="text-center font-display italic text-lg sm:text-xl text-ink-soft max-w-2xl mx-auto px-6">
    “Ubah sampah jadi karya, berdayakan pengrajin lokal bersama SulapaKarya Macca.”
  </p>
</section>
@endsection
