@extends('layouts.app', ['title' => 'SulapaKarya — Dari Sampah Jadi Karya'])

@section('content')

<style>
  @keyframes slideInRight {
    0% {
      opacity: 0;
      transform: translateX(100px) scale(1.1);
    }
    100% {
      opacity: 1;
      transform: translateX(0) scale(1.1);
    }
  }

  .animate-slide-in {
    animation: slideInRight 1s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
  }

</style>

<!-- Hero Section -->
<section class="w-full px-4 sm:px-6 lg:px-8 pt-4 pb-12 overflow-hidden">
  <div class="max-w-7xl mx-auto px-6 sm:px-10 lg:px-12 py-10 lg:py-16 relative">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
      
      <div class="lg:col-span-6 flex flex-col items-start space-y-6 z-10 text-left">
        <!-- Judul Utama (H1) -->
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-forest leading-[1.2] tracking-tight">
          Ubah Sampah di Tanganmu Jadi Nilai Berharga
        </h1>

        <!-- Subjudul (P) -->
        <p class="text-base sm:text-lg text-ink-soft leading-relaxed max-w-xl">
          Dari satu sampah ditanganmu, .
        </p>

        <div class="pt-2 flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
          @if(session()->has('user_id'))
            <a href="/setor-sampah" class="btn bg-forest hover:bg-forest/90 text-white font-bold rounded-full px-8 py-3 text-sm normal-case border-none shadow-md shadow-forest/20 transition-all duration-200 hover:scale-105 active:scale-95">
              Mulai Setor Sekarang!
            </a>
          @else
            <a href="{{ route('login') }}" class="btn bg-forest hover:bg-forest/90 text-white font-bold rounded-full px-8 py-3 text-sm normal-case border-none shadow-md shadow-forest/20 transition-all duration-200 hover:scale-105 active:scale-95">
              Mulai Setor Sekarang!
            </a>
          @endif
          <a class="btn bg-cream/10 hover:bg-cream/20 text-forest border-forest font-bold rounded-full px-8 py-3 text-sm normal-case border border-cream/20 shadow-md shadow-forest/10 transition-all duration-200 hover:scale-105 active:scale-95 ml-4" href="#kalkulator">
            Hitung Nilai Sampahmu
          </a>
        </div>
      </div>

      <div class="lg:col-span-6 flex justify-end items-center relative lg:-mr-12">
        <img
          id="hero-bottle-image"
          src="{{ asset('images/tangan-botol.png') }}" 
          alt="Tangan memegang botol plastik" 
          class="animate-slide-in w-full max-w-[500px] lg:max-w-[650px] xl:max-w-[720px] h-auto object-contain drop-shadow-2xl hover:scale-105 transition-transform duration-300 pointer-events-none scale-110 translate-x-4 lg:translate-x-8"
        >
      </div>

    </div>
  </div>
</section>

<div id="tentang-kami">
  <!-- ============ Tentang Kami ============ -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
      
      <!-- GAMBAR (SEBELAH KIRI) -->
      <div class="lg:col-span-6 flex justify-start items-center relative order-2 lg:order-1">
        <img src="{{ asset('images/tempat-sampah.png') }}" alt="Tentang SulapaKarya" class="w-full max-w-[440px] lg:max-w-[480px] h-auto object-contain drop-shadow-lg hover:scale-105 transition-transform duration-300 pointer-events-none">
      </div>

      <!-- TEKS (SEBELAH KANAN) -->
      <div class="lg:col-span-6 flex flex-col items-start space-y-6 order-1 lg:order-2">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-forest tracking-tight">
          Tentang SulapaKarya
        </h2>
        <p class="text-sm sm:text-base text-ink-soft leading-relaxed">
          Sampah anorganik yang menumpuk setiap hari, padahal masih punya nilai jual. SulapaKarya hadir untuk menjembatani masyarakat yang ingin berkontribusi menjaga lingkungan dalam nilai sambil mendapatkan penghasilan tambahan.      </p>
        <p class="text-sm sm:text-base text-ink-soft leading-relaxed">
          SulapaKarya membantu masyarakat dalam gerakan daur ulang yang berkelanjutan di Kota Makassar sehingga mewujudkan Makassar Kota Bebas Sampah.
      </div>
    </div>
  </section>

  <!-- ============ CARA KERJA / PROSES ============ -->
  <section id="cara-kerja" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
    
    <div class="text-center mb-16">
      <h2 class="text-3xl sm:text-4xl font-extrabold text-forest tracking-tight">Bagaimana SulapaKarya Bekerja</h2>
      <p class="text-xs sm:text-sm text-ink-soft mt-2">Dari sampah di rumahmu, hingga menjadi karya bernilai tinggi.</p>
    </div>

    <div class="relative">
      
      <div class="absolute left-1/2 top-5 bottom-5 w-0.5 bg-forest/20 -translate-x-1/2 z-0 hidden sm:block"></div>

      <div class="space-y-4 sm:space-y-5 relative z-10">

        <!-- STEP 1 (KIRI) -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-0">
          <div class="w-full sm:w-[42%] text-center sm:text-right order-2 sm:order-1">
            <h3 class="text-sm font-bold text-ink">Pilah Sampah</h3>
            <p class="text-xs text-ink-soft mt-1 leading-relaxed">Pisahkan sampahmu sesuai jenisnya di rumah.</p>
          </div>
          <div class="w-8 h-8 rounded-full bg-forest text-white flex items-center justify-center font-bold text-xs shadow-md shrink-0 order-1 sm:order-2">
            1
          </div>
          <div class="w-full sm:w-[42%] hidden sm:block order-3"></div>
        </div>

        <!-- STEP 2 (KANAN) -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-0">
          <div class="w-full sm:w-[42%] hidden sm:block order-3 sm:order-1"></div>
          <div class="w-8 h-8 rounded-full bg-forest text-white flex items-center justify-center font-bold text-xs shadow-md shrink-0 order-1 sm:order-2">
            2
          </div>
          <div class="w-full sm:w-[42%] text-center sm:text-left order-2 sm:order-3">
            <h3 class="text-sm font-bold text-ink">Setor ke Kami</h3>
            <p class="text-xs text-ink-soft mt-1 leading-relaxed">Antar langsung ke titik kumpul terdekat atau jadwalkan penjemputan oleh kurir kami.</p>
          </div>
        </div>

        <!-- STEP 3 (KIRI) -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-0">
          <div class="w-full sm:w-[42%] text-center sm:text-right order-2 sm:order-1">
            <h3 class="text-sm font-bold text-ink">Verifikasi & Penimbangan</h3>
            <p class="text-xs text-ink-soft mt-1 leading-relaxed">Tim kami akan mengecek serta menimbang sampahmu.</p>
          </div>
          <div class="w-8 h-8 rounded-full bg-forest text-white flex items-center justify-center font-bold text-xs shadow-md shrink-0 order-1 sm:order-2">
            3
          </div>
          <div class="w-full sm:w-[42%] hidden sm:block order-3"></div>
        </div>

        <!-- STEP 4 (KANAN) -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-0">
          <div class="w-full sm:w-[42%] hidden sm:block order-3 sm:order-1"></div>
          <div class="w-8 h-8 rounded-full bg-forest text-white flex items-center justify-center font-bold text-xs shadow-md shrink-0 order-1 sm:order-2">
            4
          </div>
          <div class="w-full sm:w-[42%] text-center sm:text-left order-2 sm:order-3">
            <h3 class="text-sm font-bold text-ink">Dapatkan Poin</h3>
            <p class="text-xs text-ink-soft mt-1 leading-relaxed">Poin langsung masuk ke akunmu setelah verifikasi selesai.</p>
          </div>
        </div>

        <!-- STEP 5 (KIRI) -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-0">
          <div class="w-full sm:w-[42%] text-center sm:text-right order-2 sm:order-1">
            <h3 class="text-sm font-bold text-ink">Tukar & Belanja</h3>
            <p class="text-xs text-ink-soft mt-1 leading-relaxed">Pakai poin untuk diskon belanja produk kriya atau cairkan jadi uang tunai.</p>
          </div>
          <div class="w-8 h-8 rounded-full bg-forest text-white flex items-center justify-center font-bold text-xs shadow-md shrink-0 order-1 sm:order-2">
            5
          </div>
          <div class="w-full sm:w-[42%] hidden sm:block order-3"></div>
        </div>

        <!-- STEP 6 (KANAN) -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-0">
          <div class="w-full sm:w-[42%] hidden sm:block order-3 sm:order-1"></div>
          <div class="w-8 h-8 rounded-full bg-forest text-white flex items-center justify-center font-bold text-xs shadow-md shrink-0 order-1 sm:order-2">
            6
          </div>
          <div class="w-full sm:w-[42%] text-center sm:text-left order-2 sm:order-3">
            <h3 class="text-sm font-bold text-ink">Langganan Layanan</h3>
            <p class="text-xs text-ink-soft mt-1 leading-relaxed">Akses fitur khusus seperti jadwal rutin dan promo eksklusif untuk warga maupun perusahaan.</p>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ============ Live Count Dampak Webapp ============ -->
  <section id="live-count-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 mb-16 relative z-20">
    <div class="relative rounded-[2rem] bg-gradient-to-br from-forest-light via-sand to-forest-light border border-ink/10 overflow-hidden">
      
      <div class="dot-grid absolute inset-0 text-forest/10"></div>
      <div class="relative px-6 sm:px-12 py-10 sm:py-16 flex flex-col items-center justify-center gap-10 sm:gap-14">
        
        <div class="text-center">
          <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-forest tracking-tight">
            Lihat kontribusimu untuk lingkungan!
          </h2>
        </div>

        <div class="w-full grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-12 lg:gap-24 items-start max-w-5xl mx-auto">

          <div class="flex flex-col items-center text-center">
            <div class="flex items-baseline gap-2 mb-3">
              <span class="counter-number text-4xl sm:text-5xl font-extrabold text-ink tracking-tight" data-target="1450">0</span>
              <span class="text-base sm:text-lg font-bold text-forest">Kg</span>
            </div>
            <span class="text-xs sm:text-sm font-semibold text-ink-soft max-w-[180px] leading-snug">
              Total Sampah Diselamatkan
            </span>
          </div>

          <div class="flex flex-col items-center text-center">
            <div class="flex items-baseline gap-2 mb-3">
              <span class="counter-number text-4xl sm:text-5xl font-extrabold text-ink tracking-tight" data-target="420">0</span>
              <span class="text-base sm:text-lg font-bold text-forest">Pcs</span>
            </div>
            <span class="text-xs sm:text-sm font-semibold text-ink-soft max-w-[180px] leading-snug">
              Produk Kriya Terjual
            </span>
          </div>

          <div class="flex flex-col items-center text-center">
            <div class="flex items-baseline gap-2 mb-3">
              <span class="counter-number text-4xl sm:text-5xl font-extrabold text-ink tracking-tight" data-target="15">0</span>
              <span class="text-base sm:text-lg font-bold text-forest">Orang</span>
            </div>
            <span class="text-xs sm:text-sm font-semibold text-ink-soft max-w-[180px] leading-snug">
              Pengrajin Lokal Diberdayakan
            </span>
          </div>

        </div>

      </div>
    </div>
  </section>
