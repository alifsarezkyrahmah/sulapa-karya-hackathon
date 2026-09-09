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
      
      <div class="lg:col-span-6 flex flex-col items-start space-y-6 z-10">
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-forest leading-[1.15] tracking-tight">
          Satu Sampah di Tanganmu Dapat Menjadi Nilai yang Berharga
        </h1>

        <div class="pt-8">
          @if(session()->has('user_id'))
            <a href="/setor-sampah" class="btn bg-forest hover:bg-forest/90 text-white font-bold rounded-full px-8 py-3 text-sm normal-case border-none shadow-md shadow-forest/20 transition-all duration-200 hover:scale-105 active:scale-95">
              Mulai Setor Sekarang!
            </a>
          @else
            <a href="{{ route('login') }}" class="btn bg-forest hover:bg-forest/90 text-white font-bold rounded-full px-8 py-3 text-sm normal-case border-none shadow-md shadow-forest/20 transition-all duration-200 hover:scale-105 active:scale-95">
              Mulai Setor Sekarang!
            </a>
          @endif
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

<!-- ============ Live Count Dampak Webapp ============ -->
<section id="live-count-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 mb-16 relative z-20">
  <div class="relative rounded-[2rem] bg-gradient-to-br from-forest-light via-sand to-forest-light border border-ink/10 overflow-hidden">
    
    <div class="dot-grid absolute inset-0 text-forest/10"></div>
    <div class="relative px-6 sm:px-12 py-10 sm:py-16 flex flex-col items-center justify-center gap-10 sm:gap-14">
      
      <div class="text-center">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-forest tracking-tight">
          Lihat kontribusimu untuk lingkungan, secara langsung!
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

