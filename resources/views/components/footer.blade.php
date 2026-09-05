<!-- ============ FOOTER COMPONENT ============ -->
<footer class="bg-gradient-to-b from-[#142e20] to-[#0d2318] text-cream/80 border-t border-ink/20 relative overflow-hidden">
  <!-- Decorative Dot Background Subtle -->
  <div class="absolute inset-0 dot-grid text-cream/[0.01] pointer-events-none"></div>

  <!-- Main Footer Content -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-12 sm:py-16 lg:py-24 relative z-10">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-8 text-center md:text-left">
      
      <!-- Kolom 1: Brand / Profil -->
      <div class="flex flex-col items-center md:items-start space-y-4">
        <div class="flex items-center justify-center md:justify-start gap-3 mb-2">
          <div class="w-12 h-12 flex items-center justify-center shrink-0">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SulapaKarya" class="w-8 h-8 object-contain rounded-full shadow-md shadow-forest/20">
          </div>
          <span class="font-display font-bold text-2xl sm:text-3xl text-white tracking-tight"> SulapaKarya </span>
        </div>
        <p class="text-sm leading-relaxed text-cream/60 font-medium max-w-sm md:max-w-none">
          Inovasi Hijau dari Makassar untuk Bumi yang Lebih Berkelanjutan
        </p>
        <button onclick="scrollToTop()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-cream/10 hover:bg-cream/20 text-cream text-xs font-bold border border-cream/20 transition-all duration-300 hover:scale-105 active:scale-95 group" aria-label="Kembali ke atas">
          <span>Kembali ke atas</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="transition-transform duration-300 group-hover:-translate-y-1">
            <path d="M18 15l-6-6-6 6"/>
          </svg>
        </button>
        <!-- Seni Dekoratif Media Sosial kriya -->
        <div class="flex items-center justify-center md:justify-start gap-3 pt-2">
          <a href="https://instagram.com/sulapakarya" aria-label="Instagram" class="w-8 h-8 rounded-lg border border-white/10 grid place-items-center hover:bg-forest hover:text-white hover:border-forest transition-all duration-300">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg>
          </a>
          <a href="https://wa.me/sulapakarya" aria-label="WhatsApp" class="w-8 h-8 rounded-lg border border-white/10 grid place-items-center hover:bg-forest hover:text-white hover:border-forest transition-all duration-300">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </a>
        </div>
      </div>

      <!-- Kolom 2: Tautan Pintar -->
      <div class="md:justify-self-center">
        <ul class="space-y-3 text-sm font-medium">
          <li>
            <a href="#tentang-kami" class="text-cream/60 hover:text-white link link-hover underline-offset-4 transition-colors">
              Tentang Kami
            </a>
          </li>
          <li>
            <a href="#cara-memilah" class="text-cream/60 hover:text-white link link-hover underline-offset-4 transition-colors">
              Cara Memilah
            </a>
          </li>
          <li>
            <a href="#kalkulator" class="text-cream/60 hover:text-white link link-hover underline-offset-4 transition-colors">
              Kalkulator
            </a>
          </li>
          <li>
            <a href="setor-sampah" class="text-cream/60 hover:text-white link link-hover underline-offset-4 transition-colors {{ request()->is('setor-sampah*') ? 'bg-forest/10 text-forest font-black' : '' }}">
              Setor Sampah
            </a>
          </li>
          <li>
            <a href="katalog" class="text-cream/60 hover:text-white link link-hover underline-offset-4 transition-colors {{ request()->is('katalog*') ? 'bg-forest/10 text-forest font-black' : '' }}">
              Katalog Kriya
            </a>
          </li>

          @if (!request()->is('login') && !request()->is('register') && !session('user_id'))
            <li class="pt-2">
              <a href="/login" class="text-cream/100 hover:text-white text-sm link link-hover underline-offset-4 font-semibold transition-colors">
                Masuk / Daftar
              </a>
            </li>
          @endif
        </ul>
      </div>

      <!-- Kolom 3: Kontak Info -->
      <div class="md:justify-self-end w-full max-w-xs mx-auto md:mx-0 text-left">
        <h4 class="text-xs font-extrabold uppercase tracking-widest text-white mb-5 flex flex-col md:flex-row items-center md:items-start gap-2 border-l-0 md:border-l-2 border-forest md:pl-3">
          <!-- Garis Horizontal Khusus Mobile -->
          <span class="w-[80px] h-[2px] bg-forest md:hidden mb-1"></span>
          
          Kontak Kami
        </h4>
        <ul class="space-y-4 text-sm font-medium text-cream/60 w-fit mx-auto md:w-full">
          <li class="flex items-start gap-3 hover:text-white transition-colors">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-forest mt-0.5 shrink-0">
              <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            <div class="flex flex-col">
              <span>Jam Penjemputan</span>
              <span class="text-xs text-cream/70">Senin – Sabtu (08.00 – 17.00 WITA)</span>
            </div>
          </li>
          <li class="flex items-start gap-3 hover:text-white transition-colors">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-forest mt-0.5 shrink-0">
              <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>
            </svg>
            <span>Makassar, Sulawesi Selatan</span>
          </li>
          <li class="flex items-center gap-3 hover:text-white transition-colors">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-forest shrink-0">
              <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>
            </svg>
            <a href="mailto:sulapakarya@contact.com">sulapakarya@contact.com</a>
          </li>
          <li class="flex items-center gap-3 hover:text-white transition-colors">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-forest shrink-0">
              <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .3 2 .7 3a2 2 0 0 1-.4 2.1L8 10.2a16 16 0 0 0 6 6l1.4-1.4a2 2 0 0 1 2.1-.4c1 .4 2 .6 3 .7a2 2 0 0 1 1.5 2.1z"/>
            </svg>
            <span>+62 812-3456-7890</span>
          </li>
        </ul>
      </div>

    </div>
  </div>

  <!-- Bottom Bar: Copyright & Legal Links -->
  <div class="border-t border-white/[0.05] bg-black/15 relative z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-cream/40 text-center sm:text-left">
      <div>
        &copy; 2026 SulapaKarya. Semua hak dilindungi.
      </div>
      <div class="flex items-center gap-5 justify-center flex-wrap">
        <a href="#" class="hover:text-white transition-colors">FAQ</a>
        <span class="text-white/[0.08] hidden sm:inline">|</span>
        <a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a>
        <span class="text-white/[0.08] hidden sm:inline">|</span>
        <a href="#" class="hover:text-white transition-colors">Syarat &amp; Ketentuan</a>
      </div>
    </div>
  </div>
</footer>

<script>
  function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
</script>