</div>

<!-- ============ SECTION: JENIS SAMPAH YANG DITERIMA ============ -->
<section id="cara-memilah" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
  <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16">
    <h2 class="text-3xl sm:text-4xl font-extrabold text-forest tracking-tight mt-4">
      Jenis Sampah yang Kami Terima
    </h2>
    <p class="text-ink-soft text-sm sm:text-base mt-2">
      Pilih jenis sampah di bawah ini untuk melihat panduan cara memilahnya secara benar.
    </p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 justify-items-center">

    <!-- Card 1: Gelas Plastik -->
    <a href="/cara-memilah#gelas-plastik" class="group w-full max-w-sm bg-white rounded-3xl p-6 border border-ink/5 shadow-sm hover:shadow-md hover:border-forest/20 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
      <div class="w-full aspect-square rounded-2xl bg-sand/30 flex items-center justify-center p-6 mb-5 overflow-hidden group-hover:bg-forest-light/30 transition-colors">
        <img src="{{ asset('images/sampah/gelas-plastik.png') }}" alt="Gelas Plastik" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
      </div>
      <div>
        <h3 class="text-lg font-bold text-ink group-hover:text-forest transition-colors">Gelas Plastik</h3>
        <p class="text-sm text-ink-soft mt-1 line-clamp-2">Gelas minuman kemasan plastik bersih dan kering.</p>
      </div>
      <div class="mt-5 flex items-center gap-1.5 text-xs font-bold text-forest opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity">
        <span>Cara Memilah</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </div>
    </a>

    <!-- Card 2: Botol Plastik -->
    <a href="/cara-memilah#botol-plastik" class="group w-full max-w-sm bg-white rounded-3xl p-6 border border-ink/5 shadow-sm hover:shadow-md hover:border-forest/20 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
      <div class="w-full aspect-square rounded-2xl bg-sand/30 flex items-center justify-center p-6 mb-5 overflow-hidden group-hover:bg-forest-light/30 transition-colors">
        <img src="{{ asset('images/sampah/botol-plastik.png') }}" alt="Botol Plastik" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
      </div>
      <div>
        <h3 class="text-lg font-bold text-ink group-hover:text-forest transition-colors">Botol Plastik</h3>
        <p class="text-sm text-ink-soft mt-1 line-clamp-2">Botol PET bening/warna tanpa label atau tutup.</p>
      </div>
      <div class="mt-5 flex items-center gap-1.5 text-xs font-bold text-forest opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity">
        <span>Cara Memilah</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </div>
    </a>

    <!-- Card 3: Kertas HVS/Buku Bekas -->
    <a href="/cara-memilah#kertas-hvs" class="group w-full max-w-sm bg-white rounded-3xl p-6 border border-ink/5 shadow-sm hover:shadow-md hover:border-forest/20 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
      <div class="w-full aspect-square rounded-2xl bg-sand/30 flex items-center justify-center p-6 mb-5 overflow-hidden group-hover:bg-forest-light/30 transition-colors">
        <img src="{{ asset('images/sampah/kertas-hvs.png') }}" alt="Kertas HVS/Buku Bekas" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
      </div>
      <div>
        <h3 class="text-lg font-bold text-ink group-hover:text-forest transition-colors">Kertas HVS / Buku</h3>
        <p class="text-sm text-ink-soft mt-1 line-clamp-2">Kertas dokumen, majalah, dan buku bekas tak terpakai.</p>
      </div>
      <div class="mt-5 flex items-center gap-1.5 text-xs font-bold text-forest opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity">
        <span>Cara Memilah</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </div>
    </a>

    <!-- Card 4: Kertas Koran -->
    <a href="/cara-memilah#kertas-koran" class="group w-full max-w-sm bg-white rounded-3xl p-6 border border-ink/5 shadow-sm hover:shadow-md hover:border-forest/20 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
      <div class="w-full aspect-square rounded-2xl bg-sand/30 flex items-center justify-center p-6 mb-5 overflow-hidden group-hover:bg-forest-light/30 transition-colors">
        <img src="{{ asset('images/sampah/kertas-koran.png') }}" alt="Kertas Koran" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
      </div>
      <div>
        <h3 class="text-lg font-bold text-ink group-hover:text-forest transition-colors">Kertas Koran</h3>
        <p class="text-sm text-ink-soft mt-1 line-clamp-2">Koran bekas kering dalam kondisi terlipat rapi.</p>
      </div>
      <div class="mt-5 flex items-center gap-1.5 text-xs font-bold text-forest opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity">
        <span>Cara Memilah</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </div>
    </a>

    <!-- Card 5: Kain Perca / Tekstil -->
    <a href="/cara-memilah#kain-perca" class="group w-full max-w-sm bg-white rounded-3xl p-6 border border-ink/5 shadow-sm hover:shadow-md hover:border-forest/20 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
      <div class="w-full aspect-square rounded-2xl bg-sand/30 flex items-center justify-center p-6 mb-5 overflow-hidden group-hover:bg-forest-light/30 transition-colors">
        <img src="{{ asset('images/sampah/kain-perca.png') }}" alt="Kain Perca" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
      </div>
      <div>
        <h3 class="text-lg font-bold text-ink group-hover:text-forest transition-colors">Kain Perca / Tekstil</h3>
        <p class="text-sm text-ink-soft mt-1 line-clamp-2">Sisa potongan kain konveksi atau pakaian rusak.</p>
      </div>
      <div class="mt-5 flex items-center gap-1.5 text-xs font-bold text-forest opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity">
        <span>Cara Memilah</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </div>
    </a>

    <!-- Card 6: Plastik Kresek -->
    <a href="/cara-memilah#plastik-kresek" class="group w-full max-w-sm bg-white rounded-3xl p-6 border border-ink/5 shadow-sm hover:shadow-md hover:border-forest/20 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
      <div class="w-full aspect-square rounded-2xl bg-sand/30 flex items-center justify-center p-6 mb-5 overflow-hidden group-hover:bg-forest-light/30 transition-colors">
        <img src="{{ asset('images/sampah/plastik-kresek.png') }}" alt="Plastik Kresek" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
      </div>
      <div>
        <h3 class="text-lg font-bold text-ink group-hover:text-forest transition-colors">Plastik Kresek</h3>
        <p class="text-sm text-ink-soft mt-1 line-clamp-2">Kantong belanja limbah plastik tipis bersih.</p>
      </div>
      <div class="mt-5 flex items-center gap-1.5 text-xs font-bold text-forest opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity">
        <span>Cara Memilah</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </div>
    </a>

    <!-- Card 7: Kaleng Besi/Seng -->
    <a href="/cara-memilah#kaleng-besi" class="group w-full max-w-sm bg-white rounded-3xl p-6 border border-ink/5 shadow-sm hover:shadow-md hover:border-forest/20 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
      <div class="w-full aspect-square rounded-2xl bg-sand/30 flex items-center justify-center p-6 mb-5 overflow-hidden group-hover:bg-forest-light/30 transition-colors">
        <img src="{{ asset('images/sampah/kaleng-besi.png') }}" alt="Kaleng Besi" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
      </div>
      <div>
        <h3 class="text-lg font-bold text-ink group-hover:text-forest transition-colors">Kaleng Besi / Seng</h3>
        <p class="text-sm text-ink-soft mt-1 line-clamp-2">Kaleng bekas minuman, biskuit, atau makanan kaleng.</p>
      </div>
      <div class="mt-5 flex items-center gap-1.5 text-xs font-bold text-forest opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity">
        <span>Cara Memilah</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </div>
    </a>

    <!-- Card 8: Botol Kaca -->
    <a href="/cara-memilah#botol-kaca" class="group w-full max-w-sm bg-white rounded-3xl p-6 border border-ink/5 shadow-sm hover:shadow-md hover:border-forest/20 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
      <div class="w-full aspect-square rounded-2xl bg-sand/30 flex items-center justify-center p-6 mb-5 overflow-hidden group-hover:bg-forest-light/30 transition-colors">
        <img src="{{ asset('images/sampah/botol-kaca.png') }}" alt="Botol Kaca" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
      </div>
      <div>
        <h3 class="text-lg font-bold text-ink group-hover:text-forest transition-colors">Botol Kaca</h3>
        <p class="text-sm text-ink-soft mt-1 line-clamp-2">Botol kecap, sirup, atau toples kaca utuh tak retak.</p>
      </div>
      <div class="mt-5 flex items-center gap-1.5 text-xs font-bold text-forest opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity">
        <span>Cara Memilah</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </div>
    </a>

    <!-- Card 9: Logam Tembaga -->
    <a href="/cara-memilah#logam-tembaga" class="group w-full max-w-sm bg-white rounded-3xl p-6 border border-ink/5 shadow-sm hover:shadow-md hover:border-forest/20 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
      <div class="w-full aspect-square rounded-2xl bg-sand/30 flex items-center justify-center p-6 mb-5 overflow-hidden group-hover:bg-forest-light/30 transition-colors">
        <img src="{{ asset('images/sampah/logam-tembaga.png') }}" alt="Logam Tembaga" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
      </div>
      <div>
        <h3 class="text-lg font-bold text-ink group-hover:text-forest transition-colors">Logam Tembaga</h3>
        <p class="text-sm text-ink-soft mt-1 line-clamp-2">Kabel tembaga bekas, kumparan, atau peralatan kuningan.</p>
      </div>
      <div class="mt-5 flex items-center gap-1.5 text-xs font-bold text-forest opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity">
        <span>Cara Memilah</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </div>
    </a>

    <!-- Card 10: Besi Tua/Padat -->
    <a href="/cara-memilah#besi-tua" class="group w-full max-w-sm bg-white rounded-3xl p-6 border border-ink/5 shadow-sm hover:shadow-md hover:border-forest/20 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
      <div class="w-full aspect-square rounded-2xl bg-sand/30 flex items-center justify-center p-6 mb-5 overflow-hidden group-hover:bg-forest-light/30 transition-colors">
        <img src="{{ asset('images/sampah/besi-tua.png') }}" alt="Besi Tua" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
      </div>
      <div>
        <h3 class="text-lg font-bold text-ink group-hover:text-forest transition-colors">Besi Tua / Padat</h3>
        <p class="text-sm text-ink-soft mt-1 line-clamp-2">Pipa besi, rangka kendaraan, atau peralatan teknik bekas.</p>
      </div>
      <div class="mt-5 flex items-center gap-1.5 text-xs font-bold text-forest opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity">
        <span>Cara Memilah</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </div>
    </a>

    <!-- Card 11: Elektronik Bekas -->
    <a href="/cara-memilah#elektronik-bekas" class="group w-full max-w-sm bg-white rounded-3xl p-6 border border-ink/5 shadow-sm hover:shadow-md hover:border-forest/20 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
      <div class="w-full aspect-square rounded-2xl bg-sand/30 flex items-center justify-center p-6 mb-5 overflow-hidden group-hover:bg-forest-light/30 transition-colors">
        <img src="{{ asset('images/sampah/elektronik-bekas.png') }}" alt="Elektronik Bekas" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
      </div>
      <div>
        <h3 class="text-lg font-bold text-ink group-hover:text-forest transition-colors">Elektronik Bekas</h3>
        <p class="text-sm text-ink-soft mt-1 line-clamp-2">Komponen HP, laptop bekas, dan sirkuit elektronik.</p>
      </div>
      <div class="mt-5 flex items-center gap-1.5 text-xs font-bold text-forest opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity">
        <span>Cara Memilah</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </div>
    </a>

    <!-- Card 12: Karton Makanan -->
    <a href="/cara-memilah#karton-makanan" class="group w-full max-w-sm bg-white rounded-3xl p-6 border border-ink/5 shadow-sm hover:shadow-md hover:border-forest/20 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
      <div class="w-full aspect-square rounded-2xl bg-sand/30 flex items-center justify-center p-6 mb-5 overflow-hidden group-hover:bg-forest-light/30 transition-colors">
        <img src="{{ asset('images/sampah/karton-makanan.png') }}" alt="Karton Makanan" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
      </div>
      <div>
        <h3 class="text-lg font-bold text-ink group-hover:text-forest transition-colors">Karton Makanan</h3>
        <p class="text-sm text-ink-soft mt-1 line-clamp-2">Dus kemasan susu, jus (tetrapak), dan kemasan makanan.</p>
      </div>
      <div class="mt-5 flex items-center gap-1.5 text-xs font-bold text-forest opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity">
        <span>Cara Memilah</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </div>
    </a>

  </div>