<!-- ============ SECTION: SULAPAKARYA PRO (KEMITRAAN BISNIS) ============ -->
<section id="mitra-bisnis" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-2">
  <div class="relative rounded-3xl bg-white border border-ink/10 p-6 sm:p-10 lg:p-14 overflow-hidden shadow-sm">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
      
      <div class="lg:col-span-7 space-y-5 sm:space-y-6 text-left">
        <div class="inline-flex items-center gap-2 border border-forest/20 bg-forest/5 px-3 py-1 rounded-full">
          <span class="w-1.5 h-1.5 rounded-full bg-forest"></span>
          <span class="text-[10px] font-mono font-semibold tracking-widest uppercase text-forest">SulapaKarya PRO &bull; Solusi B2B</span>
        </div>

        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-forest tracking-tight leading-tight">
          Pengelolaan sampah terpadu untuk efisiensi operasional usaha Anda.
        </h2>

        <p class="text-xs sm:text-sm text-ink-soft leading-relaxed max-w-xl">
          Dirancang khusus untuk warkop, kafe, restoran, dan gerai usaha di Makassar. Bebaskan area kerja dari tumpukan material sisa dengan armada logistik penjemputan terjadwal rutin.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
          <div class="p-4 rounded-2xl bg-cream/40 border border-ink/5 space-y-1.5">
            <div class="flex items-center gap-2 text-ink">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-forest"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <h4 class="text-xs font-bold">Penjemputan Terjadwal</h4>
            </div>
            <p class="text-[11px] text-ink-soft leading-relaxed">Pengaturan jadwal hari dan jam jemput berkala tanpa perlu order manual setiap waktu.</p>
          </div>

          <div class="p-4 rounded-2xl bg-cream/40 border border-ink/5 space-y-1.5">
            <div class="flex items-center gap-2 text-ink">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-forest"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
              <h4 class="text-xs font-bold">Prioritas Armada</h4>
            </div>
            <p class="text-[11px] text-ink-soft leading-relaxed">Kepastian waktu kedatangan kurir lapangan sebelum jam sibuk operasional usaha dimulai.</p>
          </div>

          <div class="p-4 rounded-2xl bg-cream/40 border border-ink/5 space-y-1.5">
            <div class="flex items-center gap-2 text-ink">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-forest"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
              <h4 class="text-xs font-bold">Diskon Produk Kriya</h4>
            </div>
            <p class="text-[11px] text-ink-soft leading-relaxed">Potongan harga eksklusif untuk pengadaan cinderamata dan dekorasi daur ulang.</p>
          </div>

          <div class="p-4 rounded-2xl bg-cream/40 border border-ink/5 space-y-1.5">
            <div class="flex items-center gap-2 text-ink">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-forest"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
              <h4 class="text-xs font-bold">Rekapitulasi Lingkungan</h4>
            </div>
            <p class="text-[11px] text-ink-soft leading-relaxed">Pantau data akumulasi tonase sampah terkelola beserta poin yang siap dicairkan ke kas usaha.</p>
          </div>
        </div>

        <div class="pt-2 flex flex-col sm:flex-row items-start sm:items-center gap-3.5">
          @if(session()->has('user_id'))
            <a href="/mitra-bisnis" class="btn bg-forest hover:bg-forest/90 text-white font-bold rounded-full px-8 py-3 text-xs normal-case border-none shadow-md shadow-forest/20 transition-all duration-200 w-full sm:w-auto text-center">
              Daftar sebagai Mitra Bisnis &rarr;
            </a>
          @else
            <a href="{{ route('login') }}" class="btn bg-forest hover:bg-forest/90 text-white font-bold rounded-full px-8 py-3 text-xs normal-case border-none shadow-md shadow-forest/20 transition-all duration-200 w-full sm:w-auto text-center">
              Daftar sebagai Mitra Bisnis &rarr;
            </a>
          @endif
          <span class="text-[11px] text-ink-soft font-mono">Verifikasi akun kemitraan bebas biaya</span>
        </div>
      </div>

      <!-- Kartu Harga & Paket Kemitraan PRO (Sisi Kanan) -->
      <div class="lg:col-span-5 flex justify-center w-full">
        <div class="w-full max-w-sm rounded-3xl bg-[#1C1A16] text-[#E5DFD5] border border-white/10 p-6 sm:p-7 space-y-4 text-left shadow-lg relative overflow-hidden">
          
          <div class="flex items-center justify-between pb-3 border-b border-white/10">
            <div>
              <span class="text-[10px] uppercase font-mono tracking-widest text-[#8C8478] block">Paket Kemitraan</span>
              <h4 class="text-sm font-bold text-white mt-0.5">SulapaKarya PRO</h4>
            </div>
            <span class="text-[9px] font-mono font-bold text-amber-400 bg-amber-400/15 px-2 py-0.5 rounded border border-amber-400/30 uppercase tracking-wider">
              B2B Partner
            </span>
          </div>

          <!-- Blok Penampilan Harga 67rb -->
          <div class="p-4 rounded-2xl bg-white/[0.04] border border-white/10 text-center space-y-1">
            <span class="text-[10px] text-[#A8A095] uppercase font-mono tracking-wider block">Biaya Layanan Operasional</span>
            <div class="flex items-baseline justify-center gap-1">
              <span class="text-xs font-bold text-[#A8A095]">Rp</span>
              <span class="text-3xl sm:text-4xl font-black font-mono text-white tracking-tight">67.000</span>
              <span class="text-xs text-[#A8A095] font-sans">/ bulan</span>
            </div>
            <p class="text-[10px] text-forest font-mono font-semibold pt-1">Termasuk seluruh rute penjemputan berkala</p>
          </div>

          <div class="space-y-2 text-xs">
            <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
              <span class="text-[#A8A095]">Rute Armada</span>
              <span class="font-mono text-white font-bold">Terjadwal Rutin</span>
            </div>
            <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
              <span class="text-[#A8A095]">Prioritas Penanganan</span>
              <span class="font-semibold text-white">Jalur Utama</span>
            </div>
            <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
              <span class="text-[#A8A095]">Konversi Insentif</span>
              <span class="font-mono text-forest font-bold">Poin Reward Usaha</span>
            </div>
          </div>

          <div class="pt-2 border-t border-white/10 text-[11px] text-[#8C8478] leading-relaxed text-center">
            Mendukung pelaporan tanggung jawab lingkungan dan integrasi ekonomi sirkular lokal di Makassar.
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ============ SECTION: JENIS SAMPAH YANG DITERIMA (DINAMIS DARI DATABASE) ============ -->
<section id="katalog-sampah" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
  <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-12">
    <h2 class="text-3xl sm:text-4xl font-extrabold text-forest tracking-tight mt-4">
      Jenis Sampah yang Kami Terima
    </h2>
    <p class="text-ink-soft text-sm sm:text-base mt-2">
      Setorkan sampah terpilah untuk mendapatkan poin tukar kriya bernilai manfaat.
    </p>

    <!-- Ringkasan Standar QC Terpadu -->
    <div class="mt-6 p-4 sm:p-5 bg-white rounded-2xl border border-ink/10 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4 text-left">
      <div class="space-y-1">
        <div class="flex items-center gap-2">
          <span class="badge badge-xs bg-forest/15 text-forest border-none font-bold text-[10px] px-2 py-0.5">STANDAR QC</span>
          <span class="text-xs font-bold text-ink">Verifikasi Kelayakan Sampah</span>
        </div>
        <p class="text-xs text-ink-soft leading-relaxed">
          QC (Quality Control) adalah syarat kelayakan fisik sampah sebelum ditimbang oleh kurir—wajib terpilah bersih dari residu minyak atau sisa makanan, dalam kondisi kering, dan terkemas rapi.
        </p>
      </div>
      <a href="https://drive.google.com/file/d/1wmkeqhpKQgUlFeEltqR7b7xnTHrDIE32/view?usp=sharing" target="_blank" rel="noopener noreferrer" class="btn btn-sm bg-forest hover:bg-forest/90 text-white rounded-xl text-xs font-bold normal-case px-4 py-2 shrink-0 border-none shadow-sm flex items-center gap-1.5 w-full sm:w-auto justify-center">
        <span>Buka Panduan QC</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
      </a>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 justify-items-center">
    @forelse($wastePrices as $wp)
      @php
        $nameLower = strtolower($wp->name);
        $imageName = 'gelas-plastik.png';
        if (str_contains($nameLower, 'botol plastik')) $imageName = 'botol-plastik.png';
        elseif (str_contains($nameLower, 'hvs') || str_contains($nameLower, 'buku')) $imageName = 'kertas-hvs.png';
        elseif (str_contains($nameLower, 'koran')) $imageName = 'kertas-koran.png';
        elseif (str_contains($nameLower, 'kain') || str_contains($nameLower, 'tekstil')) $imageName = 'kain-perca.png';
        elseif (str_contains($nameLower, 'kresek')) $imageName = 'plastik-kresek.png';
        elseif (str_contains($nameLower, 'kaleng') || str_contains($nameLower, 'seng')) $imageName = 'kaleng-besi.png';
        elseif (str_contains($nameLower, 'kaca')) $imageName = 'botol-kaca.png';
        elseif (str_contains($nameLower, 'tembaga')) $imageName = 'logam-tembaga.png';
        elseif (str_contains($nameLower, 'besi')) $imageName = 'besi-tua.png';
        elseif (str_contains($nameLower, 'elektronik') || str_contains($nameLower, 'e-waste')) $imageName = 'elektronik-bekas.png';
        elseif (str_contains($nameLower, 'karton') || str_contains($nameLower, 'dupleks') || str_contains($nameLower, 'kardus')) $imageName = 'karton-makanan.png';
      @endphp

      <div class="w-full max-w-sm bg-white rounded-3xl p-6 border border-ink/5 shadow-sm hover:shadow-md hover:border-forest/20 transition-all duration-300 flex flex-col justify-between relative overflow-hidden">
        <div class="w-full aspect-square rounded-2xl bg-sand/30 flex items-center justify-center p-6 mb-5 overflow-hidden">
          <img src="{{ asset('images/sampah/' . $imageName) }}" alt="{{ $wp->name }}" class="w-full h-full object-contain" onerror="this.src='{{ asset('images/tangan-botol.png') }}'">
        </div>
        <div>
          <div class="flex items-center justify-between gap-2">
            <h3 class="text-base sm:text-lg font-bold text-ink">{{ $wp->name }}</h3>
            <span class="badge bg-maritime/10 text-maritime border-none font-mono font-bold text-xs shrink-0 py-2.5 px-2">
              {{ number_format($wp->point_per_kg, 0, ',', '.') }} Poin/kg
            </span>
          </div>
          <p class="text-xs text-ink-soft mt-2 line-clamp-2">{{ $wp->description ?? 'Pastikan sampah dalam kondisi bersih dan kering sebelum disetor.' }}</p>
        </div>
      </div>
    @empty
      <div class="col-span-3 py-12 text-center text-ink-soft/60">
        Belum ada data jenis sampah yang dimuat.
      </div>
    @endforelse
  </div>
