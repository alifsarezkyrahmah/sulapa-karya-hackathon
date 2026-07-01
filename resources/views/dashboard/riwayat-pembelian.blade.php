@extends('layouts.dashboard', ['title' => 'Riwayat Pembelian Kriya — SulapaKarya'])

@section('dashboard-content')
<div class="space-y-6 animate-fadeIn">
    
    <div class="text-left">
        <h1 class="font-display font-extrabold text-2xl text-ink tracking-tight">Riwayat Pembelian Kriya</h1>
        <p class="text-xs text-ink-soft/80 font-medium mt-1">Daftar produk upcycling hasil karya UMKM Makassar yang telah Anda pesan.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-forest/10 border-forest/20 text-forest rounded-2xl text-xs font-bold p-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-error bg-terracotta/10 border-terracotta/20 text-terracotta rounded-2xl text-xs font-bold p-4 shadow-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white border border-ink/5 rounded-[1.5rem] p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto rounded-xl border border-ink/5">
            <table class="table w-full text-sm">
                <thead>
                    <tr class="border-b border-ink/5 text-ink/70 font-bold uppercase tracking-wider text-xs bg-cream/60">
                        <th class="py-3.5 pl-5">Tanggal & No. Pesanan</th>
                        <th class="py-3.5">Produk Kriya</th>
                        <th class="py-3.5">Skema Potongan Harga</th>
                        <th class="py-3.5 text-center">Status</th>
                        <th class="py-3.5 pr-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="font-medium text-ink/90">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-cream/10 border-b border-ink/5 transition-colors">
                            
                            <td class="py-4 pl-5">
                                <span class="text-ink-soft font-bold block">{{ $t->created_at->translatedFormat('d M Y') }}</span>
                                <span class="text-[10px] text-ink-soft/60 font-mono mt-0.5 block tracking-wide">{{ $t->order_id }}</span>
                            </td>

                            <td class="py-4">
                                <div class="flex items-center gap-3">
                                    <div class="avatar">
                                        <div class="w-10 h-10 rounded-lg border border-ink/10 overflow-hidden bg-sand/20 shadow-sm">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($t->product->photo_path) }}" alt="Foto Produk" class="object-cover w-full h-full" />
                                        </div>
                                    </div>
                                    <div class="text-left max-w-[180px]">
                                        <p class="font-bold text-ink truncate leading-tight">{{ $t->product->name }}</p>
                                        <p class="text-[10px] text-ink-soft/70 mt-0.5 uppercase tracking-wider font-semibold">{{ $t->product->product_category }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 font-mono text-xs">
                                <div class="space-y-0.5 text-left">
                                    <div class="flex justify-between max-w-[150px]">
                                        <span class="text-gray-400">Harga:</span>
                                        <span class="text-ink-soft">Rp {{ number_format($t->original_price, 0, ',', '.') }}</span>
                                    </div>
                                    @if($t->points_used > 0)
                                        <div class="flex justify-between max-w-[150px] text-maritime font-bold">
                                            <span>Koin:</span>
                                            <span>-Rp {{ number_format($t->points_used, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between max-w-[150px] text-forest font-bold border-t border-dashed border-ink/10 pt-0.5 mt-0.5">
                                        <span>Bayar:</span>
                                        <span>Rp {{ number_format($t->final_price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 text-center">
                                @if($t->status === 'pending')
                                    <span class="badge bg-amber-100 border-none text-amber-700 text-[11px] font-bold px-2.5 py-2 rounded-lg shadow-inner">Belum Bayar</span>
                                @elseif($t->status === 'success')
                                    <span class="badge bg-forest/20 border-none text-forest-dark text-[11px] font-bold px-2.5 py-2 rounded-lg shadow-inner">✓ Lunas</span>
                                @elseif($t->status === 'failed')
                                    <span class="badge bg-terracotta/10 border-none text-terracotta text-[11px] font-bold px-2.5 py-2 rounded-lg shadow-inner">Gagal</span>
                                @endif
                            </td>

                            <td class="py-4 pr-5 text-center">
                                @if($t->status === 'pending')
                                    <a href="{{ route('checkout.resume', $t->order_id) }}" class="btn btn-xs bg-amber-500 border-none text-white hover:bg-amber-600 rounded-lg px-3 font-bold shadow-sm normal-case">
                                        💳 Bayar Sekarang
                                    </a>
                                @else
                                    <span class="text-xs text-ink-soft/40 italic font-normal">Tidak ada aksi</span>
                                @endif
                            </td>
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-ink-soft/60 font-medium text-xs">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="text-2xl opacity-40">🛍️</span>
                                    <p>Anda belum pernah melakukan pembelian produk kriya.</p>
                                    <a href="{{ route('user.katalog') }}" class="text-maritime underline mt-1 font-bold">Buka Katalog Produk</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection