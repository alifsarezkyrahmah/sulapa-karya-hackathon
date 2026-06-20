@extends('layouts.app', ['title' => 'Daftar Akun — SulapaKarya Macca'])

@section('content')
<div class="min-h-[calc(100vh-5rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-cream/30">
    
    <div class="max-w-5xl w-full bg-white rounded-[2rem] shadow-xl shadow-ink/[0.03] overflow-hidden grid md:grid-cols-2 border border-ink/5 min-h-[650px]">
        
        <div class="relative hidden md:flex flex-col justify-between p-12 text-white overflow-hidden bg-gradient-to-br from-forest to-forest-dark">
            <div class="absolute inset-0 dot-grid text-white/[0.04] pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-sand/10 blur-[80px] rounded-full pointer-events-none"></div>
            <div class="absolute -top-20 -right-20 w-72 h-72 bg-terracotta/20 blur-[80px] rounded-full pointer-events-none"></div>

            <div class="relative z-10 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-white/20 backdrop-blur-md flex items-center justify-center">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 20A7 7 0 0 1 4 13c0-4 3-8 8-9 0 0 1 5-1 9 2-3 4-4 7-4 0 5-4 9-8 9 0 0-1 1-1 2z"/></svg>
                </div>
                <span class="font-display font-bold tracking-tight text-sm text-white">SulapaKarya Macca</span>
            </div>

            <div class="relative z-10 space-y-3 mt-auto">
                <span class="tag-stitch inline-block border-white/30 px-3 py-1 text-[10px] uppercase tracking-widest font-bold bg-white/5 backdrop-blur-sm text-sand">
                    Komunitas Makassar
                </span>
                <p class="font-display font-bold text-2xl lg:text-3xl leading-snug text-cream">
                    “Ubah sampah jadi karya, berdayakan pengrajin lokal.”
                </p>
                <p class="text-xs text-cream/70 max-w-sm leading-relaxed">
                    Setiap aksi pilah sampah yang Anda lakukan berkontribusi langsung pada kelestarian lingkungan hidup dan kesejahteraan UMKM kriya kota kita.
                </p>
            </div>
        </div>

        <div class="p-8 sm:p-12 flex flex-col justify-center bg-white">
            
                       <!-- TOMBOL KEMBALI KE ROUTE / (BERANDA) -->
            <div class="mb-6 md:mb-6 -mt-2 md:-mt-6 text-left">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-ink-soft/70 hover:text-forest transition-all group">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="transition-transform group-hover:-translate-x-1">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Beranda
                </a>
            </div> 
            <div class="mb-6">
                <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-ink tracking-tight">
                    Mulai Langkah Hijau Anda
                </h2>
                <p class="text-sm text-ink-soft/80 mt-2 font-medium">
                    Gabung dalam gerakan penyelamatan lingkungan
                </p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error bg-terracotta/10 border-terracotta/20 text-terracotta rounded-xl py-3 px-4 mb-5 text-sm font-semibold flex gap-2">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>
                    <span>Ada beberapa format yang tidak sesuai. Silakan periksa kembali formulir Anda.</span>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="form-control w-full">
                    <label class="label py-1">
                        <span class="label-text font-bold text-xs @error('name') text-terracotta @else text-ink/70 @enderror">Nama Lengkap</span>
                    </label>
                    <input type="text" name="name" placeholder="Masukkan nama lengkap Anda" required value="{{ old('name') }}"
                        class="input input-bordered w-full rounded-xl text-sm transition-all h-11 @error('name') input-error border-terracotta focus:border-terracotta bg-terracotta/[0.02] @else border-ink/10 focus:border-forest focus:outline-none bg-cream/[0.15] @enderror" />
                    @error('name')
                        <span class="text-xs text-terracotta font-semibold mt-1 flex gap-1 items-center"><span class="w-1 h-1 rounded-full bg-terracotta"></span> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control w-full">
                    <label class="label py-1">
                        <span class="label-text font-bold text-xs @error('email') text-terracotta @else text-ink/70 @enderror">Alamat Email</span>
                    </label>
                    <input type="email" name="email" placeholder="user@gmail.com" required value="{{ old('email') }}"
                        class="input input-bordered w-full rounded-xl text-sm transition-all h-11 @error('email') input-error border-terracotta focus:border-terracotta bg-terracotta/[0.02] @else border-ink/10 focus:border-forest focus:outline-none bg-cream/[0.15] @enderror" />
                    @error('email')
                        <span class="text-xs text-terracotta font-semibold mt-1 flex gap-1 items-center"><span class="w-1 h-1 rounded-full bg-terracotta"></span> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control w-full">
                    <label class="label py-1">
                        <span class="label-text font-bold text-xs @error('phone') text-terracotta @else text-ink/70 @enderror">Nomor HP</span>
                    </label>
                    <input type="tel" name="phone" placeholder="Contoh: 081234567890" required value="{{ old('phone') }}"
                        class="input input-bordered w-full rounded-xl text-sm transition-all h-11 @error('phone') input-error border-terracotta focus:border-terracotta bg-terracotta/[0.02] @else border-ink/10 focus:border-forest focus:outline-none bg-cream/[0.15] @enderror" />
                    @error('phone')
                        <span class="text-xs text-terracotta font-semibold mt-1 flex gap-1 items-center"><span class="w-1 h-1 rounded-full bg-terracotta"></span> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control w-full">
                    <label class="label py-1">
                        <span class="label-text font-bold text-xs @error('password') text-terracotta @else text-ink/70 @enderror">Password</span>
                    </label>
                    <input type="password" name="password" placeholder="••••••••" required
                        class="input input-bordered w-full rounded-xl text-sm transition-all h-11 @error('password') input-error border-terracotta focus:border-terracotta bg-terracotta/[0.02] @else border-ink/10 focus:border-forest focus:outline-none bg-cream/[0.15] @enderror" />
                    @error('password')
                        <span class="text-xs text-terracotta font-semibold mt-1 flex gap-1 items-center"><span class="w-1 h-1 rounded-full bg-terracotta"></span> {{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="btn w-full bg-gradient-to-r from-forest to-forest-dark hover:from-forest-dark hover:to-forest text-white border-none rounded-xl h-11 min-h-[2.75rem] font-bold text-sm tracking-wide shadow-md shadow-forest/10 normal-case transition-all">
                        Daftar Sekarang
                    </button>
                </div>
            </form>

            <div class="divider text-[10px] font-extrabold tracking-widest text-ink/30 my-5">ATAU</div>

            <div class="mb-6">
                <a href="{{ route('auth.google') }}" class="btn btn-outline w-full border-ink/10 hover:bg-ink/5 text-ink rounded-xl h-11 min-h-[2.75rem] font-bold text-sm normal-case flex items-center justify-center gap-3 transition-all">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Daftar dengan Google
                </a>
            </div>

            <div class="text-center text-sm font-medium">
                <span class="text-ink-soft">Sudah punya akun? </span>
                <a href="{{ route('login') }}" class="text-maritime font-bold hover:underline underline-offset-4 transition-all">
                    Masuk di sini
                </a>
            </div>

        </div>

    </div>
</div>
@endsection