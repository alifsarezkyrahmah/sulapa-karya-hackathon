<!DOCTYPE html>
<html lang="id" data-theme="sulapakarya">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard — SulapaKarya Macca' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">

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
              ink:   { DEFAULT: '#241F18', soft: '#5C5448' },
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
        .tag-stitch {
            border: 1.5px dashed currentColor;
            border-radius: 999px;
        }
        .dot-grid {
            background-image: radial-gradient(currentColor 1.4px, transparent 1.4px);
            background-size: 14px 14px;
        }
    </style>
</head>

<body class="font-body text-ink antialiased bg-gray-100 min-h-screen overflow-x-hidden">

    @php
        $sidebarUser = \App\Models\User::find(session('user_id'));
        $currentRole = $sidebarUser->role ?? session('role', 'user');
    @endphp

    @include('components.alert')

    <input id="dashboard-sidebar-drawer" type="checkbox" class="drawer-toggle peer" />

    <label for="dashboard-sidebar-drawer" class="fixed inset-0 bg-ink/40 backdrop-blur-sm z-40 transition-opacity duration-300 lg:hidden pointer-events-none opacity-0 peer-checked:pointer-events-auto peer-checked:opacity-100"></label>

    <div class="flex flex-col min-h-screen bg-cream/30 lg:pl-20 transition-all duration-300">
        
        <div class="lg:hidden navbar bg-white/80 backdrop-blur-md border-b border-ink/5 px-4 min-h-[3.5rem] sticky top-0 z-40 flex justify-between items-center shadow-sm">
            <label for="dashboard-sidebar-drawer" class="btn btn-square btn-ghost btn-sm text-ink-soft">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-5 h-5 stroke-current stroke-[2.2]"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </label>
            <span class="font-display font-extrabold text-sm text-ink tracking-tight">SulapaKarya Macca</span>
            <span class="badge bg-forest/10 border-none text-forest font-extrabold text-[9px] uppercase tracking-wider px-2 py-2 rounded-md">
                {{ $currentRole }}
            </span>
        </div>

        <main class="flex-1 p-6 sm:p-8 lg:p-10 max-w-7xl w-full mx-auto">
            @yield('dashboard-content')
        </main>

        <footer class="p-4 border-t border-ink/5 text-center text-[11px] font-semibold tracking-wide text-ink-soft/50 bg-white/20w-full">
            &copy; {{ date('Y') }} SulapaKarya Macca Makassar. Hak Cipta Dilindungi.
        </footer>
    </div>

    <!-- ================= SISI KIRI: SIDEBAR NAVIGASI SMART HOVER (FIXED SCROLL) ================= -->
    <!-- PERBAIKAN: Mengganti overflow-hidden dengan overflow-x-hidden & overflow-y-auto, serta menyembunyikan scrollbar bawaan browser -->
    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col justify-between bg-ink text-sand p-4 transition-all duration-300 group/sidebar shadow-xl
        w-72 -translate-x-full peer-checked:translate-x-0
        lg:w-20 lg:translate-x-0 lg:hover:w-72
        overflow-x-hidden overflow-y-auto [&::-webkit-scrollbar]:none [-ms-overflow-style:none] [scrollbar-width:none]">
        
        <!-- Pola Grid Transparan -->
        <div class="absolute inset-0 dot-grid text-white/[0.015] pointer-events-none"></div>

        <div class="relative z-10 space-y-6">
            <!-- Brand Logo Panel -->
            <div class="flex items-center gap-4 pb-4 border-b border-white/10 min-h-[3.5rem]">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-forest to-forest-dark text-white flex items-center justify-center shadow-md shrink-0 mx-auto lg:mx-0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 20A7 7 0 0 1 4 13c0-4 3-8 8-9 0 0 1 5-1 9 2-3 4-4 7-4 0 5-4 9-8 9 0 0-1 1-1 2z"/></svg>
                </div>
                <span class="font-display font-bold text-base tracking-tight text-cream whitespace-nowrap transition-opacity duration-300 lg:opacity-0 lg:group-hover/sidebar:opacity-100">
                    SulapaKarya <span class="text-sand/50 font-body text-xs font-normal">Panel</span>
                </span>
            </div>

            <!-- MENU UTAMA -->
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-white/20 px-3 block mb-3 whitespace-nowrap transition-opacity duration-300 lg:opacity-0 lg:group-hover/sidebar:opacity-100">
                    Navigasi Fitur
                </span>
                
                <ul class="space-y-1.5 font-medium text-sm p-0 m-0 list-none">
                    <!-- 1. Ringkasan Panel (Dashboard) -->
                    <li>
                        <a href="/dashboard" class="flex items-center gap-4 px-3.5 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('dashboard') || request()->is('*/dashboard') ? 'bg-forest text-white shadow-md font-bold' : '' }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="shrink-0"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
                            <span class="whitespace-nowrap transition-opacity duration-300 lg:opacity-0 lg:group-hover/sidebar:opacity-100">Ringkasan Panel</span>
                        </a>
                    </li>

                    <!-- ================= NAVIGASI ROLE: USER / WARGA ================= -->
                    @if($currentRole == 'user' || $currentRole == 'admin')
                        <!-- 2. Setor Sampah Digital -->
                        <li>
                            <a href="/setor-sampah" class="flex items-center gap-4 px-3.5 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('setor-sampah*') ? 'bg-forest text-white font-bold' : '' }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="shrink-0"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22" x2="12" y2="12"/></svg>
                                <span class="whitespace-nowrap transition-opacity duration-300 lg:opacity-0 lg:group-hover/sidebar:opacity-100">Setor Sampah Digital</span>
                            </a>
                        </li>
                        <!-- 3. Tukar Poin Kriya (Katalog) -->
                        <li>
                            <a href="/katalog" class="flex items-center gap-4 px-3.5 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('katalog*') ? 'bg-forest text-white font-bold' : '' }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="shrink-0"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
                                <span class="whitespace-nowrap transition-opacity duration-300 lg:opacity-0 lg:group-hover/sidebar:opacity-100">Tukar Poin Kriya</span>
                            </a>
                        </li>
                        <!-- 4. Riwayat Setoran -->
                        <li>
                            <a href="/riwayat-setoran" class="flex items-center gap-4 px-3.5 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('riwayat-setoran*') ? 'bg-forest text-white font-bold' : '' }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="shrink-0"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><line x1="5" y1="6" x2="5.01" y2="6"/><line x1="5" y1="12" x2="5.01" y2="12"/><line x1="5" y1="18" x2="5.01" y2="18"/></svg>
                                <span class="whitespace-nowrap transition-opacity duration-300 lg:opacity-0 lg:group-hover/sidebar:opacity-100">Riwayat Setoran</span>
                            </a>
                        </li>
                        <!-- 5. Riwayat Pembelian -->
                        <li>
                            <a href="/riwayat-pembelian" class="flex items-center gap-4 px-3.5 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('riwayat-pembelian*') ? 'bg-forest text-white font-bold' : '' }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="shrink-0"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                <span class="whitespace-nowrap transition-opacity duration-300 lg:opacity-0 lg:group-hover/sidebar:opacity-100">Riwayat Pembelian</span>
                            </a>
                        </li>
                    @endif

                    <!-- ================= NAVIGASI ROLE: PENJEMPUT / KURIR ================= -->
                    @if($currentRole == 'penjemput')
                        <li>
                            <a href="/scan-qr" class="flex items-center gap-4 px-3.5 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('scan-qr*') ? 'bg-forest text-white font-bold' : '' }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="shrink-0"><rect x="3" y="3" width="6" height="6" rx="1"/><rect x="15" y="3" width="6" height="6" rx="1"/><rect x="3" y="15" width="6" height="6" rx="1"/><path d="M16 16h2v2h-2zm0 0h-2v-2h2zM12 4v16M4 12h16"/></svg>
                                <span class="whitespace-nowrap transition-opacity duration-300 lg:opacity-0 lg:group-hover/sidebar:opacity-100">Scan QR Kriya Warga</span>
                            </a>
                        </li>
                    @endif

                    <!-- ================= NAVIGASI ROLE: ADMIN UTAMA ================= -->
                    @if($currentRole == 'admin')
                        <!-- 6. Verifikasi Setoran -->
                        <li>
                            <a href="/verifikasi-setoran" class="flex items-center gap-4 px-3.5 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('verifikasi-setoran*') ? 'bg-forest text-white font-bold' : '' }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <span class="whitespace-nowrap transition-opacity duration-300 lg:opacity-0 lg:group-hover/sidebar:opacity-100">Verifikasi Setoran</span>
                            </a>
                        </li>
                        <!-- 7. Kelola User -->
                        <li>
                            <a href="/kelola-pengguna" class="flex items-center gap-4 px-3.5 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('kelola-pengguna*') ? 'bg-forest text-white font-bold' : '' }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="shrink-0"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                <span class="whitespace-nowrap transition-opacity duration-300 lg:opacity-0 lg:group-hover/sidebar:opacity-100">Kelola User</span>
                            </a>
                        </li>
                        <!-- 8. Kelola Produk -->
                        <li>
                            <a href="/kelola-produk" class="flex items-center gap-4 px-3.5 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('kelola-produk*') ? 'bg-forest text-white font-bold' : '' }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="shrink-0"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                                <span class="whitespace-nowrap transition-opacity duration-300 lg:opacity-0 lg:group-hover/sidebar:opacity-100">Kelola Produk</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- KEAMANAN -->
            <div class="pt-2">
                <span class="text-[10px] font-bold uppercase tracking-widest text-white/20 px-3 block mb-3 whitespace-nowrap transition-opacity duration-300 lg:opacity-0 lg:group-hover/sidebar:opacity-100">
                    Keamanan
                </span>
                <ul class="space-y-1.5 font-medium text-sm p-0 m-0 list-none">
                    <!-- 9. Kelola PIN Transaksi -->
                    <li>
                        <a href="{{ route('set-pin') }}" class="flex items-center gap-4 px-3.5 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('set-pin*') ? 'bg-forest text-white font-bold' : '' }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" class="shrink-0"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <span class="whitespace-nowrap transition-opacity duration-300 lg:opacity-0 lg:group-hover/sidebar:opacity-100">Kelola PIN Transaksi</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- ================= WIDGET PROFIL BAWAH SIDEBAR ================= -->
        <!-- Ditambahkan margin-top agar tetap terdorong ke bawah namun fleksibel saat di-scroll -->
        <div class="relative z-10 bg-white/5 p-3 rounded-2xl border border-white/5 flex flex-col gap-3 mt-12 shrink-0">
            <div class="flex items-center gap-3 truncate justify-center lg:justify-start">
                <div class="avatar {{ $sidebarUser && $sidebarUser->foto_profil ? '' : 'placeholder' }} online shrink-0">
                    <div class="bg-white/10 text-cream rounded-full w-9 h-9 shadow-inner overflow-hidden flex items-center justify-center ring-2 ring-white/10">
                        @if($sidebarUser && $sidebarUser->foto_profil)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($sidebarUser->foto_profil) }}?v={{ time() }}" alt="Profil" class="w-full h-full object-cover" />
                        @else
                            <span class="text-xs font-bold font-display">{{ strtoupper(substr($sidebarUser->name ?? session('name', 'A'), 0, 1)) }}</span>
                        @endif
                    </div>
                </div>
                
                <div class="truncate text-left flex-1 transition-opacity duration-300 lg:opacity-0 lg:group-hover/sidebar:opacity-100">
                    <p class="text-xs font-bold text-cream truncate leading-tight">{{ $sidebarUser->name ?? session('name', 'User') }}</p>
                    <span class="text-[9px] bg-forest/20 text-forest-light font-extrabold px-1.5 py-0.5 rounded-md mt-0.5 inline-block uppercase tracking-wider">
                        {{ $currentRole }}
                    </span>
                </div>
            </div>

            <!-- Panel Tombol Pintasan Cepat -->
            <div class="grid grid-cols-3 gap-1.5 pt-3 border-t border-white/10 text-center text-[10px] font-bold transition-all duration-300 lg:opacity-0 lg:max-h-0 lg:group-hover/sidebar:opacity-100 lg:group-hover/sidebar:max-h-20 overflow-hidden">
                <a href="/" class="py-2 rounded-lg bg-white/5 hover:bg-white/10 text-cream transition-all flex items-center justify-center" title="Kembali ke Beranda Depan">Beranda</a>
                <a href="/profile" class="py-2 rounded-lg bg-white/5 hover:bg-white/10 text-cream transition-all flex items-center justify-center {{ request()->is('profile*') ? 'bg-forest text-white' : '' }}" title="Kelola Profil">Profil</a>
                <form action="/logout" method="POST" class="m-0 p-0">
                    @csrf
                    <button type="submit" class="w-full py-2 rounded-lg bg-terracotta/20 hover:bg-terracotta text-white transition-all flex items-center justify-center" title="Keluar Sesi">Keluar</button>
                </form>
            </div>
        </div>
    </aside>

</body>
</html>