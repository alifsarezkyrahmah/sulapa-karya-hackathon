<!-- ============ FLOATING CAPSULE NAVBAR ============ -->
<div class="w-full px-4 sm:px-6 lg:px-8 pt-4 sticky top-0 z-50">
  <header class="max-w-7xl mx-auto border border-ink/5 bg-white/30 backdrop-blur-xl rounded-full shadow-lg shadow-ink/[0.03] transition-all duration-300">
    <nav class="navbar px-6 py-2 min-h-[4rem]">
      
      <!-- Navbar Start: Logo -->
      <div class="navbar-start">
      <!-- SEBELUMNYA (Ganti bagian pembungkus w-8 h-8 ini) -->
      <a href="/" class="flex items-center gap-3 group transition-transform duration-200 active:scale-95">
        <!-- GANTI SVG DI DALAM SINI -->
        <img src="{{ asset('images/logo.png') }}" alt="Logo SulapaKarya" class="w-8 h-8 object-contain rounded-full shadow-md shadow-forest/20">
        
        <span class="font-display font-bold text-lg tracking-tight text-ink">
          SulapaKarya <span class="text-terracotta">Macca</span>
        </span>
      </a>
      </div>

      <!-- Navbar End: Menu items untuk Desktop (lg:flex) -->
      <div class="navbar-end w-full hidden lg:flex">
        <ul class="menu menu-horizontal gap-1 px-1 text-xs font-bold uppercase tracking-wider text-ink/70 items-center">
          <li><a href="#beranda" class="rounded-full px-4 py-2 hover:bg-forest/10 hover:text-forest transition-all">Beranda</a></li>
          <li><a href="#tentang" class="rounded-full px-4 py-2 hover:bg-forest/10 hover:text-forest transition-all">Tentang Kami</a></li>
          <li><a href="#cara-kerja" class="rounded-full px-4 py-2 hover:bg-forest/10 hover:text-forest transition-all">Cara Kerja</a></li>
          <li><a href="#produk" class="rounded-full px-4 py-2 hover:bg-forest/10 hover:text-forest transition-all">Katalog Kriya</a></li>
          
          <!-- MENU TAMBAHAN KHUSUS USER YANG SUDAH LOGIN (Berdasarkan Role) -->
          @if(session('user_id'))
            @if(session('role') == 'warga')
              <li><a href="/setor-sampah" class="rounded-full px-4 py-2 text-forest hover:bg-forest/10">Setor Sampah</a></li>
            @elseif(session('role') == 'pengrajin')
              <li><a href="/kelola-karya" class="rounded-full px-4 py-2 text-forest hover:bg-forest/10">Kelola Karya</a></li>
            @elseif(session('role') == 'admin')
              <li><a href="/dashboard-admin" class="rounded-full px-4 py-2 text-forest hover:bg-forest/10">Panel Admin</a></li>
            @endif
          @endif

          <div class="h-4 w-[1px] bg-ink/10 mx-2"></div>
          
          <!-- KONDISI 1: JIKA PENGUNJUNG ADALAH TAMU (BELUM LOGIN) -->
          @if(!session('user_id'))
            <li><a href="/login" class="rounded-full px-4 py-2 text-maritime hover:bg-maritime-light font-bold">Masuk</a></li>
            <li><a href="/register" class="btn btn-xs bg-gradient-to-r from-maritime to-maritime-dark hover:from-maritime-dark hover:to-maritime text-white border-none rounded-full px-4 normal-case font-bold shadow-sm shadow-maritime/20">Daftar</a></li>
          @endif

          <!-- KONDISI 2: JIKA PENGUNJUNG SUDAH LOGIN (Tampilkan Avatar Dropdown) -->
          @if(session('user_id'))
            <div class="dropdown dropdown-end ml-2">
              <button tabindex="0" class="btn btn-ghost btn-circle avatar online placeholder">
                <div class="bg-forest text-white rounded-full w-9">
                  <span class="text-xs font-bold">{{ strtoupper(substr(session('name'), 0, 1)) }}</span>
                </div>
              </button>
              <ul tabindex="0" class="dropdown-content menu menu-sm mt-3 z-[60] p-2 shadow-xl bg-white rounded-2xl w-52 border border-ink/5 gap-1 normal-case">
                <div class="px-4 py-2 border-b border-ink/5 mb-1 text-left">
                  <p class="text-xs font-bold text-ink truncate">{{ session('name') }}</p>
                  <p class="text-[10px] text-ink-soft truncate font-medium uppercase tracking-wider">{{ session('role') }}</p>
                </div>
                <li><a href="/dashboard" class="rounded-xl py-2 font-medium">Dashboard</a></li>
                <li><a href="/profil" class="rounded-xl py-2 font-medium">Profil Saya</a></li>
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
      
      <!-- Mobile Burger Trigger & Dropdown Menu (lg:hidden) -->
      <div class="navbar-end lg:hidden">
        <div class="dropdown dropdown-end">
          
          <!-- TOMBOL TRIGGER BURGER MOBILE (Anti-Bug Mobile Focus) -->
          <div tabindex="0" role="button" class="btn btn-ghost btn-sm btn-circle text-ink hover:bg-ink/5 active:scale-95 transition-all duration-200">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </div>

          <!-- ISI DROPDOWN MENU MOBILE -->
          <ul tabindex="0" class="dropdown-content menu menu-sm mt-3 z-[60] p-3 shadow-xl bg-white/95 backdrop-blur-lg rounded-2xl w-56 border border-ink/5 gap-1 normal-case">
            <div class="px-3 py-1 border-b border-ink/5 mb-1">
              <span class="text-[10px] font-extrabold uppercase tracking-wider text-ink/40">Navigasi</span>
            </div>
            <li><a href="/" class="rounded-xl py-2 font-medium text-ink hover:bg-forest/10">Beranda</a></li>
            <li><a href="#tentang" class="rounded-xl py-2 font-medium text-ink hover:bg-forest/10">Tentang Kami</a></li>
            <li><a href="#cara-kerja" class="rounded-xl py-2 font-medium text-ink hover:bg-forest/10">Cara Kerja</a></li>
            <li><a href="#produk" class="rounded-xl py-2 font-medium text-ink hover:bg-forest/10">Katalog Kriya</a></li>
            
            <!-- Kondisi 1: Jika Pengunjung Sudah Login (Berdasarkan Role Baru) -->
            @if(session('user_id'))
              <div class="my-1 border-t border-ink/5"></div>
              <div class="px-3 py-1 mb-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-forest">Akses Fitur ({{ session('role') }})</span>
              </div>
              
              @if(session('role') == 'user')
                <li><a href="/user/dashboard" class="rounded-xl py-2 font-medium text-ink hover:bg-forest/10">Dashboard Saya</a></li>
                <li><a href="/setor-sampah" class="rounded-xl py-2 font-medium text-ink hover:bg-forest/10">Setor Sampah</a></li>
              @elseif(session('role') == 'pengrajin')
                <li><a href="/pengrajin/dashboard" class="rounded-xl py-2 font-medium text-ink hover:bg-forest/10">Panel Pengrajin</a></li>
                <li><a href="/kelola-kriya" class="rounded-xl py-2 font-medium text-ink hover:bg-forest/10">Kelola Karya</a></li>
              @elseif(session('role') == 'admin')
                <li><a href="/dashboard-admin" class="rounded-xl py-2 font-medium text-ink hover:bg-forest/10">Panel Admin Utama</a></li>
              @endif
              
              <li><a href="/profil" class="rounded-xl py-2 font-medium text-ink hover:bg-forest/10">Profil Saya</a></li>
              
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

            <!-- Kondisi 2: Jika Pengunjung Adalah Tamu (Belum Login) -->
            @if(!session('user_id'))
              <div class="my-1 border-t border-ink/5"></div>
              <li><a href="/login" class="text-maritime font-bold rounded-xl py-2 hover:bg-maritime-light">Masuk</a></li>
              <li><a href="/register" class="bg-gradient-to-r from-maritime to-maritime-dark text-white font-bold hover:from-maritime-dark hover:to-maritime rounded-xl text-center py-2 border-none shadow-sm shadow-maritime/20">Daftar</a></li>
            @endif
          </ul>

        </div>
      </div>

    </nav>
  </header>
</div>