</section>

<!-- ============ SECTION: KALKULATOR ESTIMASI NILAI POIN SAMPAH ============ -->
<section id="kalkulator" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
  <div class="bg-forest rounded-3xl p-6 sm:p-12 text-white relative overflow-hidden shadow-xl">
    
    <div class="text-center max-w-2xl mx-auto mb-10">
      <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
        Hitung Estimasi Poin Sampahmu
      </h2>
      <p class="text-white/80 text-sm sm:text-base mt-3">
        Simulasikan perolehan <strong>Poin Kriya</strong> dari sampah terpilah yang siap kamu setorkan.
      </p>
    </div>

    <div class="max-w-xl mx-auto bg-white/10 backdrop-blur-md rounded-2xl p-6 sm:p-8 border border-white/15 shadow-inner">
      
      <div id="calculator-rows" class="space-y-4">
        
        <div class="calc-row flex items-center gap-2 sm:gap-3 py-2 border-b border-white/10 pb-4">
          <div class="flex-1">
            <select class="waste-select w-full bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-xs sm:text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/40 cursor-pointer">
              <option value="" disabled class="text-ink">-- Pilih Jenis Sampah --</option>
              @foreach($wastePrices as $index => $wp)
                <option value="{{ $wp->id }}" data-points="{{ $wp->point_per_kg }}" {{ $index === 0 ? 'selected' : '' }} class="text-ink">
                  {{ $wp->name }} ({{ number_format($wp->point_per_kg, 0, ',', '.') }} Poin/kg)
                </option>
              @endforeach
            </select>
          </div>

          <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
            <button type="button" class="btn-minus w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center font-bold text-base sm:text-lg transition-colors cursor-pointer select-none">
              -
            </button>
            <input type="number" step="0.5" min="0" value="1" class="weight-input w-12 sm:w-16 text-center bg-transparent font-bold text-sm sm:text-base text-white outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />
            <span class="text-xs text-white/70 font-semibold">Kg</span>
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
        <div>
          <span class="text-base sm:text-lg font-bold text-white/90 block">Estimasi Total Poin</span>
          <span class="text-[11px] text-white/60">Dapat ditukarkan produk kriya</span>
        </div>
        <div class="text-right">
          <span id="total-estimation" class="text-2xl sm:text-4xl font-extrabold text-cream tracking-tight">0</span>
          <span class="text-sm font-bold text-cream/80 ml-1">Poin</span>
        </div>
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
        <span>Tarif resmi berlaku per: <strong class="text-white font-semibold">{{ $lastUpdatedDate ?? 'Terbaru' }}</strong></span>
      </p>
    </div>

  </div>
