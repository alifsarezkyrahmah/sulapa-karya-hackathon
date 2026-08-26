<div class="w-full px-4 sm:px-6 lg:px-8 pt-4 sticky top-0 z-50">
  <header class="max-w-7xl mx-auto border border-ink/5 bg-white/30 backdrop-blur-xl rounded-full shadow-lg shadow-ink/[0.03] duration-300">
    <nav class="navbar px-6 py-2 min-h-[4rem]">

      <style>
        @media (min-width: 1441px) {
          .nav-center { 
            display: flex; 
            justify-content: flex-end; 
          }
        }
      </style>
      
      <div class="navbar-start">
        <a href="/" class="flex items-center gap-3 group transition-transform duration-200 active:scale-95 shrink-0">
          <img src="{{ asset('images/logo.png') }}" alt="Logo SulapaKarya" class="w-8 h-8 object-contain rounded-full shadow-md shadow-forest/20">
          <span class="font-display font-bold text-lg tracking-tight text-ink"> SulapaKarya </span>
        </a>
      </div>

      @php
        $navUser = session('user_id') ? \App\Models\User::find(session('user_id')) : null;
        $currentRole = $navUser->role ?? session('role', 'user');
        $cartCount = session('cart') ? collect(session('cart'))->sum('quantity') : 0;
        $notificationCount = session('user_id') ? 3 : 0;
      @endphp

      <div class="navbar-end w-full hidden lg:flex gap-4">
        <div class="flex-1 min-w-0 nav-center">
          <ul id="desktop-nav-menu" class="menu menu-horizontal flex flex-nowrap gap-0 px-0 text-xs font-bold uppercase tracking-wider text-ink/70 items-center">
            <li><a href="/#tentang-kami" data-nav="tentang-kami" class="nav-link rounded-full px-4 py-2 hover:bg-forest/10 hover:text-forest">Tentang Kami</a></li>
            <li><a href="/#cara-memilah" data-nav="cara-memilah" class="nav-link rounded-full px-4 py-2 hover:bg-forest/10 hover:text-forest">Cara Memilah</a></li>
            <li><a href="/#kalkulator" data-nav="kalkulator" class="nav-link rounded-full px-4 py-2 hover:bg-forest/10 hover:text-forest">Kalkulator</a></li>
            <li><a href="/katalog" data-nav="katalog" class="nav-link rounded-full px-4 py-2 hover:bg-forest/10 hover:text-forest {{ request()->is('katalog*') ? 'bg-forest/10 text-forest font-black' : '' }}">Katalog Kriya</a></li>
          </ul>
        </div>

        <!-- Right action group: notif, cart, profile (shrink-0) -->
        <div class="flex items-center gap-1 shrink-0">
          @if(session('user_id') && ($currentRole == 'user' || $currentRole == 'warga'))
            <div class="h-4 w-[1px] bg-ink/10 mx-0.5"></div>
            <div id="notif-dropdown-wrapper" class="relative mr-1 group">
              <div class="dropdown dropdown-end">
                <button id="notif-btn" aria-label="Notifikasi" aria-expanded="false" class="btn btn-ghost btn-circle relative">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><path d="M18 8a6 6 0 10-12 0v5l-2 2h16l-2-2z"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                </button>
                <ul id="notif-dropdown" tabindex="0" class="dropdown-content menu p-3 shadow-lg bg-white rounded-xl w-72 border border-ink/5 absolute right-0 mt-2 hidden group-hover:block group-focus-within:block">
                  <li class="menu-title text-lg px-0 text-ink font-bold text-base">Notifikasi</li>                  <p class="text-sm text-ink-soft">Tidak ada notifikasi baru</p>
                  <a href="/notifikasi" class="btn btn-sm w-full bg-forest text-white mt-2">Lihat Semua Notifikasi</a>
                </ul>
              </div>
            </div>

            <!-- Cart wrapper -->
            <div id="cart-dropdown-wrapper" class="relative group">
              <div class="dropdown dropdown-end">
                @php
                 $cartItems = session('cart', []);
                 $totalJenisProduk = count($cartItems);
                @endphp
                <button id="cart-btn" aria-label="Keranjang" aria-expanded="false" class="btn btn-ghost btn-circle relative focus:outline-none">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                  @if($totalJenisProduk > 0)
                    <span class="badge badge-sm bg-terracotta border-none text-white font-extrabold absolute -top-1 -right-1">{{ $totalJenisProduk }}</span>
                  @endif
                </button>
                
                <div tabindex="0" class="dropdown-content menu p-4 shadow-xl bg-white rounded-2xl w-80 border border-ink/5 absolute right-0 mt-2 hidden group-hover:block group-focus-within:block z-50">
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
            </div>
          @endif

          @if(!session('user_id'))
            <div class="flex items-center"><a href="/login" class="rounded-full px-4 py-2 text-forest hover:bg-forest/10 font-bold {{ request()->is('login') ? 'bg-forest/10 text-forest' : '' }}">Masuk</a></div>
            <div class="flex items-center"><a href="/register" class="btn btn-xs bg-forest hover:bg-forest/90 text-white border-none rounded-full px-4 font-bold shadow-sm shadow-forest/20">Daftar</a></div>
          @endif

          @if(session('user_id'))
            <div class="dropdown dropdown-end ml-2">
              <button tabindex="0" class="btn btn-ghost btn-circle avatar {{ $navUser && $navUser->foto_profil ? '' : 'placeholder' }}">
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
        </div>
      </div>

      <!-- MOBILE BURGER MENU (tampil di bawah 1024px) -->
      <div class="navbar-end lg:hidden">
        <div class="dropdown dropdown-end">
          <div tabindex="0" role="button" aria-controls="mobile-nav-menu" aria-expanded="false" class="btn btn-ghost btn-sm btn-circle text-ink hover:bg-ink/5 active:scale-95 duration-200">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </div>

          <ul id="mobile-nav-menu" tabindex="0" class="dropdown-content menu menu-sm mt-3 z-[60] p-3 shadow-xl bg-white/95 backdrop-blur-lg rounded-2xl w-56 border border-ink/5 gap-1 normal-case">
            <li><a href="/#beranda" data-nav="beranda" class="nav-link rounded-xl py-2 font-medium text-ink">Beranda</a></li>
            <li><a href="/#latar-belakang" data-nav="latar-belakang" class="nav-link rounded-xl py-2 font-medium text-ink">Latar Belakang</a></li>
            <li><a href="/#tujuan-utama" data-nav="tujuan-utama" class="nav-link rounded-xl py-2 font-medium text-ink">Tujuan Utama</a></li>
            <li><a href="/#cara-kerja" data-nav="cara-kerja" class="nav-link rounded-xl py-2 font-medium text-ink">Cara Kerja</a></li>
            <li><a href="/#impact-tracker" data-nav="impact-tracker" class="nav-link rounded-xl py-2 font-medium text-ink">Dampak</a></li>
            <li><a href="/#jenis-sampah" data-nav="jenis-sampah" class="nav-link rounded-xl py-2 font-medium text-ink">Jenis Sampah</a></li>
            <li><a href="/#kalkulator" data-nav="kalkulator" class="nav-link rounded-xl py-2 font-medium text-ink">Kalkulator</a></li>
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
              
              @if($currentRole == 'user' || $currentRole == 'warga')
                <li><a href="/dashboard" class="rounded-xl py-2 font-medium text-ink {{ request()->is('dashboard') ? 'bg-forest/10 text-forest font-bold' : '' }}">Dashboard Saya</a></li>
                <li><a href="/keranjang" data-nav="keranjang" class="nav-link rounded-xl py-2 font-medium text-ink flex justify-between {{ request()->is('keranjang*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Keranjang Kriya @if($cartCount > 0) <span class="badge bg-terracotta text-white font-mono font-bold">{{ $cartCount }}</span> @endif</a></li>
                <li><a href="/setor-sampah" class="rounded-xl py-2 font-medium text-ink {{ request()->is('setor-sampah*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Setor Sampah</a></li>
              @elseif($currentRole == 'penjemput')
                <li><a href="/dashboard" class="rounded-xl py-2 font-medium text-ink {{ request()->is('dashboard*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Dashboard Kurir</a></li>
              @elseif($currentRole == 'admin')
                <li><a href="/dashboard" class="rounded-xl py-2 font-medium text-ink {{ request()->is('dashboard*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Dashboard Admin</a></li>
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
    const isHomePage = window.location.pathname === "/";

    if (isHomePage) {
      const sections = ["beranda","latar-belakang","tujuan-utama","cara-kerja","impact-tracker","jenis-sampah","kalkulator","katalog"];
      const elements = sections.map(id => document.getElementById(id)).filter(el => el !== null);

      const options = {
        root: null,
        rootMargin: "-15% 0px -40% 0px",
        threshold: 0
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
              const activeId = entry.target.id;
              activateNavbarLink(activeId);
          }
        });
      }, options);

      elements.forEach(el => observer.observe(el));
    }

    function setupHoverDropdown(wrapperId) {
      const wrapper = document.getElementById(wrapperId);
      if (!wrapper) return;
      const btn = wrapper.querySelector('button, a');
      wrapper.addEventListener('mouseenter', () => { wrapper.classList.add('dropdown-open'); if (btn) btn.setAttribute('aria-expanded', 'true'); });
      wrapper.addEventListener('mouseleave', () => { wrapper.classList.remove('dropdown-open'); if (btn) btn.setAttribute('aria-expanded', 'false'); });
      wrapper.addEventListener('focusin', () => { wrapper.classList.add('dropdown-open'); if (btn) btn.setAttribute('aria-expanded', 'true'); });
      wrapper.addEventListener('focusout', () => { wrapper.classList.remove('dropdown-open'); if (btn) btn.setAttribute('aria-expanded', 'false'); });
    }

    setupHoverDropdown('notif-dropdown-wrapper');
    setupHoverDropdown('cart-dropdown-wrapper');

    function activateNavbarLink(id) {
      document.querySelectorAll(".nav-link[data-nav]").forEach(link => {
        if (link.getAttribute("data-nav") !== "katalog" && link.getAttribute("data-nav") !== "keranjang") {
          link.classList.remove("bg-forest/10", "text-forest", "font-black");
        }
      });

      const activeLinks = document.querySelectorAll(`.nav-link[data-nav="${id}"]`);
      activeLinks.forEach(link => {
        link.classList.add("bg-forest/10", "text-forest");
      });
    }
  });
</script>