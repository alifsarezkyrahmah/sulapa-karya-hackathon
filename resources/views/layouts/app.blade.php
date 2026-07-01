<!DOCTYPE html>
<html lang="id" data-theme="sulapakarya">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sulapa Karya Macca' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind & DaisyUI CDN (Solusi Instan Agar Komponen Tidak Pecah) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css">

    <!-- Tailwind Custom Configuration (Wajib untuk mapping warna kriya) -->
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
        body { background-color: #FAF6EF; }
        .tag-stitch {
            border: 1.5px dashed currentColor;
            border-radius: 999px;
        }
        .dot-grid {
            background-image: radial-gradient(currentColor 1.4px, transparent 1.4px);
            background-size: 18px 18px;
        }
    </style>
</head>

<body class="font-body text-ink antialiased bg-[#FAF6EF] min-h-screen flex flex-col justify-between">

    <!-- 1. FLOATING NAVBAR (Hanya muncul jika BUKAN di halaman login atau register) -->
    @if (!request()->is('login*') && !request()->is('register*'))
        @include('components.navbar')
    @endif

    <!-- 2. ALERT TOAST (Hanya muncul jika ada session success atau error) -->
    @include('components.alert')


    <!-- 2. MAIN CONTENT AREA -->
    <main class="flex-1 w-full">
        @yield('content')
    </main>

    <!-- 3. FOOTER -->
    @include('components.footer')

</body>
</html>