</section>

<!-- ============ Katalog Produk Daur Ulang ============ -->
<section id="produk" class="py-12 sm:py-16 lg:py-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="text-center max-w-2xl mx-auto mb-12">
      <h2 class="text-3xl sm:text-4xl font-extrabold text-forest tracking-tight">
        Katalog Produk Daur Ulang
      </h2>
      <p class="text-ink-soft text-sm sm:text-base mt-2">
        Dukung pengrajin lokal dengan membeli produk hasil olahan sampah berkualitas.
      </p>
    </div>

  @if(isset($products))  
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      
      @forelse($products->take(3) as $product)
        <div class="bg-white rounded-3xl border border-ink/10 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between group">
          
          <div class="aspect-square bg-cream/30 overflow-hidden relative">
            @if($product->photo_path)
              <img src="{{ asset('storage/' . $product->photo_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
              <div class="w-full h-full grid place-items-center text-forest/50 bg-sand/30">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
              </div>
            @endif
          </div>

          <div class="p-5 flex flex-col justify-between flex-grow">
            <div>
              <h3 class="text-base font-bold text-ink leading-snug line-clamp-1 mb-1">{{ $product->name }}</h3>
              <p class="text-forest font-bold text-base mb-4">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            </div>

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
                  <img src="{{ asset('storage/' . $product->photo_path) }}" class="w-full h-full object-cover">
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