</section>

<!-- ============ SECTION: KALKULATOR ESTIMASI NILAI SAMPAH ============ -->
<section id="kalkulator" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
  <div class="bg-forest rounded-3xl p-6 sm:p-12 text-white relative overflow-hidden shadow-xl">
    
    <div class="text-center max-w-2xl mx-auto mb-10">
      <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
        Hitung Nilai Sampahmu
      </h2>
      <p class="text-white/80 text-sm sm:text-base mt-3">
        Simulasikan berapa banyak nilai yang bisa kamu dapatkan dengan menukarkan sampah rumah tanggamu.
      </p>
    </div>

    <div id="kalkulator" class="max-w-xl mx-auto bg-white/10 backdrop-blur-md rounded-2xl p-6 sm:p-8 border border-white/15 shadow-inner">
      
      <div id="calculator-rows" class="space-y-4">
        
        <div class="calc-row flex items-center gap-2 sm:gap-3 py-2 border-b border-white/10 pb-4">
          <div class="flex-1">
            <select class="waste-select w-full bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/40 cursor-pointer">
              <option value="" selected class="text-ink">-- Pilih Jenis Sampah --</option>
              <option value="gelas-plastik" data-price="2500" class="text-ink">Gelas Plastik</option>
              <option value="botol-plastik" data-price="3000" class="text-ink">Botol Plastik</option>
              <option value="kertas-hvs" data-price="1500" class="text-ink">Kertas HVS / Buku Bekas</option>
              <option value="kertas-koran" data-price="1200" class="text-ink">Kertas Koran</option>
              <option value="kain-perca" data-price="800" class="text-ink">Kain Perca / Limbah Tekstil</option>
              <option value="plastik-kresek" data-price="500" class="text-ink">Plastik Kresek</option>
              <option value="kaleng-besi" data-price="3500" class="text-ink">Kaleng Besi / Seng</option>
              <option value="botol-kaca" data-price="1000" class="text-ink">Botol Kaca</option>
              <option value="logam-tembaga" data-price="60000" class="text-ink">Logam Tembaga</option>
              <option value="besi-tua" data-price="4500" class="text-ink">Besi Tua / Padat</option>
              <option value="elektronik-bekas" data-price="8000" class="text-ink">Elektronik Bekas</option>
              <option value="karton-makanan" data-price="1000" class="text-ink">Karton Makanan</option>
            </select>
          </div>

          <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
            <button type="button" class="btn-minus w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center font-bold text-base sm:text-lg transition-colors cursor-pointer select-none">
              -
            </button>
            <input type="number" step="0.1" min="0" value="1" class="weight-input w-12 sm:w-16 text-center bg-transparent font-bold text-sm sm:text-base text-white outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />
            <span class="text-xs text-white/70">Kg</span>
            <button type="button" class="btn-plus w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center font-bold text-base sm:text-lg transition-colors cursor-pointer select-none">
              +
            </button>
          </div>

          <button type="button" class="btn-delete hidden text-white/40 hover:text-red-300 p-1 transition-colors cursor-pointer" title="Hapus baris">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
          </button>
        </div>

      </div>

      <button type="button" id="add-row-btn" class="mt-4 text-xs font-semibold text-white/80 hover:text-white flex items-center gap-1.5 transition-colors cursor-pointer">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
        <span>Tambah Jenis Sampah Lain</span>
      </button>

      <div class="mt-6 pt-6 border-t border-white/20 flex items-center justify-between">
        <span class="text-base sm:text-lg font-bold text-white/90">Estimasi Nilai</span>
        <span id="total-estimation" class="text-2xl sm:text-3xl font-extrabold text-white">Rp 2.500</span>
      </div>
    </div>

      <div class="mt-8 text-center">
         @if(session()->has('user_id'))
          <a href="/setor-sampah" class="inline-flex items-center justify-center bg-cream hover:bg-white text-forest font-extrabold text-sm sm:text-base px-8 py-3.5 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
            Mulai Setor Sampah Sekarang!
          </a>
        @else
          <a href="{{ route('login') }}" class="inline-flex items-center justify-center bg-cream hover:bg-white text-forest font-extrabold text-sm sm:text-base px-8 py-3.5 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
            Mulai Setor Sampah Sekarang!
          </a>
        @endif
      </div>

    <div class="mt-6 text-center text-xs text-white/70 space-y-1">
      <p class="flex items-center justify-center gap-1.5">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span>Harga berlaku sejak: <strong class="text-white font-semibold">{{ $lastUpdatedDate ?? '26 Agustus 2026' }}</strong></span>
      </p>
      <p class="italic text-white/60">
        *Estimasi harga bersifat tidak mengikat dan dapat berubah sewaktu-waktu sesuai penyesuaian dari pengelola SulapaKarya.
      </p>
    </div>

  </div>
