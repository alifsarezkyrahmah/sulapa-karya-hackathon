@extends('layouts.dashboard', ['title' => 'Kelola Produk Kriya — SulapaKarya'])

@section('dashboard-content')
<div class="space-y-6 text-left">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-ink/5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">Katalog Produk Kriya</h1>
            <p class="text-xs text-ink-soft mt-0.5">Kelola inventaris hasil karya daur ulang (upcycling) mitra artisan dan pantau riwayat penjualan.</p>
        </div>
        <button type="button" onclick="document.getElementById('add_product_modal').showModal()" 
            class="btn btn-sm bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold px-4 h-10 shadow-sm transition-all w-full sm:w-auto">
            <svg class="w-4 h-4 mr-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Upload Produk Baru
        </button>
    </div>

    <!-- Alert Notifikasi -->
    @if($errors->any())
        <div class="alert alert-error bg-terracotta/10 border border-terracotta/20 text-terracotta rounded-2xl text-xs font-semibold p-4 shadow-none">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success bg-forest/10 border border-forest/20 text-forest rounded-2xl text-xs font-semibold p-4 shadow-none">
            {{ session('success') }}
        </div>
    @endif

    <!-- Toolbar Pencarian & Filter -->
    <div class="bg-white border border-ink/5 rounded-2xl p-4 shadow-none">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs">
            <!-- Pencarian Kata Kunci -->
            <div class="sm:col-span-5 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk atau material..." 
                    class="input input-bordered input-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 pl-9 text-ink font-medium">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-ink-soft" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>

            <!-- Filter Kategori -->
            <div class="sm:col-span-3">
                <select name="category" onchange="this.form.submit()" class="select select-bordered select-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 font-semibold text-ink">
                    <option value="">Semua Kategori</option>
                    <option value="Tas & Dompet" {{ request('category') == 'Tas & Dompet' ? 'selected' : '' }}>Tas & Dompet</option>
                    <option value="Dekorasi Rumah" {{ request('category') == 'Dekorasi Rumah' ? 'selected' : '' }}>Dekorasi Rumah</option>
                    <option value="Aksesoris Diri" {{ request('category') == 'Aksesoris Diri' ? 'selected' : '' }}>Aksesoris Diri</option>
                    <option value="Perlengkapan" {{ request('category') == 'Perlengkapan' ? 'selected' : '' }}>Perlengkapan Lainnya</option>
                </select>
            </div>

            <!-- Filter Status -->
            <div class="sm:col-span-2">
                <select name="status" onchange="this.form.submit()" class="select select-bordered select-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 font-semibold text-ink">
                    <option value="">Semua Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                    <option value="sold_out" {{ request('status') == 'sold_out' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="btn btn-sm bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold flex-1 h-9 shadow-none">
                    Terapkan
                </button>
                @if(request()->filled('search') || request()->filled('category') || request()->filled('status'))
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm bg-white hover:bg-cream text-ink border border-ink/10 rounded-xl text-xs font-semibold h-9 px-3 shadow-none">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. TAMPILAN MOBILE / TABLET (< md): KARTU LIST PRODUK -->
    <!-- ========================================================================= -->
    <div class="space-y-3 md:hidden">
        @forelse($products as $p)
            @php
                $allTransactions = \App\Models\Transaction::where('product_id', $p->id)->get();
                $totalSold = 0;
                foreach($allTransactions as $t) {
                    $st = strtolower($t->status ?? '');
                    if(str_contains($st, 'siap') || str_contains($st, 'paid') || str_contains($st, 'success') || str_contains($st, 'picked')) {
                        $totalSold += ($t->quantity ?? 1);
                    }
                }
            @endphp
            <div class="bg-white border border-ink/5 rounded-2xl p-4 shadow-none space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-cream/30 border border-ink/10 shrink-0">
                        @if(!empty($p->photo_path))
                            <img src="{{ asset('storage/' . $p->photo_path) }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-ink-soft/40 font-mono text-[10px]">No Pic</div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-1">
                            <span class="font-bold text-ink text-xs truncate">{{ $p->name }}</span>
                            @if($p->is_featured)
                                <span class="badge bg-amber-50 text-amber-700 border-none text-[9px] font-bold px-1.5 py-0.2 rounded">Unggulan</span>
                            @endif
                        </div>
                        <span class="text-[10px] text-ink-soft block mt-0.5">{{ $p->product_category }}</span>
                        <span class="font-mono font-bold text-forest text-xs block mt-1">Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 bg-cream/30 p-2.5 rounded-xl border border-ink/5 text-center text-xs">
                    <div>
                        <span class="text-[9px] text-ink-soft uppercase font-bold block">Sisa Stok</span>
                        <span class="font-mono font-bold text-ink text-xs">{{ $p->stock }} item</span>
                    </div>
                    <div>
                        <span class="text-[9px] text-ink-soft uppercase font-bold block">Terjual</span>
                        <span class="font-mono font-bold text-maritime text-xs">{{ $totalSold }} pcs</span>
                    </div>
                    <div>
                        <span class="text-[9px] text-ink-soft uppercase font-bold block">Status</span>
                        @if(strtolower($p->status) === 'available' && $p->stock > 0)
                            <span class="inline-flex items-center whitespace-nowrap bg-forest/10 text-forest text-[10px] font-bold px-1.5 py-0.5 rounded">Tersedia</span>
                        @else
                            <span class="inline-flex items-center whitespace-nowrap bg-terracotta/10 text-terracotta text-[10px] font-bold px-1.5 py-0.5 rounded">Habis</span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1 border-t border-ink/5">
                    <span class="text-[10px] text-ink-soft font-mono">Diupload: {{ $p->created_at ? $p->created_at->format('d M Y') : '-' }}</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="document.getElementById('edit_product_modal_{{ $p->id }}').showModal()"
                            class="btn btn-xs bg-forest hover:bg-forest-dark text-white border-none rounded-lg text-[10px] font-semibold px-2.5 shadow-none">
                            Detail / Edit
                        </button>
                        <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ $p->name }}?')" class="inline m-0 p-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs bg-white hover:bg-terracotta/10 text-terracotta border border-terracotta/30 rounded-lg text-[10px] font-semibold px-2 shadow-none">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-8 text-center text-ink-soft/60 text-xs border border-ink/5">
                Belum ada produk kriya yang terdaftar.
            </div>
        @endforelse
    </div>

    <!-- ========================================================================= -->
    <!-- 2. TAMPILAN DESKTOP (>= md): TABEL LEBAR -->
    <!-- ========================================================================= -->
    <div class="hidden md:block bg-white border border-ink/5 rounded-2xl p-6 shadow-none">
        <div class="overflow-x-auto">
            <table class="table w-full text-xs">
                <thead>
                    <tr class="bg-cream/40 text-ink-soft border-b border-ink/5 font-semibold text-[10px] uppercase">
                        <th class="py-3 pl-3">Produk & Material</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3">Harga Produk (IDR)</th>
                        <th class="py-3 text-center">Stok</th>
                        <th class="py-3 text-center font-bold text-maritime">Terjual</th>
                        <th class="py-3 text-center min-w-[100px]">Status</th>
                        <th class="py-3 pr-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/5 font-medium">
                    @forelse($products as $p)
                        @php
                            $allTransactions = \App\Models\Transaction::where('product_id', $p->id)->get();
                            $totalSold = 0;
                            foreach($allTransactions as $t) {
                                $st = strtolower($t->status ?? '');
                                if(str_contains($st, 'siap') || str_contains($st, 'paid') || str_contains($st, 'success') || str_contains($st, 'picked')) {
                                    $totalSold += ($t->quantity ?? 1);
                                }
                            }
                        @endphp
                        <tr class="hover:bg-cream/20 transition-colors">
                            <td class="py-3.5 pl-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl overflow-hidden bg-cream/40 border border-ink/10 shrink-0">
                                        @if(!empty($p->photo_path))
                                            <img src="{{ asset('storage/' . $p->photo_path) }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-ink-soft/40 font-mono text-[9px]">No Pic</div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 max-w-[200px]">
                                        <span class="font-bold text-ink block text-xs truncate flex items-center gap-1">
                                            {{ $p->name }}
                                            @if($p->is_featured)
                                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0" title="Produk Unggulan"></span>
                                            @endif
                                        </span>
                                        <span class="text-[10px] text-ink-soft block truncate">
                                            {{ $p->material_source ? 'Bahan: ' . $p->material_source : 'Diupload ' . ($p->created_at ? $p->created_at->format('d/m/y') : '-') }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5">
                                <span class="badge bg-cream border border-ink/10 text-ink text-[10px] font-semibold px-2 py-0.5 rounded capitalize">
                                    {{ $p->product_category }}
                                </span>
                            </td>

                            <td class="py-3.5 font-mono font-bold text-forest text-xs">
                                Rp {{ number_format($p->price, 0, ',', '.') }}
                            </td>

                            <td class="py-3.5 text-center font-mono font-semibold text-ink">
                                {{ $p->stock }} <span class="text-[10px] font-sans text-ink-soft">item</span>
                            </td>

                            <td class="py-3.5 text-center font-mono font-bold text-maritime">
                                {{ $totalSold }} <span class="text-[10px] font-sans font-normal text-ink-soft">terjual</span>
                            </td>

                            <td class="py-3.5 text-center min-w-[100px]">
                                @if(strtolower($p->status) === 'available' && $p->stock > 0)
                                    <span class="inline-flex items-center justify-center whitespace-nowrap bg-forest/10 text-forest border border-forest/20 text-[10px] font-bold px-2.5 py-1 rounded-md">
                                        Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center whitespace-nowrap bg-terracotta/10 text-terracotta border border-terracotta/20 text-[10px] font-bold px-2.5 py-1 rounded-md">
                                        Habis
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 pr-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="document.getElementById('edit_product_modal_{{ $p->id }}').showModal()"
                                        class="btn btn-xs bg-forest hover:bg-forest-dark text-white border-none rounded-lg text-[10px] font-semibold px-2.5 shadow-none whitespace-nowrap">
                                        Detail / Edit
                                    </button>
                                    <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ $p->name }}?')" class="inline m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs bg-white hover:bg-terracotta/10 text-terracotta border border-terracotta/30 rounded-lg text-[10px] font-semibold px-2 shadow-none">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-ink-soft/60 text-xs font-medium">
                                Belum ada produk kriya yang sesuai dengan filter atau pencarian Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT & HISTORI PRODUK (PRESISI DI TENGAH LAYAR) -->
<!-- ========================================================================= -->
@foreach($products as $p)
    @php
        $allTransactions = \App\Models\Transaction::where('product_id', $p->id)->orderBy('created_at', 'desc')->get();
    @endphp
    
    <dialog id="edit_product_modal_{{ $p->id }}" class="modal modal-middle">
        <div class="modal-box w-11/12 max-w-2xl bg-white rounded-3xl border border-ink/10 p-5 sm:p-7 text-left shadow-2xl overflow-y-auto max-h-[88vh] my-auto">
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft hover:bg-cream">✕</button></form>
            
            <div class="border-b border-ink/5 pb-3 mb-4">
                <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-forest bg-forest/10 px-2 py-0.5 rounded">
                    Manajemen Produk
                </span>
                <h3 class="font-bold text-base sm:text-lg text-ink mt-1">{{ $p->name }}</h3>
                <p class="text-xs text-ink-soft">Atur informasi harga, sisa stok, atau tinjau catatan riwayat penjualan item ini.</p>
            </div>

            <!-- Tab Switcher Modal -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 border-b border-ink/5 pb-2">
                    <button type="button" onclick="switchProductModalTab('{{ $p->id }}', 'edit')" id="pmodal_tab_edit_btn_{{ $p->id }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold bg-forest text-white shadow-none transition-all">
                        Ubah Data Produk
                    </button>
                    <button type="button" onclick="switchProductModalTab('{{ $p->id }}', 'history')" id="pmodal_tab_hist_btn_{{ $p->id }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold bg-cream text-ink-soft hover:bg-cream/60 shadow-none transition-all">
                        Riwayat Penjualan ({{ $allTransactions->count() }})
                    </button>
                </div>

                <!-- Panel 1: Ubah Data Form -->
                <div id="pmodal_panel_edit_{{ $p->id }}">
                    <form action="{{ route('admin.products.update', $p->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-ink mb-1">Nama Produk <span class="text-terracotta">*</span></label>
                                <input type="text" name="name" value="{{ $p->name }}" required 
                                    class="input input-bordered input-sm w-full rounded-xl text-xs bg-cream/20 font-semibold text-ink focus:outline-none focus:border-forest">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-ink mb-1">Kategori Produk <span class="text-terracotta">*</span></label>
                                <select name="product_category" required class="select select-bordered select-sm w-full rounded-xl text-xs bg-cream/20 font-semibold text-ink focus:outline-none focus:border-forest">
                                    <option value="Tas & Dompet" {{ $p->product_category == 'Tas & Dompet' ? 'selected' : '' }}>Tas & Dompet</option>
                                    <option value="Dekorasi Rumah" {{ $p->product_category == 'Dekorasi Rumah' ? 'selected' : '' }}>Dekorasi Rumah</option>
                                    <option value="Aksesoris Diri" {{ $p->product_category == 'Aksesoris Diri' ? 'selected' : '' }}>Aksesoris Diri</option>
                                    <option value="Perlengkapan" {{ $p->product_category == 'Perlengkapan' ? 'selected' : '' }}>Perlengkapan Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-ink mb-1">Harga Jual (Rp) <span class="text-terracotta">*</span></label>
                                <input type="number" name="price" value="{{ $p->price }}" required 
                                    class="input input-bordered input-sm w-full rounded-xl text-xs font-mono font-bold text-forest bg-cream/20 focus:outline-none focus:border-forest">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-ink mb-1">Stok Tersedia <span class="text-terracotta">*</span></label>
                                <input type="number" name="stock" value="{{ $p->stock }}" min="0" required 
                                    class="input input-bordered input-sm w-full rounded-xl text-xs font-mono font-bold text-ink bg-cream/20 focus:outline-none focus:border-forest">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-ink mb-1">Sumber Material Limbah</label>
                                <input type="text" name="material_source" value="{{ $p->material_source }}" placeholder="Contoh: Limbah Botol Plastik HDPE Makassar"
                                    class="input input-bordered input-sm w-full rounded-xl text-xs bg-cream/20 text-ink focus:outline-none focus:border-forest">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1">Deskripsi Produk</label>
                            <textarea name="description" rows="2" class="textarea textarea-bordered w-full rounded-xl text-xs bg-cream/20 text-ink leading-relaxed focus:outline-none focus:border-forest">{{ $p->description }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-end pt-1">
                            <div>
                                <label class="block text-xs font-semibold text-ink mb-1">Perbarui Foto (Opsional)</label>
                                <input type="file" name="photo" accept="image/*" class="file-input file-input-bordered file-input-sm w-full rounded-xl text-xs bg-cream/20 focus:outline-none focus:border-forest" />
                            </div>
                            <div class="space-y-2">
                                <select name="status" class="select select-bordered select-sm w-full rounded-xl text-xs font-semibold bg-cream/20 focus:outline-none focus:border-forest">
                                    <option value="available" {{ $p->status == 'available' ? 'selected' : '' }}>Status: Tersedia</option>
                                    <option value="sold_out" {{ $p->status == 'sold_out' ? 'selected' : '' }}>Status: Habis / Sold Out</option>
                                </select>
                                <label class="flex items-center gap-2 cursor-pointer bg-cream/30 p-2 rounded-xl border border-ink/5">
                                    <input type="checkbox" name="is_featured" value="1" class="checkbox checkbox-xs checkbox-success rounded" {{ $p->is_featured ? 'checked' : '' }} />
                                    <span class="text-xs font-semibold text-ink">Rekomendasi Unggulan</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm w-full bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold h-11 shadow-sm transition-all mt-3">
                            Simpan Perubahan Produk &rarr;
                        </button>
                    </form>
                </div>

                <!-- Panel 2: Histori Penjualan -->
                <div id="pmodal_panel_hist_{{ $p->id }}" class="hidden space-y-3">
                    <div class="overflow-x-auto rounded-xl border border-ink/5">
                        <table class="table w-full text-xs">
                            <thead>
                                <tr class="bg-cream/40 text-ink-soft border-b border-ink/5 font-semibold text-[10px] uppercase">
                                    <th class="py-2.5 pl-3">Waktu</th>
                                    <th class="py-2.5">Invoice</th>
                                    <th class="py-2.5">Pembeli</th>
                                    <th class="py-2.5 text-center">Qty</th>
                                    <th class="py-2.5 text-center">Status</th>
                                    <th class="py-2.5 pr-3 text-right">Nota</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ink/5 font-medium">
                                @forelse($allTransactions as $sh)
                                    @php $buyer = \App\Models\User::find($sh->user_id); @endphp
                                    <tr class="hover:bg-cream/20">
                                        <td class="py-2.5 pl-3 font-mono text-[11px] text-ink-soft">{{ $sh->created_at ? $sh->created_at->format('d/m/Y') : '-' }}</td>
                                        <td class="py-2.5 font-mono font-bold text-ink">{{ $sh->order_id }}</td>
                                        <td class="py-2.5 font-semibold text-ink truncate max-w-[120px]">{{ $buyer->name ?? 'Warga' }}</td>
                                        <td class="py-2.5 text-center font-mono font-bold text-ink">{{ $sh->quantity ?? 1 }}x</td>
                                        <td class="py-2.5 text-center">
                                            @if(str_contains(strtolower($sh->status), 'pending'))
                                                <span class="inline-flex items-center whitespace-nowrap bg-amber-50 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded">Pending</span>
                                            @elseif(str_contains(strtolower($sh->status), 'picked') || str_contains(strtolower($sh->status), 'success'))
                                                <span class="inline-flex items-center whitespace-nowrap bg-forest/10 text-forest text-[10px] font-bold px-2 py-0.5 rounded">Selesai</span>
                                            @else
                                                <span class="inline-flex items-center whitespace-nowrap bg-maritime/10 text-maritime text-[10px] font-bold px-2 py-0.5 rounded">Diproses</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 pr-3 text-right">
                                            <button type="button" onclick="document.getElementById('receipt_modal_{{ $sh->id }}').showModal()"
                                                class="btn btn-xs bg-cream hover:bg-forest hover:text-white text-ink border border-ink/10 rounded-lg text-[10px] font-semibold px-2 shadow-none">
                                                Nota
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-ink-soft/60 text-xs">Belum ada transaksi pembelian untuk produk ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-ink/40 backdrop-blur-xs"><button>close</button></form>
    </dialog>

    <!-- Modal Nota Transaksi Per Baris -->
    @foreach($allTransactions as $sh)
        @php $buyer = \App\Models\User::find($sh->user_id); @endphp
        <dialog id="receipt_modal_{{ $sh->id }}" class="modal modal-middle z-[80]">
            <div class="modal-box w-11/12 max-w-sm bg-white rounded-3xl border border-ink/10 p-5 sm:p-6 text-left relative shadow-2xl my-auto">
                <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-3.5 top-3.5 text-ink-soft hover:bg-cream">✕</button></form>

                <div class="text-center border-b border-ink/5 pb-3">
                    <h4 class="font-bold text-base text-ink">SulapaKarya Makassar</h4>
                    <p class="text-[10px] text-ink-soft font-medium uppercase tracking-wider mt-0.5">Nota Penjualan Produk Kriya</p>
                    <span class="inline-flex items-center whitespace-nowrap bg-forest/10 text-forest text-[10px] font-bold px-2 py-0.5 rounded mt-1.5 uppercase">
                        {{ $sh->status }}
                    </span>
                </div>

                <div class="py-3 space-y-1.5 border-b border-ink/5 text-xs text-ink-soft">
                    <div class="flex justify-between"><span>No. Invoice</span> <strong class="font-mono text-ink">{{ $sh->order_id }}</strong></div>
                    <div class="flex justify-between"><span>Waktu Transaksi</span> <span class="font-mono text-ink">{{ $sh->created_at ? $sh->created_at->format('d M Y, H:i') : '-' }} WITA</span></div>
                    <div class="flex justify-between"><span>Nama Pembeli</span> <strong class="text-ink">{{ $buyer->name ?? 'Warga' }}</strong></div>
                </div>

                <div class="py-3 border-b border-ink/5 text-xs space-y-1">
                    <span class="text-[10px] font-bold uppercase text-ink-soft block">Item Produk</span>
                    <div class="flex justify-between items-start">
                        <span class="font-semibold text-ink">{{ $p->name }}</span>
                        <span class="font-mono font-bold text-ink">{{ $sh->quantity ?? 1 }}x</span>
                    </div>
                    <span class="text-[10px] text-ink-soft font-mono block">@ Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                </div>

                <div class="pt-3 space-y-1 text-xs">
                    <div class="flex justify-between text-ink-soft">
                        <span>Subtotal</span>
                        <span class="font-mono text-ink">Rp {{ number_format($sh->original_price ?? ($p->price * ($sh->quantity ?? 1)), 0, ',', '.') }}</span>
                    </div>
                    @if(($sh->points_used ?? 0) > 0)
                        <div class="flex justify-between text-terracotta">
                            <span>Diskon Poin</span>
                            <span class="font-mono font-bold">- Rp {{ number_format($sh->points_used, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center font-bold text-sm text-ink pt-2 border-t border-ink/5 mt-1">
                        <span>Total Bayar</span>
                        <span class="font-mono font-bold text-forest">Rp {{ number_format($sh->final_price ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop bg-ink/40 backdrop-blur-xs"><button>close</button></form>
        </dialog>
    @endforeach
@endforeach

<!-- ========================================================================= -->
<!-- MODAL TAMBAH PRODUK BARU (PRESISI DI TENGAH LAYAR) -->
<!-- ========================================================================= -->
<dialog id="add_product_modal" class="modal modal-middle">
    <div class="modal-box w-11/12 max-w-xl bg-white rounded-3xl border border-ink/10 p-5 sm:p-7 text-left shadow-2xl overflow-y-auto max-h-[88vh] my-auto">
        <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft hover:bg-cream">✕</button></form>
        
        <div class="border-b border-ink/5 pb-3 mb-4">
            <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-forest bg-forest/10 px-2 py-0.5 rounded">
                Karya Baru
            </span>
            <h3 class="font-bold text-base sm:text-lg text-ink mt-1">Upload Produk Kriya</h3>
            <p class="text-xs text-ink-soft mt-0.5">Daftarkan produk daur ulang baru hasil kerajinan tangan mitra artisan.</p>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">Nama Produk <span class="text-terracotta">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Tas Anyaman Plastik" required 
                        class="input input-bordered input-sm w-full rounded-xl text-xs bg-cream/20 font-semibold text-ink focus:outline-none focus:border-forest">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">Kategori <span class="text-terracotta">*</span></label>
                    <select name="product_category" required class="select select-bordered select-sm w-full rounded-xl text-xs bg-cream/20 font-semibold text-ink focus:outline-none focus:border-forest">
                        <option value="Tas & Dompet">Tas & Dompet</option>
                        <option value="Dekorasi Rumah">Dekorasi Rumah</option>
                        <option value="Aksesoris Diri">Aksesoris Diri</option>
                        <option value="Perlengkapan">Perlengkapan Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">Harga Jual (Rp) <span class="text-terracotta">*</span></label>
                    <input type="number" name="price" value="{{ old('price') }}" placeholder="25000" required 
                        class="input input-bordered input-sm w-full rounded-xl text-xs font-mono font-bold text-forest bg-cream/20 focus:outline-none focus:border-forest">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">Stok Awal <span class="text-terracotta">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock', 1) }}" min="0" required 
                        class="input input-bordered input-sm w-full rounded-xl text-xs font-mono font-bold text-ink bg-cream/20 focus:outline-none focus:border-forest">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-ink mb-1">Sumber Material Dasar</label>
                    <input type="text" name="material_source" value="{{ old('material_source') }}" placeholder="Contoh: Limbah Botol Plastik HDPE Makassar" 
                        class="input input-bordered input-sm w-full rounded-xl text-xs bg-cream/20 text-ink focus:outline-none focus:border-forest">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-ink mb-1">Deskripsi Lengkap</label>
                <textarea name="description" rows="2" placeholder="Ceritakan proses pembuatan dan keunggulan produk ini..." 
                    class="textarea textarea-bordered w-full rounded-xl text-xs bg-cream/20 text-ink leading-relaxed focus:outline-none focus:border-forest">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-end pt-1">
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">Foto Hasil Karya <span class="text-terracotta">*</span></label>
                    <input type="file" name="photo" accept="image/*" required class="file-input file-input-bordered file-input-sm w-full rounded-xl text-xs bg-cream/20 focus:outline-none focus:border-forest" />
                </div>
                <div class="space-y-2">
                    <select name="status" class="select select-bordered select-sm w-full rounded-xl text-xs font-semibold bg-cream/20 focus:outline-none focus:border-forest">
                        <option value="available">Status: Tersedia</option>
                        <option value="sold_out">Status: Habis</option>
                    </select>
                    <label class="flex items-center gap-2 cursor-pointer bg-cream/30 p-2 rounded-xl border border-ink/5">
                        <input type="checkbox" name="is_featured" value="1" class="checkbox checkbox-xs checkbox-success rounded" />
                        <span class="text-xs font-semibold text-ink">Tandai sebagai Rekomendasi Unggulan</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-sm w-full bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold h-11 shadow-sm transition-all mt-3">
                Simpan & Publikasikan Produk &rarr;
            </button>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop bg-ink/40 backdrop-blur-xs"><button>close</button></form>
</dialog>

<script>
    function switchProductModalTab(productId, tab) {
        const panelEdit = document.getElementById('pmodal_panel_edit_' + productId);
        const panelHist = document.getElementById('pmodal_panel_hist_' + productId);
        const btnEdit = document.getElementById('pmodal_tab_edit_btn_' + productId);
        const btnHist = document.getElementById('pmodal_tab_hist_btn_' + productId);

        if (!panelEdit || !panelHist) return;

        if (tab === 'edit') {
            panelEdit.classList.remove('hidden');
            panelHist.classList.add('hidden');
            btnEdit.className = "px-3 py-1.5 rounded-xl text-xs font-bold bg-forest text-white shadow-none transition-all";
            btnHist.className = "px-3 py-1.5 rounded-xl text-xs font-bold bg-cream text-ink-soft hover:bg-cream/60 shadow-none transition-all";
        } else {
            panelEdit.classList.add('hidden');
            panelHist.classList.remove('hidden');
            btnHist.className = "px-3 py-1.5 rounded-xl text-xs font-bold bg-forest text-white shadow-none transition-all";
            btnEdit.className = "px-3 py-1.5 rounded-xl text-xs font-bold bg-cream text-ink-soft hover:bg-cream/60 shadow-none transition-all";
        }
    }
</script>
@endsection