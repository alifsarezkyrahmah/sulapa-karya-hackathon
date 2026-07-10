<div class="w-full px-4 sm:px-6 lg:px-8 pt-4 sticky top-0 z-50">
  <header class="max-w-7xl mx-auto border border-ink/5 bg-white/30 backdrop-blur-xl rounded-full shadow-lg shadow-ink/[0.03] transition-all duration-300">
    <nav class="navbar px-6 py-2 min-h-[4rem]">
      
      <div class="navbar-start">
        <a href="/" class="flex items-center gap-3 group transition-transform duration-200 active:scale-95">
          <img src="{{ asset('images/logo.png') }}" alt="Logo SulapaKarya" class="w-8 h-8 object-contain rounded-full shadow-md shadow-forest/20">
          <span class="font-display font-bold text-lg tracking-tight text-ink"> SulapaKarya </span>
        </a>
      </div>

      @php
        $navUser = session('user_id') ? \App\Models\User::find(session('user_id')) : null;
        $currentRole = $navUser->role ?? session('role', 'user');
        $cartCount = session('cart') ? collect(session('cart'))->sum('quantity') : 0;
      @endphp

      <div class="navbar-end w-full hidden lg:flex">
        <ul id="desktop-nav-menu" class="menu menu-horizontal gap-1 px-1 text-xs font-bold uppercase tracking-wider text-ink/70 items-center">
          <li><a href="/#beranda" data-nav="beranda" class="nav-link rounded-full px-4 py-2 hover:bg-forest/10 hover:text-forest transition-all">Beranda</a></li>
          <li><a href="/#tentang" data-nav="tentang" class="nav-link rounded-full px-4 py-2 hover:bg-forest/10 hover:text-forest transition-all">Tentang Kami</a></li>
          <li><a href="/#cara-kerja" data-nav="cara-kerja" class="nav-link rounded-full px-4 py-2 hover:bg-forest/10 hover:text-forest transition-all">Cara Kerja</a></li>
          <li><a href="/katalog" data-nav="katalog" class="nav-link rounded-full px-4 py-2 hover:bg-forest/10 hover:text-forest transition-all {{ request()->is('katalog*') ? 'bg-forest/10 text-forest font-black' : '' }}">Katalog Kriya</a></li>
          
          @if(session('user_id') && ($currentRole == 'user' || $currentRole == 'admin' || $currentRole == 'warga'))
            <li>
              <a href="/keranjang" data-nav="keranjang" class="nav-link rounded-full px-4 py-2 hover:bg-forest/10 hover:text-forest transition-all flex items-center gap-2 {{ request()->is('keranjang*') ? 'bg-forest/10 text-forest font-black' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                <span>Keranjang</span>
                @if($cartCount > 0)
                  <span class="badge badge-sm bg-terracotta border-none text-white font-extrabold px-1.5 py-2 font-mono rounded-md">
                    {{ $cartCount }}
                  </span>
                @endif
              </a>
            </li>
          @endif

          <div class="h-4 w-[1px] bg-ink/10 mx-2"></div>
          
          @if(!session('user_id'))
            <li><a href="/login" class="rounded-full px-4 py-2 text-maritime hover:bg-maritime-light font-bold {{ request()->is('login') ? 'bg-maritime-light text-maritime' : '' }}">Masuk</a></li>
            <li><a href="/register" class="btn btn-xs bg-gradient-to-r from-maritime to-maritime-dark hover:from-maritime-dark hover:to-maritime text-white border-none rounded-full px-4 normal-case font-bold shadow-sm shadow-maritime/20">Daftar</a></li>
          @endif

          @if(session('user_id'))
            <div class="dropdown dropdown-end ml-2">
              <button tabindex="0" class="btn btn-ghost btn-circle avatar online {{ $navUser && $navUser->foto_profil ? '' : 'placeholder' }}">
                <div class="bg-forest text-white rounded-full w-9 h-9 overflow-hidden flex items-center justify-center ring-2 ring-ink/5 {{ request()->is('dashboard*') || request()->is('profile*') ? 'ring-forest' : '' }}">
                  @if($navUser && $navUser->foto_profil)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($navUser->foto_profil) }}?v={{ time() }}" alt="Foto {{ session('name') }}" class="w-full h-full object-cover">
                  @else
                    <span class="text-xs font-bold font-display">{{ strtoupper(substr(session('name', 'A'), 0, 1)) }}</span>
                  @endif
                </div>
              </button>
              <ul tabindex="0" class="dropdown-content menu menu-sm mt-3 z-[60] p-2 shadow-xl bg-white rounded-2xl w-52 border border-ink/5 gap-1 normal-case">
                <div class="px-4 py-2 border-b border-ink/5 mb-1 text-left">
                  <p class="text-xs font-bold text-ink truncate">{{ session('name') }}</p>
                  <p class="text-[10px] text-ink-soft truncate font-medium uppercase tracking-wider">{{ $currentRole }}</p>
                </div>
                <li><a href="/dashboard" class="rounded-xl py-2 font-medium {{ request()->is('dashboard*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Dashboard</a></li>
                <li><a href="/profile" class="rounded-xl py-2 font-medium {{ request()->is('profile*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Profil Saya</a></li>
                <div class="my-1 border-t border-ink/5"></div>
                <li>
                  <form action="/logout" method="POST" class="p-0">
                    @csrf
                    <button type="submit" class="w-full text-left rounded-xl py-2 px-3 font-semibold text-terracotta hover:bg-terracotta-light">
                      Keluar Akun
                    </button>
                  </form>
                </li>
              </ul>
            </div>
          @endif

        </ul>
      </div>
      
      <div class="navbar-end lg:hidden">
        <div class="dropdown dropdown-end">
          
          <div tabindex="0" role="button" class="btn btn-ghost btn-sm btn-circle text-ink hover:bg-ink/5 active:scale-95 transition-all duration-200">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </div>

          <ul id="mobile-nav-menu" tabindex="0" class="dropdown-content menu menu-sm mt-3 z-[60] p-3 shadow-xl bg-white/95 backdrop-blur-lg rounded-2xl w-56 border border-ink/5 gap-1 normal-case">
            <div class="px-3 py-1 border-b border-ink/5 mb-1">
              <span class="text-[10px] font-extrabold uppercase tracking-wider text-ink/40">Navigasi</span>
            </div>
            <li><a href="/#beranda" data-nav="beranda" class="nav-link rounded-xl py-2 font-medium text-ink">Beranda</a></li>
            <li><a href="/#tentang" data-nav="tentang" class="nav-link rounded-xl py-2 font-medium text-ink">Tentang Kami</a></li>
            <li><a href="/#cara-kerja" data-nav="cara-kerja" class="nav-link rounded-xl py-2 font-medium text-ink">Cara Kerja</a></li>
            <li><a href="/katalog" data-nav="katalog" class="nav-link rounded-xl py-2 font-medium text-ink {{ request()->is('katalog*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Katalog Kriya</a></li>
            
            @if(session('user_id'))
              <div class="my-1 border-t border-ink/5"></div>
              
              <div class="flex items-center gap-3 px-3 py-2 bg-ink/[0.03] rounded-xl mb-1 text-left">
                <div class="avatar online shrink-0 {{ $navUser && $navUser->foto_profil ? '' : 'placeholder' }}">
                  <div class="bg-forest text-white rounded-full w-8 h-8 shadow-inner overflow-hidden flex items-center justify-center ring-1 ring-ink/5">
                    @if($navUser && $navUser->foto_profil)
                      <img src="{{ \Illuminate\Support\Facades\Storage::url($navUser->foto_profil) }}?v={{ time() }}" alt="Profil" class="w-full h-full object-cover" />
                    @else
                      <span class="text-[11px] font-bold font-display">{{ strtoupper(substr(session('name', 'A'), 0, 1)) }}</span>
                    @endif
                  </div>
                </div>
                <div class="truncate flex-1">
                  <p class="text-xs font-bold text-ink truncate leading-tight">{{ session('name', 'User') }}</p>
                  <span class="text-[9px] bg-forest/10 text-forest font-extrabold px-1.5 py-0.5 rounded mt-0.5 inline-block uppercase tracking-wider">
                    {{ $currentRole }}
                  </span>
                </div>
              </div>
              
              <div class="px-3 py-1 mb-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-forest">Akses Fitur</span>
              </div>
              
              @if($currentRole == 'user' || $currentRole == 'warga')
                <li><a href="/dashboard" class="rounded-xl py-2 font-medium text-ink {{ request()->is('dashboard') ? 'bg-forest/10 text-forest font-bold' : '' }}">Dashboard Saya</a></li>
                <li><a href="/keranjang" data-nav="keranjang" class="nav-link rounded-xl py-2 font-medium text-ink flex justify-between {{ request()->is('keranjang*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Keranjang Kriya @if($cartCount > 0) <span class="badge bg-terracotta text-white font-mono font-bold">{{ $cartCount }}</span> @endif</a></li>
                <li><a href="/setor-sampah" class="rounded-xl py-2 font-medium text-ink {{ request()->is('setor-sampah*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Setor Sampah</a></li>
              @elseif($currentRole == 'penjemput')
                <li><a href="/dashboard" class="rounded-xl py-2 font-medium text-ink {{ request()->is('dashboard*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Panel Penjemput</a></li>
              @elseif($currentRole == 'admin')
                <li><a href="/dashboard" class="rounded-xl py-2 font-medium text-ink {{ request()->is('dashboard*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Panel Admin Utama</a></li>
                <li><a href="/keranjang" data-nav="keranjang" class="nav-link rounded-xl py-2 font-medium text-ink flex justify-between {{ request()->is('keranjang*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Keranjang Kriya @if($cartCount > 0) <span class="badge bg-terracotta text-white font-mono font-bold">{{ $cartCount }}</span> @endif</a></li>
              @endif
              
              <li><a href="/profile" class="rounded-xl py-2 font-medium text-ink {{ request()->is('profile*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Profil Saya</a></li>
              
              <div class="my-1 border-t border-ink/5"></div>
              <li>
                <form action="/logout" method="POST" class="p-0 w-full">
                  @csrf
                  <button type="submit" class="w-full text-left text-terracotta font-semibold rounded-xl py-2 px-3 hover:bg-terracotta-light">
                    Keluar Akun
                  </button>
                </form>
              </li>
            @endif

            @if(!session('user_id'))
              <div class="my-1 border-t border-ink/5"></div>
              <li><a href="/login" class="text-maritime font-bold rounded-xl py-2 {{ request()->is('login') ? 'bg-maritime-light text-maritime' : '' }}">Masuk</a></li>
              <li><a href="/register" class="bg-gradient-to-r from-maritime to-maritime-dark text-white font-bold hover:from-maritime-dark hover:to-maritime rounded-xl text-center py-2 border-none shadow-sm shadow-maritime/20">Daftar</a></li>
            @endif
          </ul>

        </div>
      </div>

    </nav>
  </header>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Jalankan skrip ini hanya jika kita berada di halaman beranda utama (di mana ID section berada)
    const isHomePage = window.location.pathname === "/";

    if (isHomePage) {
      // Daftarkan section id yang ingin kita pantau posisinya di halaman beranda
      const sections = ["beranda", "tentang", "cara-kerja"];
      const elements = sections.map(id => document.getElementById(id)).filter(el => el !== null);

      const options = {
        root: null,
        rootMargin: "-50% 0px -50% 0px", // Memantau tepat saat section berada di tengah layar
        threshold: 0
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entries[0].isIntersecting) {
              const activeId = entries[0].target.id;
              activateNavbarLink(activeId);
          }
        });
      }, options);

      elements.forEach(el => observer.observe(el));
    }

    function activateNavbarLink(id) {
      // Bersihkan warna aktif dari semua link jangkar beranda lama
      document.querySelectorAll(".nav-link[data-nav]").forEach(link => {
        // Jangan ganggu warna menu Katalog dan Keranjang karena jalurnya berbeda halaman
        if (link.getAttribute("data-nav") !== "katalog" && link.getAttribute("data-nav") !== "keranjang") {
          link.classList.remove("bg-forest/10", "text-forest", "font-black");
        }
      });

      // Berikan warna aktif pada menu target yang sedang aktif di layar
      const activeLinks = document.querySelectorAll(`.nav-link[data-nav="${id}"]`);
      activeLinks.forEach(link => {
        link.classList.add("bg-forest/10", "text-forest");
      });
    }
  });
</script>