</section>


<!-- ============ Katalog Produk Daur Ulang ============ -->
<section id="katalog" class="py-12 sm:py-16 lg:py-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Section Header (Tengah/Center & Tanpa Background Pemisah) -->
    <div class="text-center max-w-2xl mx-auto mb-12">
      <h2 class="text-3xl sm:text-4xl font-extrabold text-forest tracking-tight">
        Katalog Kriya Daur Ulang
      </h2>
      <p class="text-ink-soft text-sm sm:text-base mt-2">
        Dukung pengrajin lokal dengan membeli produk hasil olahan sampah berkualitas.
      </p>
    </div>

  @if(isset($products))  
    <!-- Grid Produk (Menampilkan Produk + Card CTA Katalog di Akhir) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      
      @forelse($products->take(3) as $product)
        <!-- CARD PRODUK -->
        <div class="bg-white rounded-3xl border border-ink/10 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between group">
          
          <!-- Foto Produk -->
          <div class="aspect-square bg-cream/30 overflow-hidden relative">
            @if($product->photo_path)
              <img src="{{ \Illuminate\Support\Facades\Storage::url($product->photo_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
              <div class="w-full h-full grid place-items-center text-forest/50 bg-sand/30">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
              </div>
            @endif
          </div>

          <!-- Informasi Produk & Tombol Detail -->
          <div class="p-5 flex flex-col justify-between flex-grow">
            <div>
              <h3 class="text-base font-bold text-ink leading-snug line-clamp-1 mb-1">{{ $product->name }}</h3>
              <p class="text-forest font-bold text-base mb-4">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            </div>

            <!-- Tombol Detail Produk -->
             @if(session()->has('user_id'))
              <button type="button" onclick="document.getElementById('detail_modal_{{ $product->id }}').showModal()" class="w-full bg-forest hover:bg-forest/90 text-white text-xs font-bold py-3 rounded-full transition-colors text-center cursor-pointer">
                Detail Produk
              </button>
            @else
              <a href="{{ route('login') }}" class="w-full bg-forest hover:bg-forest/90 text-white text-xs font-bold py-3 rounded-full transition-colors text-center block">
                Detail Produk
              </a>
            @endif
          </div>
        </div>

        <!-- MODAL DETAIL PRODUK -->
        <dialog id="detail_modal_{{ $product->id }}" class="modal modal-bottom sm:modal-middle">
          <div class="modal-box bg-white max-w-md rounded-[2.5rem] border border-ink/5 p-6 text-left relative">
            <form method="dialog">
              <button class="btn btn-sm btn-circle btn-ghost absolute right-5 top-5 text-ink-soft bg-gray-100 hover:bg-gray-200 border-none">✕</button>
            </form>
            <h3 class="font-display font-extrabold text-xl text-ink border-b border-ink/5 pb-3">Detail Hasil Karya</h3>

            <div class="mt-4 space-y-4">
              <div class="w-full h-52 rounded-2xl overflow-hidden bg-cream/30 border border-ink/5">
                @if($product->photo_path)
                  <img src="{{ \Illuminate\Support\Facades\Storage::url($product->photo_path) }}" class="w-full h-full object-cover">
                @else
                  <div class="w-full h-full grid place-items-center text-forest/50 bg-cream/50">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                  </div>
                @endif
              </div>

              <div>
                <span class="text-[10px] font-extrabold bg-maritime/10 text-maritime px-2.5 py-1 rounded-md uppercase tracking-wider">{{ $product->product_category ?? 'Kriya Daur Ulang' }}</span>
                <h4 class="font-display font-extrabold text-lg text-ink mt-2 leading-snug">{{ $product->name }}</h4>
                <p class="font-mono font-black text-forest text-xl mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
              </div>

              @if($product->description)
                <div class="bg-cream/20 border border-ink/5 rounded-xl p-3">
                  <span class="text-[10px] font-bold text-ink-soft block mb-1">Deskripsi Produk:</span>
                  <p class="text-xs text-ink-soft/90 leading-relaxed">{{ $product->description }}</p>
                </div>
              @endif

              @if(($product->stock ?? 1) > 0)
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="w-full">
                  @csrf
                  <button type="submit" class="btn w-full bg-forest hover:bg-forest/90 border-none text-white rounded-xl font-bold normal-case shadow-md shadow-forest/20 h-12">
                    🛒 + Keranjang
                  </button>
                </form>
              @else
                <button disabled class="btn w-full bg-gray-300 border-none text-gray-500 rounded-xl font-bold normal-case h-12 cursor-not-allowed">
                  Produk Habis
                </button>
              @endif
            </div>
          </div>
          <form method="dialog" class="modal-backdrop bg-ink/30 backdrop-blur-sm">
            <button>close</button>
          </form>
        </dialog>

      @empty
      @endforelse

      <!-- CARD KHUSUS: TOMBOL HALAMAN KATALOG KRIYA -->
       @if(session()->has('user_id'))
        <!-- Jika Sudah Login: Langsung ke Halaman Katalog -->
        <a href="/katalog" class="group bg-white rounded-3xl border border-forest/40 hover:border-forest p-6 flex flex-col justify-center items-center text-center transition-all duration-300 shadow-sm hover:shadow-md min-h-[320px]">
          <div class="w-14 h-14 rounded-full bg-forest/10 text-forest flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-forest group-hover:text-white transition-all duration-300">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </div>
          <h3 class="text-lg font-bold text-forest mb-1">Lihat Semua Produk</h3>
          <p class="text-xs text-ink-soft max-w-[180px]">Jelajahi seluruh karya daur ulang di halaman Katalog Kriya</p>
        </a>
      @else
        <!-- Jika Belum Login: Redirect ke Halaman Login -->
        <a href="{{ route('login') }}" class="group bg-white rounded-3xl border border-forest/40 hover:border-forest p-6 flex flex-col justify-center items-center text-center transition-all duration-300 shadow-sm hover:shadow-md min-h-[320px]">
          <div class="w-14 h-14 rounded-full bg-forest/10 text-forest flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-forest group-hover:text-white transition-all duration-300">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </div>
          <h3 class="text-lg font-bold text-forest mb-1">Lihat Semua Produk</h3>
          <p class="text-xs text-ink-soft max-w-[180px]">Jelajahi seluruh karya daur ulang di halaman Katalog Kriya</p>
        </a>
      @endif

    </div>
  @endif
  </div>
