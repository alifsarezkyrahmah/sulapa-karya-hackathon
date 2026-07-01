@extends('layouts.app', ['title' => 'Katalog Kriya — SulapaKarya Macca'])

@section('content')
@php
    // Mengambil data user secara utuh untuk poin dan detail pengiriman
    $currentUser = \App\Models\User::find(session('user_id'));
    $userPoints = $currentUser->points_balance ?? 0;
    
    // LOGIKA PENGECEKAN ALAMAT
    $hasAddress = !empty($currentUser->address); 
@endphp

<div class="max-w-6xl mx-auto space-y-10 animate-fadeIn py-8 px-4 sm:px-6">
    
    <!-- ============ HEADER KATALOG ============ -->
    <div class="text-left bg-gradient-to-r from-maritime to-maritime-dark p-8 rounded-[2rem] text-white shadow-lg shadow-maritime/20 flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden">
        <div class="absolute inset-0 dot-grid text-white/[0.05] pointer-events-none"></div>
        <div class="relative z-10 max-w-2xl">
            <h1 class="font-display font-extrabold text-3xl tracking-tight">Katalog Produk Kriya</h1>
            <p class="text-sm text-maritime-light font-medium mt-2 leading-relaxed">Beli produk ramah lingkungan. Anda bisa menggunakan <b>Poin Kriya</b> Anda sebagai potongan harga langsung!</p>
        </div>
        <div class="relative z-10 shrink-0 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 text-center min-w-[160px]">
            <span class="text-[10px] font-bold uppercase tracking-widest text-maritime-light block mb-1">Saldo Poin Kriya</span>
            <span class="text-2xl font-mono font-extrabold text-white">🌟 {{ number_format($userPoints, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- NOTIFIKASI SUKSES / ERROR -->
    @if(session('success'))
        <div class="alert alert-success bg-forest/10 border-forest/20 text-forest rounded-2xl text-sm font-bold p-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error bg-terracotta/10 border-terracotta/20 text-terracotta rounded-2xl text-sm font-bold p-4">{{ $errors->first() }}</div>
    @endif

    <!-- ============ DAFTAR PRODUK KRIYA ============ -->
    <div>
        <h2 class="font-display font-extrabold text-xl text-ink mb-4">Daftar Produk Kriya</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($allProducts as $product)
                <!-- Card Product -->
                <div onclick="document.getElementById('detail_modal_{{ $product->id }}').showModal()" class="bg-white border border-ink/5 rounded-[1.5rem] overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col cursor-pointer group">
                    <div class="relative h-40 overflow-hidden bg-cream/50">
                        @if($product->stock <= 5)
                            <span class="absolute top-3 left-3 bg-terracotta/90 text-white text-[9px] font-extrabold px-2 py-1 rounded-md z-10">Sisa {{ $product->stock }}!</span>
                        @endif
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($product->photo_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300" />
                    </div>
                    <div class="p-4 flex flex-col flex-1">
                        <span class="text-[9px] font-bold text-ink-soft/60 uppercase">{{ $product->product_category }}</span>
                        <h3 class="font-bold text-ink text-sm mt-1 mb-2 line-clamp-2 flex-1">{{ $product->name }}</h3>
                        
                        <div class="flex items-end justify-between border-t border-ink/5 pt-3 mt-auto">
                            <div class="font-mono font-extrabold text-forest text-base">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </div>
                            <span class="btn btn-xs bg-maritime border-none text-white hover:bg-maritime-dark rounded-lg px-3 shadow-sm font-bold normal-case">
                                Detail
                            </span>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- MODAL 1: DETAIL PRODUK -->
                <!-- ========================================== -->
                <dialog id="detail_modal_{{ $product->id }}" class="modal modal-bottom sm:modal-middle">
                    <div class="modal-box bg-white max-w-md rounded-[2rem] border border-ink/5 p-6 text-left relative">
                        <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft">✕</button></form>
                        
                        <h3 class="font-display font-extrabold text-xl text-ink border-b border-ink/5 pb-3">Detail Hasil Karya</h3>
                        
                        <div class="mt-4 space-y-4">
                            <!-- Foto Produk -->
                            <div class="w-full h-48 rounded-2xl overflow-hidden bg-cream/30 border border-ink/5">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($product->photo_path) }}" class="w-full h-full object-cover">
                            </div>

                            <!-- Judul & Kategori -->
                            <div>
                                <span class="text-[10px] font-extrabold bg-maritime/10 text-maritime px-2 py-1 rounded-md uppercase tracking-wider">{{ $product->product_category }}</span>
                                <h4 class="font-display font-extrabold text-lg text-ink mt-2 leading-snug">{{ $product->name }}</h4>
                                <p class="font-mono font-black text-forest text-xl mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>

                            <!-- Deskripsi Produk -->
                            @if($product->description)
                                <div class="bg-cream/10 border border-ink/5 rounded-xl p-3">
                                    <span class="text-[10px] font-bold text-ink-soft block mb-1">Deskripsi Produk:</span>
                                    <p class="text-xs text-ink-soft/90 leading-relaxed">{{ $product->description }}</p>
                                </div>
                            @endif

                            <!-- Sumber Material -->
                            @if($product->material_source)
                                <div class="bg-forest/5 border border-forest/10 p-3 rounded-xl flex items-center gap-2">
                                    <span class="text-base">🌱</span>
                                    <div>
                                        <span class="text-[9px] font-bold text-forest block">Asal Bahan Baku Daur Ulang:</span>
                                        <p class="text-xs font-semibold text-ink-soft">{{ $product->material_source }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Info Stok -->
                            <div class="text-xs font-semibold text-ink-soft flex justify-between items-center bg-gray-50 p-3 rounded-xl">
                                <span>Status Ketersediaan:</span>
                                <span class="text-forest font-bold">✓ Tersedia (Stok: {{ $product->stock }} item)</span>
                            </div>

                            <button type="button" onclick="document.getElementById('detail_modal_{{ $product->id }}').close(); document.getElementById('buy_modal_{{ $product->id }}').showModal();" class="btn w-full bg-forest hover:bg-forest-dark border-none text-white rounded-xl font-extrabold normal-case shadow-md shadow-forest/20 mt-2 h-12">
                                Beli & Tukar Poin Sekarang
                            </button>
                        </div>
                    </div>
                    <form method="dialog" class="modal-backdrop bg-ink/30 backdrop-blur-sm"><button>close</button></form>
                </dialog>


                <!-- ========================================== -->
                <!-- MODAL 2: RINCIAN PEMBAYARAN (CEK ALAMAT) -->
                <!-- ========================================== -->
                <dialog id="buy_modal_{{ $product->id }}" class="modal modal-bottom sm:modal-middle">
                    <div class="modal-box bg-white max-w-md rounded-[2rem] border border-ink/5 p-6 text-left relative">
                        <button type="button" onclick="document.getElementById('buy_modal_{{ $product->id }}').close(); document.getElementById('detail_modal_{{ $product->id }}').showModal();" class="btn btn-sm btn-circle btn-ghost absolute left-4 top-4 text-ink-soft">←</button>
                        <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft">✕</button></form>
                        
                        <h3 class="font-display font-extrabold text-xl text-ink border-b border-ink/5 pb-3 text-center">Konfirmasi Pesanan</h3>
                        
                        <form action="{{ route('checkout.process') }}" method="POST" class="mt-4 space-y-5">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <!-- 1. INFORMASI PENGIRIMAN -->
                            <div class="space-y-2">
                                <h4 class="font-bold text-xs text-ink-soft uppercase tracking-wider">Alamat Tujuan (Profil)</h4>
                                <div class="bg-cream/20 border border-ink/5 rounded-xl p-4 text-sm space-y-2.5">
                                    <div class="flex justify-between items-start gap-4">
                                        <span class="text-ink-soft font-medium w-1/3">Penerima</span>
                                        <span class="font-bold text-ink text-right">{{ $currentUser->name ?? 'Belum Diatur' }}</span>
                                    </div>
                                    <div class="flex justify-between items-start gap-4">
                                        <span class="text-ink-soft font-medium w-1/3">No. Telepon</span>
                                        <span class="font-bold text-ink text-right">{{ $currentUser->phone ?? 'Belum Diatur' }}</span>
                                    </div>
                                    
                                    <div class="flex flex-col border-t border-ink/5 pt-2.5 mt-2.5">
                                        <span class="text-ink-soft font-medium mb-1">Alamat Lengkap</span>
                                        <!-- TAMPILAN BERUBAH JIKA ALAMAT KOSONG -->
                                        @if($hasAddress)
                                            <span class="font-bold text-ink text-xs leading-snug">{{ $currentUser->address }}</span>
                                        @else
                                            <div class="bg-terracotta/10 border border-terracotta/20 rounded-lg p-2.5 mt-1">
                                                <span class="font-bold text-terracotta text-xs leading-snug flex items-center gap-2">
                                                    ⚠️ Alamat kosong! Anda wajib melengkapinya.
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 2. RINGKASAN PRODUK -->
                            <div class="space-y-2">
                                <h4 class="font-bold text-xs text-ink-soft uppercase tracking-wider">Item Yang Dipilih</h4>
                                <div class="flex gap-4 items-center bg-cream/30 p-3 rounded-xl border border-ink/5">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($product->photo_path) }}" class="w-14 h-14 rounded-lg object-cover shadow-sm border border-ink/10">
                                    <div>
                                        <h4 class="font-bold text-xs text-ink line-clamp-1">{{ $product->name }}</h4>
                                        <span class="font-mono font-bold text-forest text-xs">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            @php
                                $maxDiscount = min($product->price, $userPoints);
                            @endphp
                            
                            <!-- 3. AKTIVASI DISKON POIN -->
                            <div class="form-control bg-maritime/5 p-4 rounded-xl border border-maritime/10">
                                <label class="cursor-pointer label p-0 justify-start gap-3">
                                    <input type="checkbox" name="use_points" value="1" 
                                        onchange="kalkulasiTotal(this, {{ $product->price }}, {{ $maxDiscount }}, 'display_total_{{ $product->id }}')" 
                                        class="checkbox checkbox-sm border-maritime checked:border-maritime [--chkbg:theme(colors.maritime.DEFAULT)]" 
                                        {{ ($maxDiscount == 0 || !$hasAddress) ? 'disabled' : '' }} />
                                    <div>
                                        <span class="label-text font-bold text-xs text-ink block">Gunakan Poin Kriya</span>
                                        @if($maxDiscount > 0)
                                            <span class="text-[10px] text-maritime font-medium font-mono">Diskon Poin: -Rp {{ number_format($maxDiscount, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-[10px] text-terracotta font-medium">Saldo Poin tidak mencukupi</span>
                                        @endif
                                    </div>
                                </label>
                            </div>

                            <div class="flex justify-between items-center border-t border-ink/5 pt-4">
                                <span class="font-bold text-xs text-ink-soft">Total Pembayaran:</span>
                                <span class="font-display font-extrabold text-2xl text-ink">Rp <span id="display_total_{{ $product->id }}">{{ number_format($product->price, 0, ',', '.') }}</span></span>
                            </div>

                            <!-- LOGIKA TOMBOL CHECKOUT (DIBLOKIR JIKA ALAMAT KOSONG) -->
                            @if($hasAddress)
                                <button type="submit" class="btn w-full bg-forest hover:bg-forest-dark border-none text-white rounded-xl font-bold normal-case shadow-md shadow-forest/20 mt-4 h-12">
                                    Lanjut Ke Kasir Midtrans
                                </button>
                            @else
                                <a href="{{ route('profile.index') }}" class="btn w-full bg-terracotta hover:bg-red-600 border-none text-white rounded-xl font-bold normal-case shadow-md shadow-terracotta/20 mt-4 h-12 flex items-center justify-center gap-2">
                                    Isi Alamat di Profil Terlebih Dahulu
                                </a>
                            @endif
                        </form>
                    </div>
                    <form method="dialog" class="modal-backdrop bg-ink/30 backdrop-blur-sm"><button>close</button></form>
                </dialog>

            @empty
                <div class="col-span-full py-16 text-center text-ink-soft text-sm">Belum ada produk kriya yang tersedia.</div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function kalkulasiTotal(checkbox, hargaAsli, maxDiskon, idDisplay) {
        let total = hargaAsli;
        if (checkbox.checked) {
            total = hargaAsli - maxDiskon;
        }
        document.getElementById(idDisplay).innerText = new Intl.NumberFormat('id-ID').format(total);
    }
</script>
@endsection