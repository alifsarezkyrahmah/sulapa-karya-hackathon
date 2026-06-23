<!DOCTYPE html>
<html lang="id" data-theme="sulapakarya">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard — SulapaKarya Macca' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind & DaisyUI CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css">

    <!-- Tailwind Custom Configuration -->
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

    <!-- Laravel Vite Assets -->
    @vite(['resources/css/app.css','resources/js/app.js'])

    <!-- Style Inti Tambahan -->
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

<body class="font-body text-ink antialiased bg-gray-100 min-h-screen">

    <!-- 1. ALERT TOAST GLOBAL -->
    @include('components.alert')

    <!-- 2. DAISYUI RESPONSIVE DRAWER LAYOUT -->
    <div class="drawer lg:drawer-open">
        <input id="dashboard-sidebar-drawer" type="checkbox" class="drawer-toggle" />
        
        <!-- ================= SISI KANAN: AREA KONTEN UTAMA ================= -->
        <div class="drawer-content flex flex-col min-h-screen bg-cream/30">
            
            <!-- BILAH MEMU MOBILE: Hanya Muncul di Layar Handphone (lg:hidden) -->
            <div class="lg:hidden navbar bg-white/80 backdrop-blur-md border-b border-ink/5 px-4 min-h-[3.5rem] sticky top-0 z-40 flex justify-between items-center shadow-sm">
                <label for="dashboard-sidebar-drawer" class="btn btn-square btn-ghost btn-sm text-ink-soft">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-5 h-5 stroke-current stroke-[2.2]"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </label>
                <span class="font-display font-extrabold text-sm text-ink tracking-tight">SulapaKarya Macca</span>
                <span class="badge bg-forest/10 border-none text-forest font-extrabold text-[9px] uppercase tracking-wider px-2 py-2 rounded-md">
                    {{ session('role') }}
                </span>
            </div>

            <!-- CONTAINER DINAMIS MAIN KONTEN (Bersih Bebas Navbar Desktop) -->
            <main class="flex-1 p-6 sm:p-8 lg:p-10 max-w-7xl w-full mx-auto">
                @yield('dashboard-content')
            </main>

            <!-- MINI FOOTER INTERNAL -->
            <footer class="p-4 border-t border-ink/5 text-center text-[11px] font-semibold tracking-wide text-ink-soft/50 bg-white/20">
                &copy; {{ date('Y') }} SulapaKarya Macca Makassar. Hak Cipta Dilindungi.
            </footer>
        </div>

        @php
            // Sinkronisasi data user terautentikasi secara real-time untuk kebutuhan Avatar di Sidebar
            $sidebarUser = \App\Models\User::find(session('user_id'));
        @endphp

        <!-- ================= SISI KIRI: SIDEBAR NAVIGASI INTEGRAL ================= -->
        <div class="drawer-side z-50">
            <label for="dashboard-sidebar-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            
            <aside class="w-72 min-h-screen bg-ink text-sand p-6 flex flex-col justify-between border-r border-ink/10 relative">
                <!-- Aksen Pola Kriya Halus pada Sidebar -->
                <div class="absolute inset-0 dot-grid text-white/[0.015] pointer-events-none"></div>

                <div class="relative z-10">
                    <!-- Brand Logo Panel -->
                    <div class="flex items-center gap-3 pb-6 border-b border-white/10 mb-6">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-forest to-forest-dark text-white flex items-center justify-center shadow-md shadow-forest/20">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 20A7 7 0 0 1 4 13c0-4 3-8 8-9 0 0 1 5-1 9 2-3 4-4 7-4 0 5-4 9-8 9 0 0-1 1-1 2z"/></svg>
                        </div>
                        <span class="font-display font-bold text-base tracking-tight text-cream">
                            SulapaKarya <span class="text-sand/70 font-body text-xs font-normal">Panel</span>
                        </span>
                    </div>

                    <!-- MENU UTAMA MENURUT ROLE AKUN -->
                    <span class="text-[10px] font-bold uppercase tracking-widest text-white/30 px-3 block mb-2">Navigasi Fitur</span>
                    <ul class="menu p-0 gap-1 font-medium text-sm">
                        <!-- Rute General Dashboard -->
                        <li>
                            <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('dashboard') || request()->is('*/dashboard') ? 'bg-forest text-white shadow-md shadow-forest/10 font-bold' : '' }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                Ringkasan Panel
                            </a>
                        </li>

                        <!-- JIKA LOGIN SEBAGAI USER (WARGA) -->
                        @if(session('role') == 'user')
                            <li>
                                <a href="/setor-sampah" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('setor-sampah*') ? 'bg-forest text-white font-bold' : '' }}">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
                                    Setor Sampah Digital
                                </a>
                            </li>
                            <li>
                                <a href="/katalog" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('katalog*') ? 'bg-forest text-white font-bold' : '' }}">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                    Tukar Poin Kriya
                                </a>
                            </li>
                        @endif

                        <!-- JIKA LOGIN SEBAGAI PENGRAJIN / ARTISAN -->
                        @if(session('role') == 'pengrajin' || session('role') == 'artisan')
                            <li>
                                <a href="/kelola-karya" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('kelola-karya*') ? 'bg-forest text-white font-bold' : '' }}">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                    Kelola Produk Kriya
                                </a>
                            </li>
                        @endif

                        <!-- JIKA LOGIN SEBAGAI ADMIN -->
                        @if(session('role') == 'admin')
                            <li>
                                <a href="/verifikasi-setoran" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('verifikasi-setoran*') ? 'bg-forest text-white font-bold' : '' }}">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    Verifikasi Setoran
                                </a>
                            </li>
                            <li>
                                <a href="/kelola-pengguna" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('kelola-pengguna*') ? 'bg-forest text-white font-bold' : '' }}">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    Data Pengguna
                                </a>
                            </li>
                        @endif

                        <div class="my-3 border-t border-white/10"></div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-white/30 px-3 block mb-2">Keamanan</span>

                        <li>
                            <a href="{{ route('set-pin') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-white {{ request()->is('set-pin*') ? 'bg-forest text-white font-bold' : '' }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                Kelola PIN Transaksi
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- ================= BAGIAN BAWAH SIDEBAR: WIDGET INTEGRASI AKUN TOTAL ================= -->
                <div class="relative z-10 bg-white/5 p-4 rounded-2xl border border-white/5 flex flex-col gap-3">
                    <div class="flex items-center gap-3 truncate">
                        <!-- Foto Profil Real-time Sinkronisasi Komponen Kiri -->
                        <div class="avatar {{ $sidebarUser && $sidebarUser->foto_profil ? '' : 'placeholder' }} online shrink-0">
                            <div class="bg-white/10 text-cream rounded-full w-9 h-9 shadow-inner overflow-hidden flex items-center justify-center ring-2 ring-white/10">
                                @if($sidebarUser && $sidebarUser->foto_profil)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($sidebarUser->foto_profil) }}?v={{ time() }}" alt="Profil" class="w-full h-full object-cover" />
                                @else
                                    <span class="text-xs font-bold font-display">{{ strtoupper(substr(session('name', 'A'), 0, 1)) }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="truncate text-left flex-1">
                            <p class="text-xs font-bold text-cream truncate leading-tight">{{ session('name', 'User') }}</p>
                            <span class="text-[9px] bg-forest/20 text-forest-light font-extrabold px-1.5 py-0.5 rounded-md mt-0.5 inline-block uppercase tracking-wider">
                                {{ session('role', 'User') }}
                            </span>
                        </div>
                    </div>

                    <!-- Panel Aksi Terpadu Navigasi Utama & Fungsi Keluar Sesi -->
                    <div class="grid grid-cols-3 gap-1.5 pt-3 border-t border-white/10 text-center text-[11px] font-bold">
                        <a href="/" class="py-2 rounded-lg bg-white/5 hover:bg-white/10 text-cream transition-all flex items-center justify-center" title="Kembali ke Beranda Depan">
                            Beranda
                        </a>
                        <a href="/profile" class="py-2 rounded-lg bg-white/5 hover:bg-white/10 text-cream transition-all flex items-center justify-center {{ request()->is('profile*') ? 'bg-forest text-white' : '' }}" title="Kelola Profil & Password">
                            Profil
                        </a>
                        <form action="/logout" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="w-full py-2 rounded-lg bg-terracotta/20 hover:bg-terracotta text-white transition-all flex items-center justify-center" title="Keluar dari Sesi Aplikasi">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </aside>
        </div>
    </div>

</body>
</html>