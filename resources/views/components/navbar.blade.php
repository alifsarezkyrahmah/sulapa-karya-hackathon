<div class="w-full px-2 sm:px-6 lg:px-8 pt-4 sticky top-0 z-50">
  <!-- Container Glassmorphism Utama -->
  <header class="max-w-7xl mx-auto border border-white/40 bg-white/25 backdrop-blur-md shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] rounded-full transition-all duration-300">
    <nav class="navbar bg-transparent px-4 sm:px-6 py-2 min-h-[4rem] flex justify-between items-center">

      <style>
        .nav-link.active-nav {
          background-color: rgba(46, 125, 50, 0.12) !important;
          color: #2E7D32 !important;
          font-weight: 800 !important;
        }
      </style>
      
      <!-- Brand Logo -->
      <div class="navbar-start w-auto shrink-0">
        <a href="/" class="flex items-center gap-3 group transition-transform duration-200 active:scale-95 shrink-0">
          <img src="{{ asset('images/logo.png') }}" alt="Logo SulapaKarya" class="w-8 h-8 object-contain rounded-full shadow-md shadow-forest/20">
          <span class="font-display font-black text-xl tracking-tight text-ink">SulapaKarya</span>
        </a>
      </div>

      @php
        $currentUserId = session('user_id') ?? (auth()->check() ? auth()->id() : null);
        $navUser = $currentUserId ? \App\Models\User::find($currentUserId) : null;
        $currentRole = $navUser->role ?? session('role', 'user');

        $dashboardUrl = match($navUser->role ?? $currentRole) {
          'user' => '/user/dashboard',
          'admin' => '/admin/dashboard',
          'penjemput' => '/penjemput/dashboard',
          default => '/dashboard',
        };

        $isPro = $navUser ? ($navUser->is_pro ?? (($navUser->business_status ?? '') === 'approved')) : false;
        $cartItems = session('cart', []);
        $cartCount = count($cartItems);

        $navNotifications = ($currentUserId && \Illuminate\Support\Facades\Schema::hasTable('notifications'))
            ? \App\Models\Notification::where('user_id', $currentUserId)->orderBy('created_at', 'desc')->take(5)->get()
            : collect([]);
            
        $unreadCount = ($currentUserId && \Illuminate\Support\Facades\Schema::hasTable('notifications'))
            ? \App\Models\Notification::where('user_id', $currentUserId)->where('is_read', false)->count()
            : 0;
      @endphp

      <!-- Desktop Menu (Laptop / PC) -->
      <div class="hidden lg:flex items-center justify-end gap-2 flex-1 min-w-0">
        <div class="flex-1 flex justify-center min-w-0">
          <ul id="desktop-nav-menu" class="menu menu-horizontal flex flex-nowrap gap-1 px-0 text-[11px] font-bold uppercase tracking-wider text-ink/70 items-center">
            <li><a href="/" data-nav="beranda" class="nav-link rounded-full px-3 py-1.5 hover:bg-forest/10 hover:text-forest transition-colors">Beranda</a></li>
            <li><a href="/#tentang-kami" data-nav="tentang-kami" class="nav-link rounded-full px-3 py-1.5 hover:bg-forest/10 hover:text-forest transition-colors">Tentang Kami</a></li>
            <li><a href="/#kalkulator" data-nav="kalkulator" class="nav-link rounded-full px-3 py-1.5 hover:bg-forest/10 hover:text-forest transition-colors">Kalkulator Poin</a></li>
            <li><a href="/katalog" data-nav="katalog" class="nav-link rounded-full px-3 py-1.5 hover:bg-forest/10 hover:text-forest transition-all {{ request()->is('katalog*') ? 'active-nav' : '' }}">Katalog Kriya</a></li>
            <li><a href="/#mitra-bisnis" data-nav="mitra-bisnis" class="nav-link rounded-full px-3 py-1.5 hover:bg-forest/10 hover:text-forest transition-all {{ request()->is('mitra-bisnis*') ? 'active-nav' : '' }}">SulapaKarya PRO</a></li>
          </ul>
        </div>
      </div>

      <!-- Action Group Kanan (Desktop Only) -->
      <div class="navbar-end w-auto hidden lg:flex items-center gap-2 shrink-0">
        @if($currentUserId)
          <!-- 1. Keranjang Desktop -->
          @if($currentRole == 'user')
            <a href="/keranjang" id="cart-btn" aria-label="Keranjang" class="btn btn-ghost btn-circle relative text-ink hover:bg-white/40 focus:outline-none">
              <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
              </svg>
              @if($cartCount > 0)
                <span class="badge badge-sm bg-forest border-none text-white font-extrabold absolute -top-1 -right-1 font-mono">{{ $cartCount }}</span>
              @endif
            </a>
          @endif

          <!-- 2. Notifikasi Dropdown Desktop -->
          <div class="dropdown dropdown-end relative">
            <button id="notif-btn" aria-label="Notifikasi" tabindex="0" class="btn btn-ghost btn-circle relative text-ink hover:bg-white/40 focus:outline-none">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0">
                <path d="M18 8a6 6 0 10-12 0v5l-2 2h16l-2-2z"/>
                <path d="M13.73 21a2 2 0 01-3.46 0"/>
              </svg>
              @if($unreadCount > 0)
                <span class="badge badge-xs bg-forest text-white font-extrabold absolute top-2 right-2 p-1 animate-pulse"></span>
              @endif
            </button>
            
            <div tabindex="0" class="dropdown-content z-[70] mt-3 p-4 shadow-2xl bg-white/95 backdrop-blur-xl rounded-2xl w-80 sm:w-88 border border-white/60 text-left overflow-hidden right-0 flex flex-col">
              <div class="flex items-center justify-between pb-2 border-b border-ink/5 mb-2">
                <div class="flex items-center gap-2">
                  <span class="text-ink font-bold text-sm">Notifikasi</span>
                  @if($unreadCount > 0)
                    <span class="text-[10px] text-forest font-bold bg-forest/10 px-2 py-0.5 rounded-full font-mono">{{ $unreadCount }} Baru</span>
                  @endif
                </div>
                @if($unreadCount > 0)
                  <form action="{{ route('notifikasi.markAll') }}" method="POST" class="m-0 p-0">
                    @csrf
                    <button type="submit" class="text-[10px] font-semibold text-ink-soft hover:text-forest transition-colors">Tandai dibaca</button>
                  </form>
                @endif
              </div>

              <div class="divide-y divide-ink/5 max-h-72 overflow-y-auto custom-scrollbar">
                @forelse($navNotifications as $notif)
                  <a href="{{ route('notifikasi.read', $notif->id) }}" class="block px-3.5 py-3 hover:bg-forest/5 transition-colors {{ !$notif->is_read ? 'bg-forest/[0.04]' : '' }}">
                    <div class="flex items-start gap-3">
                      <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 text-xs mt-0.5
                        {{ $notif->type === 'payout' ? 'bg-amber-100 text-amber-800' : '' }}
                        {{ $notif->type === 'deposit' ? 'bg-forest/15 text-forest' : '' }}
                        {{ $notif->type === 'order' ? 'bg-maritime/15 text-maritime' : '' }}
                        {{ $notif->type === 'warning' ? 'bg-terracotta/15 text-terracotta' : '' }}
                        {{ !in_array($notif->type, ['payout','deposit','order','warning']) ? 'bg-cream text-ink' : '' }}">
                        @if($notif->type === 'payout') 💰 @elseif($notif->type === 'deposit') ♻️ @elseif($notif->type === 'order') 🛍️ @elseif($notif->type === 'warning') ⚠️ @else ✨ @endif
                      </div>
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1">
                          <p class="font-bold text-xs text-ink truncate {{ !$notif->is_read ? 'text-forest' : '' }}">{{ $notif->title }}</p>
                          @if(!$notif->is_read) <span class="w-1.5 h-1.5 rounded-full bg-forest shrink-0"></span> @endif
                        </div>
                        <p class="text-[11px] text-ink-soft leading-relaxed mt-0.5 line-clamp-2">{{ $notif->message }}</p>
                        <span class="text-[9px] text-ink-soft/60 font-mono block mt-1">{{ $notif->created_at->diffForHumans() }}</span>
                      </div>
                    </div>
                  </a>
                @empty
                  <div class="py-8 text-center text-xs text-ink-soft/60">
                    Tidak ada notifikasi baru
                  </div>
                @endforelse
              </div>

              <div class="mt-3 pt-2 border-t border-ink/5 text-center">
                <a href="{{ $dashboardUrl }}" class="btn btn-sm w-full border border-forest/20 text-forest hover:bg-forest/10 normal-case font-bold rounded-xl">Cek Notifikasi Lain</a>
              </div>
            </div>
          </div>

          <div class="h-4 w-[1px] bg-ink/10 mx-1"></div>
        @endif

        <!-- Profile Desktop -->
        @if(!$currentUserId)
          <div class="flex items-center gap-2">
            <div class="flex items-center"><a href="/login" class="rounded-full px-4 py-2 text-forest justify-between hover:bg-forest/10 font-bold {{ request()->is('login') ? 'bg-forest/10 text-forest' : '' }}">Masuk</a></div>
            <div class="flex items-center"><a href="/register" class="rounded-full px-4 py-2 bg-forest text-white justify-between hover:bg-forest/90 border-none rounded-full px-2 font-bold shadow-sm shadow-forest/20">Daftar</a></div>
          </div>
        @else
          <div class="dropdown dropdown-end relative">
            <a href="/dashboard" tabindex="0" class="btn btn-ghost btn-circle avatar placeholder focus:outline-none hover:bg-white/40">
              <div class="bg-forest text-white rounded-full w-9 h-9 overflow-hidden flex items-center justify-center ring-2 ring-forest/20 {{ request()->is('dashboard*') || request()->is('profile*') ? 'ring-forest' : '' }}">
                @if($navUser && $navUser->foto_profil)
                  <img src="{{ \Illuminate\Support\Facades\Storage::url($navUser->foto_profil) }}?v={{ $navUser->updated_at ? $navUser->updated_at->timestamp : time() }}" alt="{{ $navUser->name ?? session('name') }}" class="w-full h-full object-cover">
                @else
                  <span class="text-xs font-black font-display">{{ strtoupper(substr($navUser->name ?? session('name', 'U'), 0, 1)) }}</span>
                @endif
              </div>
            </a>
            
            <div tabindex="0" class="dropdown-content mt-3 z-[70] p-3 shadow-2xl bg-white/95 backdrop-blur-xl rounded-2xl w-64 border border-white/60 flex flex-col gap-1 text-left right-0">
              <div class="px-3 py-2 border-b border-ink/5 mb-1 text-left">
                <p class="text-sm font-extrabold text-ink truncate max-w-[210px]" title="{{ $navUser->name ?? session('name') }}">
                  {{ $navUser->name ?? session('name') }}
                </p>
                @if($currentRole == 'user')
                  <p class="text-xs text-forest font-semibold mt-0.5 flex items-center gap-1 font-mono">
                    <img src="{{ asset('images/sulapa-koin.png') }}" alt="Poin" class="w-3.5 h-3.5 object-contain shrink-0" onerror="this.style.display='none'">
                    <span>{{ number_format($navUser->points_balance ?? 0, 0, ',', '.') }} Poin</span>
                  </p>
                  @if($isPro)
                    <span class="text-[9px] bg-gradient-to-r from-forest to-emerald-600 text-white font-black px-2 py-0.5 rounded-full mt-1.5 inline-flex items-center gap-1 uppercase tracking-wider shadow-sm">
                      SulapaKarya PRO
                    </span>
                  @endif
                @else
                  <span class="text-[9px] bg-forest/10 text-forest font-black px-2 py-0.5 rounded mt-1 inline-block uppercase tracking-wider font-mono">
                    {{ $currentRole === 'penjemput' ? 'Kurir Lapangan' : $currentRole }}
                  </span>
                @endif
              </div>
              
              <a href="/dashboard" class="rounded-xl py-2 px-3 text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors {{ request()->is('dashboard*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Dashboard</a>
              <a href="/profile" class="rounded-xl py-2 px-3 text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors {{ request()->is('profile*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Profil Saya</a>
              
              <div class="my-1 border-t border-ink/5"></div>
              
              <form action="/logout" method="POST" class="p-0 m-0">
                @csrf
                <button type="submit" class="w-full text-left rounded-xl py-2 px-3 text-xs font-bold text-terracotta hover:bg-terracotta/10 transition-colors">
                  Keluar Akun
                </button>
              </form>
            </div>
          </div>
        @endif
      </div>

      <!-- ================================================================= -->
      <!-- MOBILE ACTION BAR (LAYAR HP / SCREEN < 1024PX)                   -->
      <!-- ================================================================= -->
      <div class="navbar-end w-auto lg:hidden flex items-center gap-1 shrink-0">
        
        <!-- 1. Keranjang Mobile -->
        @if($currentUserId && $currentRole === 'user')
          <a href="/keranjang" class="btn btn-ghost btn-sm btn-circle relative text-ink hover:bg-white/40">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            @if($cartCount > 0)
              <span class="badge badge-xs bg-terracotta text-white font-bold absolute -top-1 -right-1 font-mono">{{ $cartCount }}</span>
            @endif
          </a>
        @endif

        <!-- 2. Notifikasi Mobile -->
        <div class="dropdown dropdown-end relative">
          <button tabindex="0" aria-label="Notifikasi" class="btn btn-ghost btn-sm btn-circle relative text-ink hover:bg-white/40 focus:outline-none">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 8a6 6 0 10-12 0v5l-2 2h16l-2-2z"/>
              <path d="M13.73 21a2 2 0 01-3.46 0"/>
            </svg>
            @if($unreadCount > 0)
              <span class="badge badge-xs bg-terracotta text-white font-bold absolute top-1.5 right-1.5 p-1 animate-pulse"></span>
            @endif
          </button>
          
          <div tabindex="0" class="dropdown-content z-[70] p-0 shadow-2xl bg-white/95 backdrop-blur-xl rounded-2xl w-72 sm:w-80 border border-ink/10 right-0 mt-2 text-left overflow-hidden">
            <div class="px-3.5 py-2.5 bg-cream/40 border-b border-ink/5 flex items-center justify-between">
              <span class="font-bold text-xs text-ink">Notifikasi ({{ $unreadCount }})</span>
              @if($unreadCount > 0)
                <form action="{{ route('notifikasi.markAll') }}" method="POST" class="m-0 p-0">
                  @csrf
                  <button type="submit" class="text-[10px] font-semibold text-ink-soft hover:text-forest">Tandai dibaca</button>
                </form>
              @endif
            </div>
            <div class="divide-y divide-ink/5 max-h-60 overflow-y-auto">
              @forelse($navNotifications as $notif)
                <a href="{{ route('notifikasi.read', $notif->id) }}" class="block p-3 hover:bg-forest/5 {{ !$notif->is_read ? 'bg-forest/[0.04]' : '' }}">
                  <p class="font-bold text-xs text-ink truncate {{ !$notif->is_read ? 'text-forest' : '' }}">{{ $notif->title }}</p>
                  <p class="text-[10px] text-ink-soft line-clamp-2 mt-0.5">{{ $notif->message }}</p>
                </a>
              @empty
                <div class="py-6 text-center text-xs text-ink-soft/60">Tidak ada notifikasi</div>
              @endforelse
            </div>
            <div class="p-2 border-t border-ink/5 bg-cream/20 text-center">
              <a href="{{ $dashboardUrl }}" class="text-[11px] font-bold text-forest hover:underline">Lihat Dashboard</a>
            </div>
          </div>
        </div>

        <!-- Menu Burger Mobile -->
        <div class="dropdown dropdown-end relative">
          <button tabindex="0" role="button" aria-label="Buka Menu" class="btn btn-ghost btn-sm btn-circle text-ink hover:bg-white/40 focus:outline-none">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>

          <div tabindex="0" class="dropdown-content z-[70] p-3 shadow-2xl bg-white/95 backdrop-blur-2xl rounded-3xl w-72 sm:w-80 border border-white/80 mt-3 right-0 text-left overflow-hidden max-h-[80vh] flex flex-col">
            
            <div class="overflow-y-auto flex-1 custom-scrollbar">
              <!-- Profil Mobile -->
              @if($currentUserId)
                <div class="flex items-center gap-3 p-2.5 bg-forest/5 rounded-2xl mb-2.5 border border-forest/10">
                  <div class="w-10 h-10 rounded-full bg-forest text-white flex items-center justify-center font-bold text-sm shrink-0 overflow-hidden ring-2 ring-forest/20">
                    @if($navUser && $navUser->foto_profil)
                      <img src="{{ \Illuminate\Support\Facades\Storage::url($navUser->foto_profil) }}?v={{ time() }}" alt="Profil" class="w-full h-full object-cover" />
                    @else
                      <span class="font-display">{{ strtoupper(substr($navUser->name ?? session('name', 'U'), 0, 1)) }}</span>
                    @endif
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-ink truncate leading-tight">{{ $navUser->name ?? session('name', 'User') }}</p>
                    @if($currentRole === 'user')
                      <p class="text-[11px] text-forest font-semibold flex items-center gap-1 mt-0.5 font-mono">
                        <span>{{ number_format($navUser->points_balance ?? 0, 0, ',', '.') }} Poin</span>
                      </p>
                    @else
                      <span class="text-[9px] bg-forest text-white font-bold px-1.5 py-0.5 rounded-full inline-block uppercase tracking-wider mt-0.5">
                        {{ $currentRole }}
                      </span>
                    @endif
                  </div>
                </div>
              @endif

              <!-- Link Navigasi Mobile -->
              <nav class="space-y-1">
                <a href="/" data-nav="beranda" class="nav-link block px-3.5 py-2 rounded-xl text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors {{ request()->is('/') ? 'bg-forest/10 text-forest font-bold' : '' }}">Beranda</a>
                <a href="/#tentang-kami" data-nav="tentang-kami" class="nav-link block px-3.5 py-2 rounded-xl text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors">Tentang Kami</a>
                <a href="/#kalkulator" data-nav="kalkulator" class="nav-link block px-3.5 py-2 rounded-xl text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors">Kalkulator Poin</a>
                <a href="/katalog" data-nav="katalog" class="nav-link block px-3.5 py-2 rounded-xl text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors {{ request()->is('katalog*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Katalog Kriya</a>
                <a href="/#mitra-bisnis" data-nav="mitra-bisnis" class="nav-link block px-3.5 py-2 rounded-xl text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors {{ request()->is('mitra-bisnis*') ? 'bg-forest/10 text-forest font-bold' : '' }}">SulapaKarya PRO</a>

                <div class="my-2 border-t border-ink/5"></div>

                @if($currentUserId)
                  <a href="{{ $dashboardUrl }}" class="block px-3.5 py-2 rounded-xl text-xs font-bold text-forest hover:bg-forest/10 transition-colors {{ request()->is('*dashboard*') ? 'bg-forest/10' : '' }}">Dashboard Saya</a>
                  
                  @if($currentRole === 'user')
                    <a href="/riwayat-pembelian" class="block px-3.5 py-2 rounded-xl text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors {{ request()->is('riwayat-pembelian*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Riwayat Pembelian</a>
                    <a href="/setor-sampah/riwayat" class="block px-3.5 py-2 rounded-xl text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors {{ request()->is('*setor*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Riwayat Setoran</a>
                  @endif

                  <a href="/profile" class="block px-3.5 py-2 rounded-xl text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors {{ request()->is('profile*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Pengaturan Profil</a>
                  
                  <div class="my-2 border-t border-ink/5"></div>
                  
                  <form action="/logout" method="POST" class="p-0 m-0">
                    @csrf
                    <button type="submit" class="w-full text-left text-xs font-bold text-terracotta hover:bg-terracotta/10 rounded-xl px-3.5 py-2 transition-colors">
                      Keluar Akun
                    </button>
                  </form>
                @else
                  <div class="pt-1 grid grid-cols-2 gap-2">
                    <a href="/login" class="btn btn-sm btn-ghost border border-forest/20 text-forest font-bold rounded-xl text-center text-xs">Masuk</a>
                    <a href="/register" class="btn btn-sm bg-forest text-white font-bold rounded-xl text-center text-xs border-none shadow-md shadow-forest/20">Daftar</a>
                  </div>
                @endif
              </nav>
            </div>

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
      const sections = ["beranda", "tentang-kami", "cara-memilah", "kalkulator", "katalog", "mitra-bisnis"];
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