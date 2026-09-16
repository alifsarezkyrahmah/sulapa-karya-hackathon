<!DOCTYPE html>
<html lang="id" data-theme="sulapakarya">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard — SulapaKarya' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css">

    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              forest:     { DEFAULT: '#2E7D32', dark: '#1B5E20', light: '#E8F5E9' },
              maritime:   { DEFAULT: '#0277BD', dark: '#01579B', light: '#E1F5FE' },
              terracotta: { DEFAULT: '#D84315', dark: '#A8330F', light: '#FBE9E7' },
              cream: '#FAF6EF',
              sand:  '#F0E6D9',
              ink:   { DEFAULT: '#241F18', soft: '#6E675C' },
            },
            fontFamily: {
              display: ['Fraunces', 'serif'],
              body: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'sans-serif'],
              mono: ['"JetBrains Mono"', 'monospace'],
            },
          },
        },
      };
    </script>

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        html { scroll-behavior: smooth; }
        .custom-sidebar-scroll::-webkit-scrollbar { display: none; }
        .custom-sidebar-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="font-body text-ink antialiased bg-[#F7F5F0] min-h-screen selection:bg-forest/15 selection:text-forest">

    @php
        $userId = session('user_id') ?? auth()->id();
        $sidebarUser = \App\Models\User::find($userId);
        $currentRole = $sidebarUser->role ?? session('role', 'user');
        $cartCount = session('cart') ? collect(session('cart'))->sum('quantity') : 0;
        $userPoints = ($sidebarUser && $sidebarUser->points_balance > 0) ? $sidebarUser->points_balance : 0;
        $businessStatus = $sidebarUser->business_status ?? 'none';

        // Ambil riwayat notifikasi akun aktif
        $userNotifications = \Illuminate\Support\Facades\Schema::hasTable('notifications')
            ? \App\Models\Notification::where('user_id', $userId)->orderBy('created_at', 'desc')->take(6)->get()
            : collect([]);
        $unreadNotifCount = \Illuminate\Support\Facades\Schema::hasTable('notifications')
            ? \App\Models\Notification::where('user_id', $userId)->where('is_read', false)->count()
            : 0;
    @endphp

    @include('components.alert')

    <input id="dashboard-sidebar-drawer" type="checkbox" class="drawer-toggle peer" />
    <label for="dashboard-sidebar-drawer" class="fixed inset-0 bg-ink/40 backdrop-blur-xs z-40 transition-opacity duration-300 lg:hidden pointer-events-none opacity-0 peer-checked:pointer-events-auto peer-checked:opacity-100"></label>

    <!-- ================= KONTEN UTAMA ================= -->
    <div class="flex flex-col min-h-screen lg:pl-64 transition-all duration-300">
        
        <!-- Minimal Topbar Header -->
        <header class="bg-white/80 backdrop-blur-md border-b border-ink/5 sticky top-0 z-30 px-4 sm:px-8 lg:px-10 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <label for="dashboard-sidebar-drawer" class="btn btn-ghost btn-sm btn-circle lg:hidden text-ink-soft hover:bg-cream">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-5 h-5 stroke-current stroke-[2]"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </label>
                <div class="hidden sm:flex items-center gap-2 text-xs font-semibold text-ink-soft">
                    <span>Panel</span>
                    <span>/</span>
                    <span class="text-ink font-bold capitalize">{{ str_replace(['-', '_'], ' ', request()->segment(1) ?? 'Dashboard') }}</span>
                </div>
            </div>

            <!-- KANAN TOPBAR: URUTAN KIRI KE KANAN -> (1. BADGE ROLE, 2. KOIN POIN, 3. NOTIFIKASI) -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                
                <!-- 1. BADGE ROLE SAJA (TANPA NAMA USER) -->
                <span class="badge border-none text-[10px] font-bold tracking-wider uppercase px-2.5 py-2 rounded-lg 
                    {{ $currentRole === 'admin' ? 'bg-forest/10 text-forest' : ($currentRole === 'penjemput' ? 'bg-maritime/10 text-maritime' : ($businessStatus === 'approved' ? 'bg-amber-400/20 text-amber-900 border border-amber-400/30' : 'bg-sand/60 text-ink-soft')) }}">
                    @if($currentRole === 'penjemput')
                        Kurir Lapangan
                    @elseif($businessStatus === 'approved' && $currentRole === 'user')
                        Mitra PRO
                    @else
                        {{ $currentRole }}
                    @endif
                </span>

                <!-- 2. TAMPILAN POIN / KOIN SULAPA KHUSUS ROLE USER / WARGA -->
                @if($currentRole === 'user' || $currentRole === 'warga')
                    <a href="/pencairan-poin" class="group relative flex items-center bg-gradient-to-r from-amber-500/15 via-amber-400/20 to-amber-500/10 border-2 border-amber-400/50 hover:border-amber-400 pl-1.5 pr-4 py-1 rounded-full shadow-[0_4px_16px_rgba(245,158,11,0.22)] hover:shadow-[0_6px_22px_rgba(245,158,11,0.35)] transition-all duration-300 cursor-pointer select-none my-auto" title="Klik untuk mencairkan saldo poin">
                        <div class="relative w-9 h-9 sm:w-11 sm:h-11 -ml-2.5 rounded-full bg-gradient-to-tr from-amber-500 via-amber-300 to-yellow-100 p-0.5 shadow-md shadow-amber-600/30 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                            <div class="w-full h-full rounded-full bg-white/90 backdrop-blur-xs flex items-center justify-center overflow-hidden p-0.5">
                                <img src="{{ asset('images/sulapa-koin.png') }}" 
                                     alt="Koin Sulapa" 
                                     class="w-full h-full object-contain filter drop-shadow-[0_2px_4px_rgba(180,83,9,0.3)] group-hover:brightness-110 transition-all duration-300"
                                     onerror="this.onerror=null; this.src='https://api.iconify.design/solar:dollar-minimalistic-bold-duotone.svg?color=%23f59e0b';">
                            </div>
                            <div class="absolute inset-0 rounded-full border border-white/60 pointer-events-none opacity-80 group-hover:opacity-100"></div>
                        </div>

                        <div class="ml-2.5 flex items-baseline gap-1">
                            <span class="font-mono font-black text-sm sm:text-base text-ink tracking-tight group-hover:text-amber-900 transition-colors drop-shadow-xs">
                                {{ number_format($userPoints, 0, ',', '.') }}
                            </span>
                            <span class="text-[9px] sm:text-[10px] font-extrabold text-amber-700 uppercase tracking-wider font-sans">
                                POIN
                            </span>
                        </div>

                        <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500 ring-2 ring-white"></span>
                        </span>
                    </a>
                @endif

                <!-- 3. DROPDOWN NOTIFIKASI LONCENG -->
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle btn-sm text-ink-soft hover:text-ink hover:bg-cream/80 relative transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        
                        @if($unreadNotifCount > 0)
                            <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-terracotta opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-terracotta border-2 border-white"></span>
                            </span>
                        @endif
                    </label>

                    <div tabindex="0" class="dropdown-content z-[100] menu p-0 shadow-2xl bg-white border border-ink/10 rounded-2xl w-80 sm:w-96 max-w-[calc(100vw-2rem)] mt-2 text-left overflow-hidden">
                        <div class="px-4 py-3 bg-cream/40 border-b border-ink/5 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-xs text-ink">Aktivitas & Notifikasi</span>
                                @if($unreadNotifCount > 0)
                                    <span class="bg-forest/15 text-forest text-[10px] font-bold px-2 py-0.5 rounded-full font-mono">
                                        {{ $unreadNotifCount }} Baru
                                    </span>
                                @endif
                            </div>

                            @if($unreadNotifCount > 0)
                                <form action="{{ route('notifikasi.markAll') }}" method="POST" class="m-0 p-0">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-semibold text-ink-soft hover:text-forest transition-colors">
                                        Tandai dibaca
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="divide-y divide-ink/5 max-h-[360px] overflow-y-auto">
                            @forelse($userNotifications as $n)
                                <a href="{{ route('notifikasi.read', $n->id) }}" 
                                   class="p-3.5 flex items-start gap-3 hover:bg-cream/20 transition-colors {{ !$n->is_read ? 'bg-forest/[0.04]' : '' }}">
                                    
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-xs shadow-xs
                                        {{ $n->type === 'payout' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $n->type === 'deposit' ? 'bg-forest/15 text-forest' : '' }}
                                        {{ $n->type === 'order' ? 'bg-maritime/15 text-maritime' : '' }}
                                        {{ $n->type === 'warning' ? 'bg-terracotta/15 text-terracotta' : '' }}
                                        {{ $n->type === 'success' ? 'bg-forest/15 text-forest' : '' }}
                                        {{ !in_array($n->type, ['payout','deposit','order','warning','success']) ? 'bg-cream text-ink' : '' }}">
                                        @if($n->type === 'payout')
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                        @elseif($n->type === 'deposit')
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                        @elseif($n->type === 'order')
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                        @elseif($n->type === 'warning')
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                        @else
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1">
                                            <p class="font-bold text-xs text-ink truncate {{ !$n->is_read ? 'text-forest' : '' }}">
                                                {{ $n->title }}
                                            </p>
                                            @if(!$n->is_read)
                                                <span class="w-2 h-2 rounded-full bg-forest shrink-0"></span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-ink-soft leading-relaxed mt-0.5 line-clamp-2">
                                            {{ $n->message }}
                                        </p>
                                        <span class="text-[9px] text-ink-soft/60 font-mono block mt-1">
                                            {{ $n->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </a>
                            @empty
                                <div class="py-10 text-center text-xs text-ink-soft/60">
                                    Belum ada pemberitahuan baru saat ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="h-4 w-[1px] bg-ink/10 hidden sm:block"></div>

                <a href="/" class="btn btn-xs btn-ghost text-xs font-bold text-ink-soft hover:text-forest hover:bg-forest/10 rounded-lg px-2.5 hidden sm:inline-flex">
                    Lihat Web ↗
                </a>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="flex-1 p-6 sm:p-8 lg:p-10 max-w-7xl w-full mx-auto text-left">
            @yield('dashboard-content')
        </main>

        <!-- Minimal Footer -->
        <footer class="px-6 py-4 border-t border-ink/5 text-center sm:text-left sm:flex sm:justify-between text-[11px] font-medium text-ink-soft/60 max-w-7xl mx-auto w-full">
            <span>&copy; {{ date('Y') }} SulapaKarya — Sistem Sirkular Upcycling Makassar.</span>
            <span class="hidden sm:inline">Kelola sampah, ciptakan karya bernilai.</span>
        </footer>
    </div>

    <!-- ================= SIDEBAR NAVIGASI MINIMALIS ================= -->
    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col justify-between bg-[#1C1A16] text-[#E5DFD5] border-r border-white/5
        w-64 -translate-x-full peer-checked:translate-x-0 lg:translate-x-0 transition-transform duration-300 shadow-2xl lg:shadow-none custom-sidebar-scroll overflow-y-auto">
        
        <div class="p-5 space-y-6">
            <!-- Brand Logo -->
            <a href="/" class="flex items-center gap-3 px-2 py-1 group">
                <div class="w-8 h-8 rounded-xl bg-forest/20 text-white flex items-center justify-center p-1.5 ring-1 ring-forest/40">
                    <img src="{{ asset('images/logo.png') }}" alt="SulapaKarya" class="w-full h-full object-contain" onerror="this.src='{{ asset('images/tangan-botol.png') }}'">
                </div>
                <div>
                    <h2 class="font-display font-black text-sm tracking-tight text-white group-hover:text-forest-light transition-colors">SulapaKarya</h2>
                    <p class="text-[9px] text-[#A8A095] uppercase tracking-widest font-mono">Workspace</p>
                </div>
            </a>

            <!-- Navigasi Menu Minimalis -->
            <nav class="space-y-5">
                
                <!-- Section 1: Dashboard Utama (Warga & Admin) -->
                @if($currentRole !== 'penjemput')
                <div class="space-y-1">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-[#8C8478] px-3 pb-1">Utama</p>
                    <a href="/dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors {{ request()->is('dashboard') || request()->is('*/dashboard') ? 'bg-forest text-white shadow-sm font-bold' : 'text-[#D0C8BD] hover:bg-white/5 hover:text-white' }}">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/></svg>
                        <span>Dashboard</span>
                    </a>
                </div>
                @endif

                <!-- Section 2A: Menu Spesifik Role KURIR / PENJEMPUT -->
                @if($currentRole == 'penjemput')
                <div class="space-y-1">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-[#8C8478] px-3 pb-1">Operasional Kurir</p>

                    <a href="{{ route('penjemput.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors {{ request()->is('kurir/misi*') || request()->is('dashboard') || request()->routeIs('penjemput.dashboard') ? 'bg-forest text-white shadow-sm font-bold' : 'text-[#D0C8BD] hover:bg-white/5 hover:text-white' }}">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        <span>Misi Penjemputan</span>
                    </a>

                    <a href="{{ route('penjemput.history') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors {{ request()->is('kurir/riwayat*') || request()->routeIs('penjemput.history') ? 'bg-forest text-white shadow-sm font-bold' : 'text-[#D0C8BD] hover:bg-white/5 hover:text-white' }}">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        <span>Riwayat Setoran</span>
                    </a>
                </div>
                @endif

                <!-- Section 2B: Menu Spesifik Role WARGA & MITRA BISNIS -->
                @if($currentRole == 'user' || $currentRole == 'warga')
                <div class="space-y-1">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-[#8C8478] px-3 pb-1">Aktivitas Warga</p>
                    
                    <!-- Setor Sampah -->
                    <a href="/setor-sampah" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors {{ request()->is('setor-sampah*') ? 'bg-forest text-white shadow-sm font-bold' : 'text-[#D0C8BD] hover:bg-white/5 hover:text-white' }}">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                        <span>Setor Sampah</span>
                    </a>

                    <!-- Pencairan Poin -->
                    <a href="{{ route('pencairan.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors {{ request()->is('pencairan*') || request()->routeIs('pencairan.*') ? 'bg-forest text-white shadow-sm font-bold' : 'text-[#D0C8BD] hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                            <span>Pencairan Poin</span>
                        </div>
                        <span class="badge badge-xs {{ request()->is('pencairan*') ? 'bg-white text-forest' : 'bg-amber-400/20 text-amber-300' }} border-none font-bold text-[9px] px-1.5 py-0.5 font-mono">Rp</span>
                    </a>

                    <!-- Keranjang Kriya -->
                    <a href="/keranjang" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors {{ request()->is('keranjang*') ? 'bg-forest text-white shadow-sm font-bold' : 'text-[#D0C8BD] hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                            <span>Keranjang Kriya</span>
                        </div>
                        @if($cartCount > 0)
                            <span class="badge badge-xs bg-terracotta border-none text-white font-mono font-bold">{{ $cartCount }}</span>
                        @endif
                    </a>

                    <!-- Riwayat Setoran Sampah -->
                    <a href="/riwayat-setoran" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors {{ request()->is('riwayat-setoran*') ? 'bg-forest text-white shadow-sm font-bold' : 'text-[#D0C8BD] hover:bg-white/5 hover:text-white' }}">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        <span>Riwayat Setoran</span>
                    </a>

                    <!-- Riwayat Belanja Kriya -->
                    <a href="/riwayat-pembelian" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors {{ request()->is('riwayat-pembelian*') ? 'bg-forest text-white shadow-sm font-bold' : 'text-[#D0C8BD] hover:bg-white/5 hover:text-white' }}">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        <span>Riwayat Belanja</span>
                    </a>
                </div>

                <!-- Section Baru: B2B & Kemitraan SulapaKarya PRO -->
                <div class="space-y-1 pt-2 border-t border-white/5">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-[#8C8478] px-3 pb-1">Kemitraan B2B</p>
                    
                    <a href="{{ route('bisnis.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors {{ request()->is('mitra-bisnis*') || request()->routeIs('bisnis.*') ? 'bg-forest text-white shadow-sm font-bold' : 'text-[#D0C8BD] hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3 min-w-0">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="{{ $businessStatus === 'approved' ? 'text-amber-400' : '' }}"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            <span class="truncate">
                                {{ $businessStatus === 'approved' && $sidebarUser->business_name ? $sidebarUser->business_name : 'SulapaKarya PRO' }}
                            </span>
                        </div>
                        <div>
                            @if($businessStatus === 'approved')
                                <span class="badge badge-xs bg-amber-400 text-amber-950 border-none font-bold text-[8px] px-1.5 py-0.5 font-mono">
                                    PRO
                                </span>
                            @elseif($businessStatus === 'verified_unpaid')
                                <span class="badge badge-xs bg-amber-500/20 text-amber-300 border border-amber-400/30 font-bold text-[8px] px-1.5 py-0.5 font-mono">
                                    BAYAR
                                </span>
                            @elseif($businessStatus === 'pending')
                                <span class="badge badge-xs bg-white/10 text-white/70 border border-white/10 font-bold text-[8px] px-1.5 py-0.5 font-mono">
                                    PENDING
                                </span>
                            @else
                                <span class="badge badge-xs bg-white/10 text-[#A8A095] border border-white/10 font-bold text-[8px] px-1.5 py-0.5 font-mono">
                                    DAFTAR
                                </span>
                            @endif
                        </div>
                    </a>
                </div>
                @endif

                <!-- Section 2C: Menu Spesifik Role ADMIN -->
                @if($currentRole == 'admin')
                <div class="space-y-1">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-[#8C8478] px-3 pb-1">Manajemen Admin</p>

                    <a href="/statistik" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors {{ request()->is('statistik*') ? 'bg-forest text-white shadow-sm font-bold' : 'text-[#D0C8BD] hover:bg-white/5 hover:text-white' }}">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        <span>Statistik Platform</span>
                    </a>

                    <a href="/verifikasi-setoran" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors {{ request()->is('verifikasi-setoran*') ? 'bg-forest text-white shadow-sm font-bold' : 'text-[#D0C8BD] hover:bg-white/5 hover:text-white' }}">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>Verifikasi Setoran</span>
                    </a>

                    <a href="/verifikasi-poin" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors {{ request()->is('verifikasi-poin*') ? 'bg-forest text-white shadow-sm font-bold' : 'text-[#D0C8BD] hover:bg-white/5 hover:text-white' }}">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        <span>Verifikasi Poin</span>
                    </a>

                    <a href="/kelola-pengguna" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors {{ request()->is('kelola-pengguna*') ? 'bg-forest text-white shadow-sm font-bold' : 'text-[#D0C8BD] hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            <span>Kelola Pengguna</span>
                        </div>
                        @php
                            $adminPendingCount = \App\Models\User::where('business_status', 'pending')->count();
                        @endphp
                        @if($adminPendingCount > 0)
                            <span class="badge badge-xs bg-amber-400 text-amber-950 font-bold border-none font-mono px-1.5 py-0.5">
                                {{ $adminPendingCount }}
                            </span>
                        @endif
                    </a>

                    <a href="/kelola-produk" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors {{ request()->is('kelola-produk*') ? 'bg-forest text-white shadow-sm font-bold' : 'text-[#D0C8BD] hover:bg-white/5 hover:text-white' }}">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                        <span>Kelola Produk Kriya</span>
                    </a>

                    <a href="{{ route('admin.waste-prices.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors {{ request()->is('admin/harga-sampah*') || request()->is('harga-sampah*') || request()->routeIs('admin.waste-prices.*') ? 'bg-forest text-white shadow-sm font-bold' : 'text-[#D0C8BD] hover:bg-white/5 hover:text-white' }}">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        <span>Tarif & Nilai Sampah</span>
                    </a>
                </div>
                @endif

                <!-- Section 3: Standarisasi & QC (Semua Role) -->
                <div class="space-y-1 pt-2 border-t border-white/5">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-[#8C8478] px-3 pb-1">Standarisasi</p>
                    
                    <a href="https://drive.google.com/file/d/1wmkeqhpKQgUlFeEltqR7b7xnTHrDIE32/view?usp=sharing" 
                    target="_blank" 
                    rel="noopener noreferrer" 
                    class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-colors text-[#D0C8BD] hover:bg-white/5 hover:text-white group">
                        <div class="flex items-center gap-3">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 11l3 3L22 4"/>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                            <span>Panduan Aplikasi</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="badge badge-xs bg-forest/20 text-forest-light border-none font-bold text-[9px] px-1.5 py-0.5 font-mono">SOP</span>
                            <span class="text-[10px] text-[#8C8478] group-hover:text-white transition-colors">↗</span>
                        </div>
                    </a>
                </div>

            </nav>
        </div>

        <!-- Profil & Logout Minimalis di Bagian Bawah -->
        <div class="p-4 border-t border-white/5 bg-[#171512]">
            <div class="flex items-center justify-between gap-3">
                <a href="/profile" class="flex items-center gap-3 min-w-0 flex-1 group" title="Buka Profil">
                    <div class="w-8 h-8 rounded-full bg-forest text-white flex items-center justify-center font-bold text-xs shrink-0 overflow-hidden ring-1 ring-white/10 {{ request()->is('profile*') ? 'ring-2 ring-forest-light' : '' }}">
                        @if($sidebarUser && $sidebarUser->foto_profil && \Illuminate\Support\Facades\Storage::disk('public')->exists($sidebarUser->foto_profil))
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($sidebarUser->foto_profil) }}?v={{ $sidebarUser->updated_at ? $sidebarUser->updated_at->timestamp : time() }}" 
                                 alt="{{ $sidebarUser->name }}" 
                                 class="w-full h-full object-cover">
                        @elseif($sidebarUser && $sidebarUser->foto_profil)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($sidebarUser->foto_profil) }}" 
                                 alt="{{ $sidebarUser->name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($sidebarUser->name ?? session('name', 'U'), 0, 1)) }}
                        @endif
                    </div>
                    <div class="truncate text-left">
                        <p class="text-xs font-bold text-white truncate group-hover:text-forest-light transition-colors">{{ $sidebarUser->name ?? session('name', 'User') }}</p>
                        <p class="text-[10px] text-[#8C8478] truncate capitalize font-mono">
                            @if($businessStatus === 'approved' && $currentRole === 'user')
                                Mitra Bisnis PRO
                            @elseif($currentRole === 'penjemput')
                                Kurir Lapangan
                            @else
                                {{ $currentRole }}
                            @endif
                        </p>
                    </div>
                </a>

                <form action="/logout" method="POST" class="m-0 p-0">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-xs btn-square text-[#8C8478] hover:text-terracotta hover:bg-terracotta/10 rounded-lg" title="Keluar">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </button>
                </form>
            </div>
        </div>

    </aside>

</body>
</html>