<script>
  document.addEventListener("DOMContentLoaded", () => {
    // Animasi Counter Angka Dampak
    const counterSection = document.getElementById("live-count-section");
    const counters = document.querySelectorAll(".counter-number");
    let hasAnimated = false;

    const animateCounters = () => {
      counters.forEach((counter) => {
        const target = +counter.getAttribute("data-target");
        const duration = 1800;
        const frameRate = 1000 / 60;
        const totalFrames = Math.round(duration / frameRate);
        let currentFrame = 0;

        const countUp = setInterval(() => {
          currentFrame++;
          const progress = currentFrame / totalFrames;
          const currentCount = Math.floor(target * (1 - Math.pow(1 - progress, 2)));
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
      { threshold: 0.3 }
    );

    if (counterSection) observer.observe(counterSection);

    // ==========================================
    // LOGIKA KALKULATOR ESTIMASI POIN
    // ==========================================
    const container = document.getElementById('calculator-rows');
    const addBtn = document.getElementById('add-row-btn');
    const totalDisplay = document.getElementById('total-estimation');

    function calculateTotalPoints() {
      let totalPoints = 0;
      const rows = container.querySelectorAll('.calc-row');

      rows.forEach(row => {
        const select = row.querySelector('.waste-select');
        const input = row.querySelector('.weight-input');
        const selectedOption = select.options[select.selectedIndex];
        
        const pointPerKg = parseFloat(selectedOption?.getAttribute('data-points')) || 0;
        const weight = parseFloat(input.value) || 0;

        totalPoints += Math.round(pointPerKg * weight);
      });

      totalDisplay.textContent = totalPoints.toLocaleString('id-ID');

      rows.forEach(row => {
        const deleteBtn = row.querySelector('.btn-delete');
        if (rows.length > 1) {
          deleteBtn.classList.remove('hidden');
        } else {
          deleteBtn.classList.add('hidden');
        }
      });
    }

    addBtn.addEventListener('click', function () {
      const firstRow = container.querySelector('.calc-row');
      const newRow = firstRow.cloneNode(true);

      newRow.querySelector('.waste-select').selectedIndex = 0;
      newRow.querySelector('.weight-input').value = 1;

      container.appendChild(newRow);
      calculateTotalPoints();
    });

    container.addEventListener('click', function (e) {
      const row = e.target.closest('.calc-row');
      if (!row) return;

      const input = row.querySelector('.weight-input');

      if (e.target.closest('.btn-plus')) {
        let currentVal = parseFloat(input.value) || 0;
        input.value = (currentVal + 0.5).toFixed(1).replace(/\.0$/, '');
        calculateTotalPoints();
      }

      if (e.target.closest('.btn-minus')) {
        let currentVal = parseFloat(input.value) || 0;
        if (currentVal > 0.5) {
          input.value = (currentVal - 0.5).toFixed(1).replace(/\.0$/, '');
        } else if (currentVal > 0) {
          input.value = 0;
        }
        calculateTotalPoints();
      }

      if (e.target.closest('.btn-delete')) {
        const rows = container.querySelectorAll('.calc-row');
        if (rows.length > 1) {
          row.remove();
          calculateTotalPoints();
        }
      }
    });

    container.addEventListener('input', calculateTotalPoints);
    container.addEventListener('change', calculateTotalPoints);

    calculateTotalPoints();
  });
</script>
@endsection