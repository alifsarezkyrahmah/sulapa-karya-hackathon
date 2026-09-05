<div class="w-full px-2 sm:px-6 lg:px-8 pt-4 sticky top-0 z-50">
  <header class="max-w-7xl mx-auto border border-ink/5 bg-white/30 backdrop-blur-xl rounded-full shadow-lg shadow-ink/[0.03] duration-300">
    <nav class="navbar px-6 py-2 min-h-[4rem] flex items-center justify-between">
      
      <!-- LOGO (KIRI) -->
      <div class="navbar-start w-auto shrink-0">
        <a href="/" class="flex items-center gap-3 group transition-transform duration-200 active:scale-95 shrink-0">
          <img src="{{ asset('images/logo.png') }}" alt="Logo SulapaKarya" class="w-8 h-8 object-contain rounded-full shadow-md shadow-forest/20">
          <span class="font-display font-bold text-2xl tracking-tight text-ink"> SulapaKarya </span>
        </a>
      </div>

      @php
        $navUser = session('user_id') ? \App\Models\User::find(session('user_id')) : null;
        $currentRole = $navUser->role ?? session('role', 'user');
        $cartCount = session('cart') ? collect(session('cart'))->sum('quantity') : 0;
        $notificationCount = session('user_id') ? 3 : 0;
      @endphp

      <!-- DESKTOP NAVBAR (TENGAH & KANAN) -->
      <div class="hidden lg:flex items-center justify-end gap-2 flex-1 min-w-0">
        <!-- MENU (TENGAH) -->
        <div class="flex-1 flex justify-center min-w-0">
          <ul id="desktop-nav-menu" class="menu menu-horizontal flex flex-nowrap gap-0.5 px-0 text-[11px] font-bold uppercase tracking-wider text-ink/70 items-center">
            <li><a href="/" data-nav="beranda" class="nav-link rounded-full px-2 py-1.5 hover:bg-forest/10 hover:text-forest">Beranda</a></li>
            <li><a href="/#tentang-kami" data-nav="tentang-kami" class="nav-link rounded-full px-2 py-1.5 hover:bg-forest/10 hover:text-forest">Tentang Kami</a></li>
            <li class="dropdown dropdown-hover">
              <a href="/#cara-memilah" data-nav="cara-memilah" class="nav-link rounded-full px-2 py-1.5 hover:bg-forest/10 hover:text-forest {{ request()->is('cara-memilah*') ? 'bg-forest/10 text-forest font-black' : '' }}">Cara Memilah</a>

              <ul tabindex="0" class="dropdown-content menu p-2 shadow-xl bg-cream rounded-2xl w-56 border border-ink/5 z-50 normal-case">
                <li><a href="/cara-memilah#gelas-plastik" class="rounded-xl py-2 font-medium text-ink">Gelas Plastik</a></li>
                <li><a href="/cara-memilah#botol-plastik" class="rounded-xl py-2 font-medium text-ink">Botol Plastik</a></li>
                <li><a href="/cara-memilah#kertas-hvs" class="rounded-xl py-2 font-medium text-ink">Kertas HVS/Buku Bekas</a></li>
                <li><a href="/cara-memilah#kertas-koran" class="rounded-xl py-2 font-medium text-ink">Kertas Koran</a></li>
                <li><a href="/cara-memilah#kain-perca" class="rounded-xl py-2 font-medium text-ink">Kain Perca</a></li>
                <li><a href="/cara-memilah#plastik-kresek" class="rounded-xl py-2 font-medium text-ink">Plastik Kresek</a></li>
                <li><a href="/cara-memilah#kaleng-besi" class="rounded-xl py-2 font-medium text-ink">Kaleng Besi/Seng</a></li>
                <li><a href="/cara-memilah#botol-kaca" class="rounded-xl py-2 font-medium text-ink">Botol Kaca</a></li>
                <li><a href="/cara-memilah#logam-tembaga" class="rounded-xl py-2 font-medium text-ink">Logam Tembaga</a></li>
                <li><a href="/cara-memilah#besi-tua" class="rounded-xl py-2 font-medium text-ink">Besi Tua/Padat</a></li>
                <li><a href="/cara-memilah#elektronik-bekas" class="rounded-xl py-2 font-medium text-ink">Elektronik Bekas</a></li>
                <li><a href="/cara-memilah#karton-makanan" class="rounded-xl py-2 font-medium text-ink">Karton Makanan</a></li>
              </ul>
            </li>  
            <li><a href="/#kalkulator" data-nav="kalkulator" class="nav-link rounded-full px-2 py-1.5 hover:bg-forest/10 hover:text-forest">Kalkulator</a></li>
            <li><a href="/katalog" data-nav="katalog" class="nav-link rounded-full px-2 py-1.5 hover:bg-forest/10 hover:text-forest {{ request()->is('katalog*') ? 'bg-forest/10 text-forest font-black' : '' }}">Katalog Kriya</a></li>
          </ul>
        </div>

        <!-- Right action group: notif, cart, profile (shrink-0) -->
        <div class="flex items-center gap-1 shrink-0">
          @if(session('user_id'))
            <div class="dropdown dropdown-end dropdown-hover">
              <a href="/notifikasi" id="notif-btn" aria-label="Notifikasi" class="btn btn-ghost btn-circle relative">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><path d="M18 8a6 6 0 10-12 0v5l-2 2h16l-2-2z"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
              </a>
              <div tabindex="0" class="dropdown-content p-4 shadow-xl bg-white rounded-2xl w-80 border border-ink/5 z-50">
                <div class="flex items-center justify-between pb-2 border-b border-ink/5 mb-2">
                  <span class="text-ink font-bold text-sm">Notifikasi</span>
                </div>
                
                <div class="py-6 text-center">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto text-ink-soft/40 mb-2"><path d="M18 8a6 6 0 10-12 0v5l-2 2h16l-2-2z"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                  <p class="text-xs text-ink-soft font-medium">Tidak ada notifikasi baru</p>
                </div>
                
                <div class="mt-3 pt-2 border-t border-ink/5">
                  <a href="/notifikasi" class="btn btn-sm w-full bg-forest hover:bg-forest/90 text-white normal-case font-bold border-none rounded-xl">Lihat Semua Notifikasi</a>
                </div>
              </div>
            </div>
          @endif
          
          @if(session('user_id') && $currentRole === 'user')
            <!-- Cart wrapper -->
            <div class="dropdown dropdown-end dropdown-hover">
              @php
              $cartItems = session('cart', []);
              $totalJenisProduk = count($cartItems);
              @endphp
              <a href="/keranjang" id="cart-btn" aria-label="Keranjang" class="btn btn-ghost btn-circle relative focus:outline-none">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                @if($totalJenisProduk > 0)
                  <span class="badge badge-sm bg-terracotta border-none text-white font-extrabold absolute -top-1 -right-1">{{ $totalJenisProduk }}</span>
                @endif
              </a>
              
              <div tabindex="0" class="dropdown-content menu p-4 shadow-xl bg-white rounded-2xl w-80 border border-ink/5 z-50">
                <div class="flex items-center justify-between pb-2 border-b border-ink/5 mb-2">
                  <span class="text-ink font-bold text-sm">Keranjang</span>
                  <span class="text-xs text-ink-soft font-medium">Baru Ditambahkan</span>
                </div>

                @php
                  $cartItems = session('cart', []);
                @endphp

                @if(count($cartItems) > 0)
                  <div class="max-h-60 overflow-y-auto flex flex-col gap-2 pr-1 custom-scrollbar">
                    @foreach(array_slice($cartItems, 0, 5, true) as $id => $item)
                      @php
                        $productId = $item['id'] ?? $id;
                      @endphp

                      @if(request()->is('katalog*'))
                        <button type="button" onclick="openProductModal('{{ $productId }}')" class="w-full flex items-center justify-between p-2 hover:bg-ink/[0.04] rounded-xl transition-colors group/item text-left">
                      @else
                        <a href="/katalog?open_modal={{ $productId }}" class="flex items-center justify-between p-2 hover:bg-ink/[0.04] rounded-xl transition-colors group/item">
                      @endif
                        <div class="flex items-center gap-3 p-1.5 hover:bg-ink/[0.02] rounded-xl transition-colors">
                          <img src="{{ isset($item['foto']) ? asset('storage/'.$item['foto']) : 'https://placehold.co/100' }}" 
                              alt="{{ $item['name'] ?? $item['nama'] ?? 'Produk' }}" 
                              class="w-10 h-10 object-cover rounded-lg border border-ink/5 shrink-0">
                          
                          <p class="text-xs font-semibold text-ink truncate">{{ $item['name'] ?? $item['nama'] ?? 'Produk Kriya' }}</p>
                        </div>
                          <div class="text-right shrink-0">
                            <span class="text-xs font-bold text-forest">Rp {{ number_format($item['price'] ?? $item['harga'] ?? 0, 0, ',', '.') }}</span>
                          </div>
                      @if(request()->is('katalog*'))
                        </button>
                      @else
                        </a>
                      @endif
                    @endforeach
                  </div>

                  <div class="mt-3 pt-2 border-t border-ink/5">
                    <a href="/keranjang" class="btn btn-sm w-full bg-forest hover:bg-forest/90 text-white normal-case font-bold border-none rounded-xl">Lihat Keranjang</a>
                  </div>
                @else
                  <div class="py-6 text-center">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto text-ink-soft/40 mb-2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <p class="text-xs text-ink-soft font-medium">Keranjang belanjamu masih kosong!</p>
                  </div>
                  <div class="mt-2">
                    <a href="/katalog" class="btn btn-sm w-full border border-forest/20 text-forest hover:bg-forest/10 normal-case font-bold rounded-xl">Mulai Belanja</a>
                  </div>
                @endif
              </div>
            </div>
          @endif

          @if(!session('user_id'))
            <div class="flex items-center"><a href="/login" class="rounded-full px-4 py-2 text-forest justify-between hover:bg-forest/10 font-bold {{ request()->is('login') ? 'bg-forest/10 text-forest' : '' }}">Masuk</a></div>
            <div class="flex items-center"><a href="/register" class="rounded-full px-4 py-2 bg-forest text-white justify-between hover:bg-forest/90 border-none rounded-full px-2 font-bold shadow-sm shadow-forest/20">Daftar</a></div>
          @endif

          @if(session('user_id'))
            <div class="dropdown dropdown-end dropdown-hover ml-2">
              @php
                $dashboardUrl = match($navUser->role ?? $currentRole) {
                  'user' => '/user/dashboard',
                  'admin' => '/dashboard-admin',
                  'penjemput' => '/penjemput/dashboard',
                  default => '/dashboard'
                };
              @endphp
              <a href="{{ $dashboardUrl }}" tabindex="0" class="btn btn-ghost btn-circle avatar {{ $navUser && $navUser->foto_profil ? '' : 'placeholder' }}">
                <div class="bg-forest text-white rounded-full w-9 h-9 overflow-hidden flex items-center justify-center ring-2 ring-ink/5 {{ request()->is('dashboard*') || request()->is('profile*') ? 'ring-forest' : '' }}">
                  @if($navUser && $navUser->foto_profil)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($navUser->foto_profil) }}?v={{ time() }}" alt="Foto {{ session('name') }}" class="w-full h-full object-cover">
                  @else
                    <span class="text-xs font-bold font-display">{{ strtoupper(substr(session('name', 'A'), 0, 1)) }}</span>
                  @endif
                </div>
              </a>
              <ul tabindex="0" class="dropdown-content menu menu-sm z-50 p-2 shadow-xl bg-white rounded-2xl w-52 border border-ink/5 gap-1 normal-case">
                <div class="px-3 py-2 border-b border-ink/5 mb-1 text-left">
                  <p class="text-sm font-bold text-ink truncate leading-tight">{{ session('name') }}</p>
                  @if($navUser && $navUser->role === 'user')
                    <p class="text-xs text-forest font-semibold mt-0.5 flex items-center gap-1">
                      <img src="{{ asset('images/sulapapoin.png') }}" alt="Poin" class="w-3.5 h-3.5 object-contain shrink-0">
                      <span>{{ number_format($navUser->points_balance ?? 0, 0, ',', '.') }} Poin</span>
                    </p>
                  @else
                    <p class="text-xs text-forest font-semibold uppercase tracking-wider mt-0.5">{{ $currentRole }}</p>
                  @endif
                </div>
                @if($navUser && $navUser->role === 'user')
                  <li><a href="/setor-sampah" class="rounded-xl py-2 font-medium {{ request()->is('setor-sampah*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Setor Sampah</a></li>
                  <div class="my-1 border-t border-ink/5"></div>
                @endif
                <li><a href="{{ $dashboardUrl }}" class="rounded-xl py-2 font-medium {{ request()->is('dashboard*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Dashboard</a></li>
                @if($navUser && $navUser->role === 'user')
                  <li><a href="/riwayat-setoran" class="rounded-xl py-2 font-medium {{ request()->is('riwayat-setoran*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Riwayat Setoran</a></li>
                  <li><a href="/riwayat-pembelian" class="rounded-xl py-2 font-medium {{ request()->is('riwayat-pembelian*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Riwayat Pembelian</a></li>
                @endif
                <div class="my-1 border-t border-ink/5"></div>
                <li><a href="/profile" class="rounded-xl py-2 font-medium {{ request()->is('profile*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Pengaturan Profil</a></li>
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
        </div>
      </div>

      <!-- MOBILE NAVBAR (tampil di bawah lg) -->
      <div class="navbar-end lg:hidden">
        <div class="dropdown dropdown-end">
          <div tabindex="0" role="button" aria-controls="mobile-nav-menu" aria-expanded="false" class="btn btn-ghost btn-sm btn-circle text-ink hover:bg-ink/5 active:scale-95 duration-200">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </div>

          <!-- Gantilah div id="mobile-nav-menu" beserta isinya menjadi seperti ini -->
          <div id="mobile-nav-menu" tabindex="0" class="dropdown-content mt-3 z-[60] shadow-xl bg-white/95 backdrop-blur-lg rounded-2xl w-60 border border-ink/5 normal-case flex flex-col max-h-[80vh] overflow-hidden">
            
            <!-- TAMBAHKAN 'overflow-y-auto' DI SINI agar scroll berjalan ke bawah -->
            <ul class="menu menu-sm p-3 gap-1 overflow-y-auto flex-1 flex flex-col flex-nowrap">
              
              <!-- Profile Section (PALING ATAS) -->
              @if(session('user_id'))
                <div class="flex items-center gap-3 px-3 py-3 bg-forest/5 rounded-xl mb-2 text-left border border-forest/10 shrink-0">
                  <div class="avatar shrink-0 {{ $navUser && $navUser->foto_profil ? '' : 'placeholder' }}">
                    <div class="bg-forest text-white rounded-full w-10 h-10 overflow-hidden flex items-center justify-center ring-2 ring-forest/20">
                      @if($navUser && $navUser->foto_profil)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($navUser->foto_profil) }}?v={{ time() }}" alt="Profil" class="w-full h-full object-cover" />
                      @else
                        <span class="text-sm font-bold font-display">{{ strtoupper(substr(session('name', 'A'), 0, 1)) }}</span>
                      @endif
                    </div>
                  </div>
                  <div class="truncate flex-1">
                    <p class="text-sm font-bold text-ink truncate leading-tight">{{ session('name', 'User') }}</p>
                    @if($navUser && $navUser->role === 'user')
                      <p class="text-xs text-forest font-semibold flex items-center gap-1 mt-0.5">
                        <img src="{{ asset('images/sulapapoin.png') }}" alt="Poin" class="w-3.5 h-3.5 object-contain shrink-0">
                        <span>{{ number_format($navUser->points_balance ?? 0, 0, ',', '.') }} Poin</span>
                      </p>
                    @else
                      <span class="text-xs bg-forest text-white font-bold px-1.5 py-0.5 rounded-full inline-block uppercase tracking-wider">
                        {{ $currentRole }}
                      </span>
                    @endif
                  </div>
                </div>
                <div class="my-2 border-t border-ink/5 shrink-0"></div>
              @endif
              
              <!-- Menu Navigasi (Sisa menu ke bawah...) -->
              <li><a href="/" data-nav="beranda" class="nav-link rounded-xl py-2 font-medium text-ink {{ request()->is('/') ? 'bg-forest/10 text-forest font-bold' : '' }}">Beranda</a></li>
              <li><a href="/#tentang-kami" data-nav="tentang-kami" class="nav-link rounded-xl py-2 font-medium text-ink">Tentang Kami</a></li>
              <li><a href="/#cara-memilah" data-nav="cara-memilah" class="nav-link rounded-xl py-2 font-medium text-ink">Cara Memilah</a></li>
              <li><a href="/#kalkulator" data-nav="kalkulator" class="nav-link rounded-xl py-2 font-medium text-ink">Kalkulator</a></li>
              @if(session('user_id') && $currentRole === 'user')
                <li><a href="/katalog" data-nav="katalog" class="nav-link rounded-xl py-2 font-medium text-ink {{ request()->is('katalog*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Katalog Kriya</a></li>
                <li><a href="/setor-sampah" data-nav="setor-sampah" class="nav-link rounded-xl py-2 font-medium text-ink {{ request()->is('setor-sampah*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Setor Sampah</a></li>
              @endif
              <div class="my-2 border-t border-ink/5 shrink-0"></div>

              <!-- Notifikasi & Keranjang -->
              @if(session('user_id'))
                @php
                  $dashboardUrl = match($navUser->role ?? $currentRole) {
                    'user' => '/user/dashboard',
                    'admin' => '/admin/dashboard',
                    'penjemput' => '/penjemput/dashboard',
                    default => '/dashboard'
                  };
                @endphp
                
                <li><a href="{{ $dashboardUrl }}" class="rounded-xl py-2 font-medium text-ink {{ request()->is('dashboard*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Dashboard</a></li>
                <li><a href="/notifikasi" class="rounded-xl py-2 font-medium text-ink {{ request()->is('notifikasi*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Notifikasi</a></li>
                
                @if($currentRole === 'user')
                  <li><a href="/keranjang" class="rounded-xl py-2 font-medium text-ink flex justify-between items-center {{ request()->is('keranjang*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Keranjang @if($cartCount > 0)<span class="badge badge-sm bg-terracotta text-white font-mono font-bold">{{ $cartCount }}</span>@endif</a></li>
                  <li><a href="/riwayat-pembelian" class="rounded-xl py-2 font-medium text-ink {{ request()->is('riwayat-pembelian*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Riwayat Pembelian</a></li>
                  <li><a href="/riwayat-setoran" class="rounded-xl py-2 font-medium text-ink {{ request()->is('riwayat-setoran*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Riwayat Setoran</a></li>
                @endif
                
                <div class="my-2 border-t border-ink/5 shrink-0"></div>
                <li><a href="/profile" class="rounded-xl py-2 font-medium text-ink {{ request()->is('profile*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Pengaturan Profil</a></li>
                
                <!-- LOGOUT PALING BAWAH -->
                <li class="shrink-0">
                  <form action="/logout" method="POST" class="w-full p-0">
                    @csrf
                      <button type="submit" class="w-full text-left rounded-xl py-3 px-3 semibold text-terracotta hover:bg-terracotta/10 transition-colors">Keluar Akun</button>
                  </form>
                </li>
              @else
                <li><a href="/login" class="text-forest font-bold rounded-xl py-2 text-center {{ request()->is('login') ? 'bg-forest/10' : '' }}">Masuk</a></li>
                <li><a href="/register" class="bg-forest text-white font-bold hover:bg-forest/90 rounded-xl text-center py-2 border-none">Daftar</a></li>
              @endif
            </ul>
          </div>
        </div>
      </div>

    </nav>
  </header>
</div>

<script>
  function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  document.addEventListener("DOMContentLoaded", function () {
    const isHomePage = window.location.pathname === "/";

    if (isHomePage) {
      const sections = ["beranda", "tentang-kami", "cara-memilah", "kalkulator", "katalog"];
      const elements = sections.map(id => document.getElementById(id)).filter(el => el !== null);

      const options = {
        root: null,
        rootMargin: "-10% 0px -30% 0px",
        threshold: 0.1
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting && window.scrollY > 100) {
            activateNavbarLink(entry.target.id);
          }
        });
      }, options);

      elements.forEach(el => observer.observe(el));

      function checkTopScroll() {
        if (window.scrollY < 100) {
          activateNavbarLink("beranda");
        }
      }

      window.addEventListener("scroll", checkTopScroll);
      checkTopScroll();
    }

    function activateNavbarLink(id) {
      document.querySelectorAll(".nav-link[data-nav]").forEach(link => {
        link.classList.remove("bg-forest/10", "text-forest", "font-black", "font-bold");
      });

      const activeLinks = document.querySelectorAll(`.nav-link[data-nav="${id}"]`);
      activeLinks.forEach(link => {
        link.classList.add("bg-forest/10", "text-forest", "font-bold");
      });
    }
  });
</script>