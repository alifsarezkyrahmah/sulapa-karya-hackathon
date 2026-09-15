@extends('layouts.app', ['title' => 'Katalog Kriya — SulapaKarya'])

@section('content')
@php
    $currentUser = auth()->user() ?? \App\Models\User::find(session('user_id'));
    $userPoints = $currentUser->points_balance ?? 0;
    $isProPartner = (($currentUser->business_status ?? '') === 'approved');
    $proDiscountPercent = 10;
@endphp

<div class="max-w-7xl mx-auto space-y-6 sm:space-y-8 py-6 sm:py-10 px-4 sm:px-6 lg:px-8 text-left">
    
    <!-- ============ BANNER HEADER KATALOG ============ -->
    <div class="rounded-3xl bg-[#1C1A16] text-[#E5DFD5] border border-white/10 p-6 sm:p-10 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
        <div class="space-y-2 max-w-2xl">
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-semibold tracking-widest uppercase text-[#A8A095] border border-white/15 px-3 py-1 rounded-full">
                    Karya Daur Ulang Makassar
                </span>
                @if($isProPartner)
                    <span class="text-[10px] font-mono font-bold bg-amber-400 text-amber-950 px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                        Diskon PRO {{ $proDiscountPercent }}% Aktif
                    </span>
                @endif
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight">Katalog Produk Kriya</h1>
            <p class="text-xs sm:text-sm text-[#A8A095] leading-relaxed">
                Karya kerajinan berkualitas hasil olahan sampah terpilah oleh pengrajin lokal. Tukarkan simpanan poin sebagai potongan harga langsung.
            </p>
        </div>

        <!-- Box Saldo Poin -->
        <div class="bg-white/5 border border-white/10 backdrop-blur-sm p-4 sm:p-5 rounded-2xl text-left md:text-right w-full md:w-auto shrink-0 min-w-[180px]">
            <span class="text-[10px] font-bold font-mono uppercase tracking-wider text-[#A8A095] block">Simpanan Poin Kriya</span>
            <div class="flex items-baseline md:justify-end gap-1.5 mt-1">
                <span class="text-2xl sm:text-3xl font-black font-mono text-white">{{ number_format($userPoints, 0, ',', '.') }}</span>
                <span class="text-xs font-semibold text-[#A8A095]">Poin</span>
            </div>
            <span class="text-[10px] text-forest font-mono block mt-1">1 Poin = Rp 1 Potongan</span>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-forest/10 border border-forest/20 text-forest text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 rounded-2xl bg-terracotta/10 border border-terracotta/20 text-terracotta text-xs font-semibold">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- ============ SEARCH, FILTER & SORT BAR ============ -->
    <div class="bg-white border border-ink/10 rounded-3xl p-4 sm:p-5 space-y-4 shadow-xs">
        <form action="{{ url('/katalog') }}" method="GET" class="space-y-4 m-0">
            
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                <!-- Input Pencarian -->
                <div class="relative flex-1">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari karya kriya, wadah rotan, cinderamata..." 
                           class="input input-sm input-bordered w-full rounded-2xl bg-cream/20 pl-10 pr-4 text-xs font-medium text-ink focus:outline-none focus:border-forest h-11">
                    <svg class="w-4 h-4 text-ink-soft/60 absolute left-3.5 top-1/2 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    @if(request('q'))
                        <a href="{{ url('/katalog') }}" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-ink-soft hover:text-ink text-xs font-bold">✕</a>
                    @endif
                </div>

                <!-- Dropdown Urutan / Sorting -->
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs text-ink-soft font-semibold hidden sm:inline">Urutkan:</span>
                    <select name="sort" onchange="this.form.submit()" 
                            class="select select-sm select-bordered rounded-2xl bg-cream/20 text-xs font-bold text-ink focus:outline-none focus:border-forest h-11 w-full sm:w-auto">
                        <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                        <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="termahal" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="terpopuler" {{ request('sort') == 'terpopuler' ? 'selected' : '' }}>Stok Menipis</option>
                    </select>
                </div>
            </div>

            <!-- Tab Kategori Produk (Horizontal Scroll di HP) -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 pt-1 no-scrollbar text-xs">
                <a href="{{ request()->fullUrlWithQuery(['kategori' => 'semua']) }}" 
                   class="px-4 py-2 rounded-xl font-bold transition-colors whitespace-nowrap {{ (!request('kategori') || request('kategori') == 'semua') ? 'bg-forest text-white shadow-2xs' : 'bg-cream/40 text-ink-soft hover:bg-cream hover:text-ink' }}">
                    Semua Koleksi
                </a>
                
                @if(isset($categories))
                    @foreach($categories as $cat)
                        <a href="{{ request()->fullUrlWithQuery(['kategori' => $cat]) }}" 
                           class="px-4 py-2 rounded-xl font-bold transition-colors whitespace-nowrap {{ request('kategori') == $cat ? 'bg-forest text-white shadow-2xs' : 'bg-cream/40 text-ink-soft hover:bg-cream hover:text-ink' }}">
                            {{ $cat }}
                        </a>
                    @endforeach
                @endif
            </div>

        </form>
    </div>

    <!-- ============ DAFTAR PRODUK KRIYA ============ -->
    <div class="space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-ink/5">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-ink tracking-tight">Hasil Pencarian Produk</h2>
                <p class="text-xs text-ink-soft mt-0.5">
                    @if(request('q') || request('kategori'))
                        Menampilkan hasil untuk: 
                        @if(request('q')) <span class="font-bold text-ink">"{{ request('q') }}"</span> @endif
                        @if(request('kategori') && request('kategori') != 'semua') <span class="badge badge-xs bg-forest/10 text-forest font-bold font-mono">{{ request('kategori') }}</span> @endif
                    @else
                        Menampilkan semua produk kriya siap kirim Makassar.
                    @endif
                </p>
            </div>
            <span class="text-xs font-mono font-semibold text-ink-soft bg-cream px-3 py-1.5 rounded-xl border border-ink/5">
                {{ $allProducts->total() ?? count($allProducts) }} Item
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 sm:gap-6">
            @forelse($allProducts as $product)
                @php
                    $isLowStock = ($product->stock <= 5 && $product->stock > 0);
                    $finalPrice = $isProPartner ? round($product->price * 0.9) : $product->price;
                @endphp
                
                <!-- Card Produk Minimalis -->
                <div class="bg-white border border-ink/10 rounded-3xl overflow-hidden shadow-xs hover:shadow-md hover:border-forest/20 transition-all flex flex-col justify-between group">
                    <div>
                        <!-- Foto Produk -->
                        <div class="aspect-square bg-cream/30 overflow-hidden relative cursor-pointer" onclick="document.getElementById('detail_modal_{{ $product->id }}').showModal()">
                            @if($product->stock <= 0)
                                <div class="absolute inset-0 bg-ink/60 backdrop-blur-2xs flex items-center justify-center z-10">
                                    <span class="text-white text-[10px] font-bold font-mono uppercase tracking-wider bg-terracotta px-2.5 py-1 rounded-lg">Habis</span>
                                </div>
                            @elseif($isLowStock)
                                <span class="absolute top-3 left-3 bg-terracotta text-white text-[9px] font-mono font-bold px-2 py-0.5 rounded-md z-10 shadow-2xs">
                                    Sisa {{ $product->stock }}
                                </span>
                            @endif

                            @if($isProPartner)
                                <span class="absolute top-3 right-3 bg-amber-400 text-amber-950 text-[9px] font-mono font-black px-2 py-0.5 rounded-md z-10 shadow-2xs">
                                    -{{ $proDiscountPercent }}% PRO
                                </span>
                            @endif

                            @if($product->photo_path)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($product->photo_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full grid place-items-center text-forest/40 bg-sand/30 font-mono text-xl font-bold uppercase">
                                    {{ substr($product->name, 0, 2) }}
                                </div>
                            @endif
                        </div>

                        <!-- Keterangan Item -->
                        <div class="p-4 space-y-1">
                            <span class="text-[9px] font-mono font-bold uppercase tracking-wider text-ink-soft/70 block">
                                {{ $product->product_category ?? 'Kriya Daur Ulang' }}
                            </span>
                            <h3 class="font-bold text-ink text-sm leading-snug line-clamp-2 hover:text-forest transition-colors cursor-pointer" onclick="document.getElementById('detail_modal_{{ $product->id }}').showModal()">
                                {{ $product->name }}
                            </h3>
                            @if($product->material_source)
                                <span class="text-[10px] text-forest font-semibold block truncate">
                                    Bahan: {{ $product->material_source }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Baris Harga & Tombol -->
                    <div class="p-4 pt-2 border-t border-ink/5 mt-auto">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <div>
                                @if($isProPartner)
                                    <span class="text-[10px] text-ink-soft/60 line-through font-mono block">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    <span class="font-mono font-black text-forest text-base block">Rp {{ number_format($finalPrice, 0, ',', '.') }}</span>
                                @else
                                    <span class="font-mono font-black text-forest text-base block">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <span class="text-[10px] font-mono text-ink-soft">Stok: {{ $product->stock }}</span>
                        </div>

                        <button type="button" onclick="document.getElementById('detail_modal_{{ $product->id }}').showModal()" 
                            class="btn btn-xs w-full bg-cream hover:bg-forest hover:text-white text-ink border border-ink/10 rounded-xl font-bold h-8 transition-colors shadow-none">
                            Lihat Detail
                        </button>
                    </div>
                </div>

                <!-- MODAL DETAIL PRODUK -->
                <dialog id="detail_modal_{{ $product->id }}" class="modal modal-bottom sm:modal-middle">
                    <div class="modal-box bg-white max-w-md rounded-3xl border border-ink/10 p-6 text-left relative shadow-xl">
                        <form method="dialog">
                            <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft hover:bg-cream">✕</button>
                        </form>
                        
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-forest bg-forest/10 px-2 py-0.5 rounded">
                            Detail Karya Daur Ulang
                        </span>
                        
                        <div class="mt-3.5 space-y-4">
                            <!-- Foto di Modal -->
                            <div class="w-full aspect-square rounded-2xl overflow-hidden bg-cream/30 border border-ink/5">
                                @if($product->photo_path)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($product->photo_path) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full grid place-items-center text-forest/40 font-mono text-2xl font-bold uppercase">
                                        {{ substr($product->name, 0, 2) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Judul & Harga -->
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-mono font-bold bg-cream border border-ink/10 text-ink px-2 py-0.5 rounded uppercase">
                                        {{ $product->product_category ?? 'Kriya' }}
                                    </span>
                                    @if($isProPartner)
                                        <span class="text-[10px] font-mono font-bold bg-amber-100 text-amber-900 px-2 py-0.5 rounded uppercase">
                                            Diskon 10% Khusus PRO
                                        </span>
                                    @endif
                                </div>
                                <h4 class="font-bold text-lg text-ink mt-1.5 leading-snug">{{ $product->name }}</h4>
                                
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="font-mono font-black text-forest text-2xl">Rp {{ number_format($finalPrice, 0, ',', '.') }}</span>
                                    @if($isProPartner)
                                        <span class="text-xs text-ink-soft/60 line-through font-mono">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Deskripsi & Bahan -->
                            @if($product->description)
                                <div class="bg-cream/20 border border-ink/5 rounded-2xl p-3.5 space-y-1">
                                    <span class="text-[10px] font-bold text-ink-soft uppercase font-mono block">Deskripsi:</span>
                                    <p class="text-xs text-ink/80 leading-relaxed">{{ $product->description }}</p>
                                </div>
                            @endif

                            @if($product->material_source)
                                <div class="p-3 bg-forest/[0.04] border border-forest/15 rounded-2xl text-xs space-y-0.5">
                                    <span class="text-[10px] font-bold text-forest font-mono uppercase block">Asal Material Daur Ulang:</span>
                                    <span class="text-ink font-medium">{{ $product->material_source }}</span>
                                </div>
                            @endif

                            <!-- Ketersediaan Stok -->
                            <div class="text-xs text-ink-soft flex justify-between items-center bg-cream/30 p-3 rounded-xl border border-ink/5 font-medium">
                                <span>Status Stok:</span>
                                @if($product->stock > 0)
                                    <span class="text-forest font-bold font-mono">Tersedia ({{ $product->stock }} item)</span>
                                @else
                                    <span class="text-terracotta font-bold font-mono">Stok Habis</span>
                                @endif
                            </div>

                            <!-- Tombol Tambah ke Keranjang -->
                            @if($product->stock > 0)
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="w-full pt-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm w-full bg-forest hover:bg-forest-dark border-none text-white rounded-xl font-bold h-11 shadow-sm text-xs">
                                        + Tambahkan ke Keranjang
                                    </button>
                                </form>
                            @else
                                <button disabled class="btn btn-sm w-full bg-gray-200 border-none text-gray-400 rounded-xl font-bold h-11 cursor-not-allowed text-xs">
                                    Produk Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                    <form method="dialog" class="modal-backdrop bg-ink/40 backdrop-blur-xs"><button>close</button></form>
                </dialog>

            @empty
                <div class="col-span-full py-16 text-center text-ink-soft/60 text-xs border border-ink/5 rounded-3xl bg-white space-y-2">
                    <p class="font-semibold text-sm text-ink">Tidak ada produk kriya yang cocok dengan pencarian.</p>
                    <a href="{{ url('/katalog') }}" class="btn btn-xs bg-forest text-white rounded-xl font-bold px-4 h-8 border-none inline-flex">
                        Reset Filter
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Paginasi (Jika Menggunakan Pagination) -->
        @if(method_exists($allProducts, 'links'))
            <div class="pt-6">
                {{ $allProducts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection