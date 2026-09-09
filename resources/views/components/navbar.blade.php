<div class="w-full px-4 sm:px-6 lg:px-8 pt-4 sticky top-0 z-50">
  <!-- Container Glassmorphism Utama -->
  <header class="max-w-7xl mx-auto border border-white/40 bg-white/25 backdrop-blur-md shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] rounded-full transition-all duration-300">
    <nav class="navbar bg-transparent px-4 sm:px-6 py-2 min-h-[4rem] flex justify-between items-center">

      <style>
        @media (min-width: 1024px) {
          .nav-center { 
            display: flex; 
            justify-content: center; 
          }
        }
        /* Style link aktif rapi di dalam kotak */
        .nav-link.active-nav {
          background-color: rgba(46, 125, 50, 0.12) !important;
          color: #2E7D32 !important;
          font-weight: 800 !important;
        }
      </style>
      
      <!-- Brand Logo -->
      <div class="navbar-start w-auto">
        <a href="/" class="flex items-center gap-3 group transition-transform duration-200 active:scale-95 shrink-0">
          <img src="{{ asset('images/logo.png') }}" alt="Logo SulapaKarya" class="w-8 h-8 object-contain rounded-full shadow-md shadow-forest/20" onerror="this.src='{{ asset('images/tangan-botol.png') }}'">
          <span class="font-display font-black text-lg tracking-tight text-ink">SulapaKarya</span>
        </a>
      </div>

      @php
        $currentUserId = session('user_id') ?? (auth()->check() ? auth()->id() : null);
        $navUser = $currentUserId ? \App\Models\User::find($currentUserId) : null;
        $currentRole = $navUser->role ?? session('role', 'user');
        
        $cartItems = session('cart', []);
        $cartCount = count($cartItems);

        // Ambil notifikasi riil dari database
        $navNotifications = ($currentUserId && \Illuminate\Support\Facades\Schema::hasTable('notifications'))
            ? \App\Models\Notification::where('user_id', $currentUserId)->orderBy('created_at', 'desc')->take(5)->get()
            : collect([]);
            
        $unreadCount = ($currentUserId && \Illuminate\Support\Facades\Schema::hasTable('notifications'))
            ? \App\Models\Notification::where('user_id', $currentUserId)->where('is_read', false)->count()
            : 0;
      @endphp

      <!-- Desktop Menu -->
      <div class="navbar-center hidden lg:flex flex-1 justify-center px-4">
        <ul id="desktop-nav-menu" class="menu menu-horizontal flex flex-nowrap gap-1 px-0 text-xs font-bold uppercase tracking-wider text-ink/80 items-center">
          <li><a href="/#live-count-section" data-nav="live-count-section" class="nav-link rounded-full px-4 py-2 hover:bg-white/40 hover:text-forest transition-all">Dampak</a></li>
          <li><a href="/#katalog-sampah" data-nav="katalog-sampah" class="nav-link rounded-full px-4 py-2 hover:bg-white/40 hover:text-forest transition-all">Jenis Sampah</a></li>
          <li><a href="/#kalkulator" data-nav="kalkulator" class="nav-link rounded-full px-4 py-2 hover:bg-white/40 hover:text-forest transition-all">Kalkulator Poin</a></li>
          <li><a href="/#produk" data-nav="produk" class="nav-link rounded-full px-4 py-2 hover:bg-white/40 hover:text-forest transition-all">Kriya Unggulan</a></li>
          <li><a href="/katalog" data-nav="katalog" class="nav-link rounded-full px-4 py-2 hover:bg-white/40 hover:text-forest transition-all {{ request()->is('katalog*') ? 'active-nav' : '' }}">Katalog Toko</a></li>
        </ul>
      </div>

      <!-- Action Group Kanan (Desktop) -->
      <div class="navbar-end w-auto hidden lg:flex items-center gap-2 shrink-0">
        @if($currentUserId)
          
          <!-- Notifikasi Dropdown Desktop -->
          <div id="notif-dropdown-wrapper" class="relative group">
            <div class="dropdown dropdown-end">
              <button id="notif-btn" aria-label="Notifikasi" class="btn btn-ghost btn-circle relative text-ink hover:bg-white/40 focus:outline-none">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0">
                  <path d="M18 8a6 6 0 10-12 0v5l-2 2h16l-2-2z"/>
                  <path d="M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
                @if($unreadCount > 0)
                  <span class="badge badge-xs bg-terracotta text-white font-extrabold absolute top-2 right-2 p-1 animate-pulse"></span>
                @endif
              </button>
              
              <div tabindex="0" class="dropdown-content z-50 p-0 shadow-2xl bg-white/95 backdrop-blur-xl rounded-2xl w-80 sm:w-88 border border-white/60 absolute right-0 mt-2 hidden group-hover:block group-focus-within:block text-left overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-ink/5 bg-cream/40">
                  <div class="flex items-center gap-2">
                    <span class="text-ink font-bold text-xs">Pemberitahuan</span>
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

                <div class="divide-y divide-ink/5 max-h-72 overflow-y-auto">
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
                      Tidak ada pemberitahuan baru
                    </div>
                  @endforelse
                </div>

                <div class="p-2.5 border-t border-ink/5 bg-cream/20 text-center">
                  <a href="/dashboard" class="text-xs font-bold text-forest hover:underline">Buka Dashboard</a>
                </div>
              </div>
            </div>
          </div>

          <!-- Keranjang Dropdown Desktop -->
          @if($currentRole == 'user' || $currentRole == 'warga')
            <div id="cart-dropdown-wrapper" class="relative group">
              <div class="dropdown dropdown-end">
                <button id="cart-btn" aria-label="Keranjang" class="btn btn-ghost btn-circle relative text-ink hover:bg-white/40 focus:outline-none">
                  <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                  </svg>
                  @if($cartCount > 0)
                    <span class="badge badge-sm bg-terracotta border-none text-white font-extrabold absolute -top-1 -right-1 font-mono">{{ $cartCount }}</span>
                  @endif
                </button>
                
                <div tabindex="0" class="dropdown-content z-50 p-4 shadow-2xl bg-white/90 backdrop-blur-xl rounded-2xl w-80 border border-white/60 absolute right-0 mt-2 hidden group-hover:block group-focus-within:block text-left overflow-hidden">
                  <div class="flex items-center justify-between pb-2 border-b border-ink/5 mb-2">
                    <span class="text-ink font-bold text-sm">Keranjang Kriya</span>
                    <span class="text-xs text-ink-soft font-medium">{{ $cartCount }} Item</span>
                  </div>

                  @if($cartCount > 0)
                    <div class="max-h-60 overflow-y-auto flex flex-col gap-2 pr-1 custom-scrollbar">
                      @foreach(array_slice($cartItems, 0, 5, true) as $id => $item)
                        <div class="flex items-center justify-between p-2 hover:bg-ink/[0.04] rounded-xl transition-colors">
                          <div class="flex items-center gap-3 truncate">
                            <img src="{{ isset($item['foto']) ? asset('storage/'.$item['foto']) : 'https://placehold.co/100' }}" 
                                alt="{{ $item['name'] ?? 'Produk' }}" 
                                class="w-10 h-10 object-cover rounded-lg border border-ink/5 shrink-0">
                            <div class="truncate text-left">
                              <p class="text-xs font-semibold text-ink truncate">{{ $item['name'] ?? 'Produk Kriya' }}</p>
                              <p class="text-[10px] text-ink-soft font-mono">{{ $item['quantity'] ?? 1 }}x</p>
                            </div>
                          </div>
                          <span class="text-xs font-bold text-forest shrink-0 font-mono">
                            Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}
                          </span>
                        </div>
                      @endforeach
                    </div>

                    <div class="mt-3 pt-2 border-t border-ink/5">
                      <a href="/keranjang" class="btn btn-sm w-full bg-forest hover:bg-forest/90 text-white normal-case font-bold border-none rounded-xl shadow-md">Lihat Keranjang</a>
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

          <div class="h-4 w-[1px] bg-ink/10 mx-1"></div>
        @endif

        <!-- Profile Desktop -->
        @if(!$currentUserId)
          <div class="flex items-center gap-2">
            <a href="/login" class="btn btn-sm btn-ghost rounded-full px-5 text-forest font-bold text-xs hover:bg-white/40 {{ request()->is('login') ? 'bg-white/40' : '' }}">Masuk</a>
            <a href="/register" class="btn btn-sm bg-forest hover:bg-forest/90 text-white border-none rounded-full px-5 text-xs font-bold shadow-md shadow-forest/20">Daftar</a>
          </div>
        @else
          <div class="dropdown dropdown-end">
            <button tabindex="0" class="btn btn-ghost btn-circle avatar placeholder focus:outline-none hover:bg-white/40">
              <div class="bg-forest text-white rounded-full w-9 h-9 overflow-hidden flex items-center justify-center ring-2 ring-forest/20 {{ request()->is('dashboard*') || request()->is('profile*') ? 'ring-forest' : '' }}">
                @if($navUser && $navUser->foto_profil)
                  <img src="{{ \Illuminate\Support\Facades\Storage::url($navUser->foto_profil) }}?v={{ $navUser->updated_at ? $navUser->updated_at->timestamp : time() }}" alt="{{ $navUser->name ?? session('name') }}" class="w-full h-full object-cover">
                @else
                  <span class="text-xs font-black font-display">{{ strtoupper(substr($navUser->name ?? session('name', 'U'), 0, 1)) }}</span>
                @endif
              </div>
            </button>
            <ul tabindex="0" class="dropdown-content menu menu-sm mt-3 z-[60] p-2 shadow-2xl bg-white/90 backdrop-blur-xl rounded-2xl w-56 border border-white/60 gap-1 normal-case text-left">
              <div class="px-4 py-2.5 border-b border-ink/5 mb-1 text-left">
                <p class="text-xs font-extrabold text-ink truncate">{{ $navUser->name ?? session('name') }}</p>
                <span class="text-[9px] bg-forest/10 text-forest font-black px-2 py-0.5 rounded mt-1 inline-block uppercase tracking-wider">{{ $currentRole === 'penjemput' ? 'Kurir Lapangan' : $currentRole }}</span>
              </div>
              <li><a href="/dashboard" class="rounded-xl py-2 font-medium {{ request()->is('dashboard*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Dashboard</a></li>
              <li><a href="/profile" class="rounded-xl py-2 font-medium {{ request()->is('profile*') ? 'bg-forest/10 text-forest font-bold' : '' }}">Profil Saya</a></li>
              <div class="my-1 border-t border-ink/5"></div>
              <li>
                <form action="/logout" method="POST" class="p-0">
                  @csrf
                  <button type="submit" class="w-full text-left rounded-xl py-2 px-3 font-bold text-terracotta hover:bg-terracotta/10">
                    Keluar Akun
                  </button>
                </form>
              </li>
            </ul>
          </div>
        @endif
      </div>

      <!-- ================================================================= -->
      <!-- MOBILE ACTION BAR (NOTIFIKASI + KERANJANG + BURGER DROPDOWN RAPI) -->
      <!-- ================================================================= -->
      <div class="navbar-end w-auto lg:hidden flex items-center gap-1">
        
        @if($currentUserId)
          <!-- Tombol Lonceng Notifikasi Mobile -->
          <div class="dropdown dropdown-end">
            <button tabindex="0" aria-label="Notifikasi" class="btn btn-ghost btn-sm btn-circle relative text-ink hover:bg-white/40 focus:outline-none">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8a6 6 0 10-12 0v5l-2 2h16l-2-2z"/>
                <path d="M13.73 21a2 2 0 01-3.46 0"/>
              </svg>
              @if($unreadCount > 0)
                <span class="badge badge-xs bg-terracotta text-white font-bold absolute top-1.5 right-1.5 p-1 animate-pulse"></span>
              @endif
            </button>
            
            <div tabindex="0" class="dropdown-content z-[70] p-0 shadow-2xl bg-white rounded-2xl w-72 sm:w-80 border border-ink/10 absolute right-0 mt-2 text-left overflow-hidden">
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
                <a href="/dashboard" class="text-[11px] font-bold text-forest hover:underline">Buka Dashboard</a>
              </div>
            </div>
          </div>

          <!-- Keranjang Mobile -->
          @if($currentRole == 'user' || $currentRole == 'warga')
            <a href="/keranjang" class="btn btn-ghost btn-sm btn-circle relative text-ink hover:bg-white/40">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
              @if($cartCount > 0)
                <span class="badge badge-xs bg-terracotta text-white font-bold absolute -top-1 -right-1 font-mono">{{ $cartCount }}</span>
              @endif
            </a>
          @endif
        @endif

        <!-- Menu Burger Mobile (Diperbaiki: Lebar Cukup, Overflow Hidden, Tidak Meluber) -->
        <div class="dropdown dropdown-end">
          <button tabindex="0" role="button" aria-label="Buka Menu" class="btn btn-ghost btn-sm btn-circle text-ink hover:bg-white/40 focus:outline-none">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>

          <div tabindex="0" class="dropdown-content z-[60] p-3 shadow-2xl bg-white/95 backdrop-blur-2xl rounded-3xl w-72 sm:w-80 border border-white/80 mt-3 text-left overflow-hidden">
            
            <!-- Profil User Header (Nama Panjang Aman & Terpotong Rapi) -->
            @if($currentUserId)
              <div class="flex items-center gap-3 p-2.5 bg-cream/40 rounded-2xl mb-2.5 border border-ink/5">
                <div class="w-10 h-10 rounded-full bg-forest text-white flex items-center justify-center font-bold text-sm shrink-0 overflow-hidden ring-1 ring-ink/5">
                  @if($navUser && $navUser->foto_profil)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($navUser->foto_profil) }}?v={{ $navUser->updated_at ? $navUser->updated_at->timestamp : time() }}" alt="Profil" class="w-full h-full object-cover" />
                  @else
                    {{ strtoupper(substr($navUser->name ?? session('name', 'U'), 0, 1)) }}
                  @endif
                </div>
                <div class="min-w-0 flex-1">
                  <p class="text-xs font-bold text-ink truncate leading-tight">{{ $navUser->name ?? session('name') }}</p>
                  <span class="text-[9px] bg-forest/10 text-forest font-black px-1.5 py-0.5 rounded mt-0.5 inline-block uppercase tracking-wider">
                    {{ $currentRole === 'penjemput' ? 'Kurir Lapangan' : $currentRole }}
                  </span>
                </div>
              </div>
            @endif

            <!-- Navigasi Utama Mobile (Dengan Batasan Padding Tepat) -->
            <nav class="space-y-1">
              <a href="/#live-count-section" data-nav="live-count-section" class="nav-link block px-3.5 py-2 rounded-xl text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors">Dampak Lingkungan</a>
              <a href="/#katalog-sampah" data-nav="katalog-sampah" class="nav-link block px-3.5 py-2 rounded-xl text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors">Jenis Sampah</a>
              <a href="/#kalkulator" data-nav="kalkulator" class="nav-link block px-3.5 py-2 rounded-xl text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors">Kalkulator Poin</a>
              <a href="/#produk" data-nav="produk" class="nav-link block px-3.5 py-2 rounded-xl text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors">Kriya Unggulan</a>
              <a href="/katalog" data-nav="katalog" class="nav-link block px-3.5 py-2 rounded-xl text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors {{ request()->is('katalog*') ? 'active-nav' : '' }}">Katalog Toko</a>
              
              @if($currentUserId)
                <div class="my-2 border-t border-ink/5"></div>
                <a href="/dashboard" class="block px-3.5 py-2 rounded-xl text-xs font-bold text-forest hover:bg-forest/10 transition-colors {{ request()->is('dashboard*') ? 'bg-forest/10' : '' }}">Dashboard Saya</a>
                <a href="/profile" class="block px-3.5 py-2 rounded-xl text-xs font-semibold text-ink hover:bg-forest/10 hover:text-forest transition-colors {{ request()->is('profile*') ? 'active-nav' : '' }}">Profil Saya</a>
                
                <div class="my-2 border-t border-ink/5"></div>
                <form action="/logout" method="POST" class="p-0 m-0">
                  @csrf
                  <button type="submit" class="w-full text-left text-xs font-bold text-terracotta hover:bg-terracotta/10 rounded-xl px-3.5 py-2 transition-colors">
                    Keluar Akun
                  </button>
                </form>
              @else
                <div class="my-2 border-t border-ink/5 pt-2 grid grid-cols-2 gap-2">
                  <a href="/login" class="btn btn-sm btn-ghost border border-forest/20 text-forest font-bold rounded-xl text-center text-xs">Masuk</a>
                  <a href="/register" class="btn btn-sm bg-forest text-white font-bold rounded-xl text-center text-xs border-none shadow-md shadow-forest/20">Daftar</a>
                </div>
              @endif
            </nav>

          </div>
        </div>
      </div>

    </nav>
  </header>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const isHomePage = window.location.pathname === "/" || window.location.pathname === "";

    if (isHomePage) {
      const sections = ["live-count-section", "katalog-sampah", "kalkulator", "produk"];
      const elements = sections.map(id => document.getElementById(id)).filter(el => el !== null);

      const observerOptions = {
        root: null,
        rootMargin: "-20% 0px -50% 0px",
        threshold: 0
      };

      const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            activateNavbarLink(entry.target.id);
          }
        });
      }, observerOptions);

      elements.forEach(el => sectionObserver.observe(el));
    }

    function activateNavbarLink(id) {
      document.querySelectorAll(".nav-link[data-nav]").forEach(link => {
        if (link.getAttribute("data-nav") !== "katalog") {
          link.classList.remove("active-nav");
        }
      });

      const activeLinks = document.querySelectorAll(`.nav-link[data-nav="${id}"]`);
      activeLinks.forEach(link => {
        link.classList.add("active-nav");
      });
    }
  });
</script>