</section>

<!-- ============ CLOSING TAGLINE ============ -->
<section class="py-12 sm:py-16">
  <p class="text-center font-display italic text-lg sm:text-xl text-ink-soft max-w-2xl mx-auto px-6">
    “Satu langkah kecilmu hari ini, adalah harapan besar untuk bumi esok hari.”
  </p>
</section>
@endsection

<script>
  document.addEventListener("DOMContentLoaded", () => {
    
    const counterSection = document.getElementById("live-count-section");
    const counters = document.querySelectorAll(".counter-number");
    let hasAnimated = false;

    const bottleImg = document.getElementById("hero-bottle-image");

    if (bottleImg) {
      const bottleObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            // Pasang animasi saat terlihat di layar
            bottleImg.classList.add("animate-slide-in");
          } else {
            // Reset state saat keluar dari pandangan layar
            bottleImg.classList.remove("animate-slide-in");
          }
        });
      }, { threshold: 0.2 });

      bottleObserver.observe(bottleImg);
    }

    const animateCounters = () => {
      counters.forEach((counter) => {
        const target = +counter.getAttribute("data-target");
        const duration = 1800; // Durasi animasi (ms)
        const frameRate = 1000 / 60;
        const totalFrames = Math.round(duration / frameRate);
        let currentFrame = 0;

        const countUp = setInterval(() => {
          currentFrame++;
          const progress = currentFrame / totalFrames;
          // Easing formula (easeOutQuad) agar animasi melambat mulus di akhir
          const currentCount = Math.floor(target * (1 - Math.pow(1 - progress, 2)));

          // Format ribuan (contoh: 1.450)
          counter.innerText = currentCount.toLocaleString("id-ID");

          if (currentFrame === totalFrames) {
            counter.innerText = target.toLocaleString("id-ID");
            clearInterval(countUp);
          }
        }, frameRate);
      });
    };

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting && !hasAnimated) {
            hasAnimated = true;
            animateCounters();
          }
        });
      },
      { threshold: 0.3 } // Animasi dipicu saat 30% elemen terlihat di layar
    );

    if (counterSection) {
      observer.observe(counterSection);
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('calculator-rows');
    const addBtn = document.getElementById('add-row-btn');
    const totalDisplay = document.getElementById('total-estimation');

    // Hitung total nilai sampah
    function calculateTotal() {
      let total = 0;
      const rows = container.querySelectorAll('.calc-row');

      rows.forEach(row => {
        const select = row.querySelector('.waste-select');
        const input = row.querySelector('.weight-input');
        const selectedOption = select.options[select.selectedIndex];
        
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const weight = parseFloat(input.value) || 0;

        total += price * weight;
      });

      // Format ke mata uang Rupiah
      totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');

      // Tampilkan/Sembunyikan tombol hapus jika baris > 1
      rows.forEach(row => {
        const deleteBtn = row.querySelector('.btn-delete');
        if (rows.length > 1) {
          deleteBtn.classList.remove('hidden');
        } else {
          deleteBtn.classList.add('hidden');
        }
      });
    }

    // Event Listener untuk tombol Tambah Baris
    addBtn.addEventListener('click', function () {
      const firstRow = container.querySelector('.calc-row');
      const newRow = firstRow.cloneNode(true);

      // Reset nilai input & select pada baris baru
      newRow.querySelector('.waste-select').selectedIndex = 0;
      newRow.querySelector('.weight-input').value = 1;

      container.appendChild(newRow);
      calculateTotal();
    });

    // Delegasi Event untuk Plus, Minus, Hapus, dan Input Change
    container.addEventListener('click', function (e) {
      const row = e.target.closest('.calc-row');
      if (!row) return;

      const input = row.querySelector('.weight-input');

      // Tombol Plus (+)
      if (e.target.closest('.btn-plus')) {
        let currentVal = parseFloat(input.value) || 0;
        input.value = (currentVal + 1).toFixed(1).replace(/\.0$/, '');
        calculateTotal();
      }

      // Tombol Minus (-)
      if (e.target.closest('.btn-minus')) {
        let currentVal = parseFloat(input.value) || 0;
        if (currentVal > 0.5) {
          input.value = (currentVal - 1).toFixed(1).replace(/\.0$/, '');
        } else if (currentVal > 0) {
          input.value = 0;
        }
        calculateTotal();
      }

      // Tombol Hapus (X)
      if (e.target.closest('.btn-delete')) {
        const rows = container.querySelectorAll('.calc-row');
        if (rows.length > 1) {
          row.remove();
          calculateTotal();
        }
      }
    });

    // Event saat dropdown atau isi angka berubah langsung
    container.addEventListener('input', calculateTotal);
    container.addEventListener('change', calculateTotal);

    // Jalankan kalkulasi awal
    calculateTotal();
  });
</script>