@extends('layouts.dashboard', ['title' => 'Kelola Produk Kriya — SulapaKarya Macca'])

@section('dashboard-content')
<div class="space-y-6 animate-fadeIn text-left">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="text-left">
            <h1 class="font-display font-extrabold text-2xl text-ink tracking-tight">Katalog Produk Kriya</h1>
            <p class="text-xs text-ink-soft/80 font-medium mt-1">Kelola data produk daur ulang (upcycling) yang dijual dalam mata uang Rupiah via Midtrans.</p>
        </div>
        <button onclick="add_product_modal.showModal()" class="btn btn-sm bg-forest border-none text-white hover:bg-forest-dark rounded-xl normal-case shadow-md font-bold px-4 gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Upload Produk Baru
        </button>
    </div>

    @if($errors->any())
        <div class="alert alert-error bg-terracotta/10 border-terracotta/20 text-terracotta rounded-2xl text-xs font-bold text-left p-4 shadow-sm">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success bg-forest/10 border-forest/20 text-forest rounded-2xl text-xs font-bold text-left p-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-ink/5 rounded-[1.5rem] p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto rounded-xl border border-ink/5">
            <table class="table w-full text-sm">
                <thead>
                    <tr class="border-b border-ink/5 text-ink/70 font-bold uppercase tracking-wider text-xs bg-cream/60">
                        <th class="py-3.5 pl-5">Nama Produk & Gambar</th>
                        <th class="py-3.5">Kategori / Material</th>
                        <th class="py-3.5">Harga Produk (IDR)</th>
                        <th class="py-3.5">Stok Sisa</th>
                        <th class="py-3.5 font-bold text-maritime">Total Terjual</th>
                        <th class="py-3.5 text-center">Status</th>
                        <th class="py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="font-medium text-ink/90">
                    @forelse($products as $p)
                        @php
                            // 🔥 STRATEGI PENYELAMAT: Tarik paksa data mentah dari tabel database transactions
                            // Kita pakai 'whereRaw' dengan casting 'TO_CHAR' / 'VARCHAR' untuk menjamin kecocokan UUID vs Integer di Supabase
                            $allTransactions = \App\Models\Transaction::whereRaw('CAST(product_id AS VARCHAR) = ?', [(string)$p->id])
                                                ->orderBy('created_at', 'desc')
                                                ->get();
                                                
                            // Hitung baris data yang statusnya mengandung kata 'Siap', 'paid', 'success', atau 'picked_up'
                            $totalSold = 0;
                            foreach($allTransactions as $t) {
                                $st = strtolower($t->status);
                                if(str_contains($st, 'siap') || str_contains($st, 'pickup') || str_contains($st, 'paid') || str_contains($st, 'success') || str_contains($st, 'picked_up')) {
                                    $totalSold += ($t->quantity ?? 1);
                                }
                            }
                        @endphp
                        <tr class="hover:bg-cream/10 border-b border-ink/5 transition-colors">
                            <td class="py-4 pl-5">
                                <div class="flex items-center gap-3">
                                    <div class="avatar">
                                        <div class="w-12 h-12 rounded-lg border border-ink/10 shadow-sm overflow-hidden flex items-center justify-center text-ink-soft/30 bg-sand/20">
                                            @if(!empty($p->photo_path))
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($p->photo_path) }}" alt="{{ $p->name }}" class="object-cover w-full h-full" />
                                            @else
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-left max-w-[200px] truncate">
                                        <p class="font-bold text-ink truncate leading-tight flex items-center gap-1">
                                            {{ $p->name }}
                                            @if($p->is_featured) <span class="text-maritime" title="Produk Unggulan">⭐</span> @endif
                                        </p>
                                        <p class="text-[10px] text-ink-soft/70 font-mono mt-0.5 truncate">Diupload: {{ $p->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4">
                                <span class="badge bg-cream border border-ink/10 text-ink-soft font-bold text-[10px] px-2 py-1 rounded-md">{{ $p->product_category }}</span>
                                @if($p->material_source)
                                    <p class="text-[10px] text-gray-400 mt-1 truncate max-w-[120px]">Bahan: {{ $p->material_source }}</p>
                                @endif
                            </td>

                            <td class="py-4 font-mono font-bold text-forest">
                                Rp {{ number_format($p->price, 0, ',', '.') }}
                            </td>

                            <td class="py-4 font-mono font-bold text-ink">
                                {{ $p->stock }} <span class="text-[10px] font-sans font-normal text-ink-soft">item</span>
                            </td>

                            <td class="py-4 font-mono font-bold text-maritime">
                                {{ $totalSold }} <span class="text-[10px] font-sans font-normal text-ink-soft">terjual</span>
                            </td>

                            <td class="py-4 text-center">
                                @if($p->stock > 0)
                                    <span class="badge bg-forest/10 border-none text-forest text-[11px] font-bold px-2.5 py-2 rounded-lg">Tersedia</span>
                                @else
                                    <span class="badge bg-terracotta/10 border-none text-terracotta text-[11px] font-bold px-2.5 py-2 rounded-lg">Habis</span>
                                @endif
                            </td>

                            <td class="py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="document.getElementById('edit_product_modal_{{ $p->id }}').showModal()" class="btn btn-xs bg-maritime border-none text-white hover:bg-maritime-dark rounded-md font-bold px-2.5">
                                        Edit / Histori
                                    </button>
                                    
                                    <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ $p->name }} secara permanen?')" class="inline m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs bg-terracotta border-none text-white hover:bg-red-600 rounded-md font-bold px-2.5">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <dialog id="edit_product_modal_{{ $p->id }}" class="modal modal-bottom sm:modal-middle">
                            <div class="modal-box bg-white max-w-4xl rounded-[2rem] border border-ink/5 p-6 text-left relative overflow-hidden max-h-[90vh] flex flex-col">
                                <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft z-50">✕</button></form>
                                
                                <div class="tabs tabs-lifted w-full flex-1 flex flex-col overflow-hidden">
                                    
                                    <input type="radio" name="tabs_{{ $p->id }}" class="tab font-bold text-xs" aria-label="Ubah Data Produk" checked />
                                    <div class="tab-content bg-white border-base-300 rounded-box p-4 pt-6 overflow-y-auto flex-1">
                                        <form action="{{ route('admin.products.update', $p->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                            @csrf
                                            @method('PUT')
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="form-control">
                                                    <label class="label py-1"><span class="label-text font-bold text-xs">Nama Produk</span></label>
                                                    <input type="text" name="name" value="{{ old('name', $p->name) }}" required class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">
                                                </div>
                                                
                                                <div class="form-control">
                                                    <label class="label py-1"><span class="label-text font-bold text-xs">Kategori</span></label>
                                                    <select name="product_category" required class="select select-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20 font-bold">
                                                        <option value="Tas & Dompet" {{ $p->product_category == 'Tas & Dompet' ? 'selected' : '' }}>Tas & Dompet</option>
                                                        <option value="Dekorasi Rumah" {{ $p->product_category == 'Dekorasi Rumah' ? 'selected' : '' }}>Dekorasi Rumah</option>
                                                        <option value="Aksesoris Diri" {{ $p->product_category == 'Aksesoris Diri' ? 'selected' : '' }}>Aksesoris Diri</option>
                                                        <option value="Perlengkapan" {{ $p->product_category == 'Perlengkapan' ? 'selected' : '' }}>Perlengkapan Lainnya</option>
                                                    </select>
                                                </div>

                                                <div class="form-control">
                                                    <label class="label py-1"><span class="label-text font-bold text-xs text-forest">Harga Jual (Rp)</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-ink-soft">Rp</span>
                                                        <input type="number" name="price" value="{{ old('price', $p->price) }}" required class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20 font-mono font-bold text-forest pl-10">
                                                    </div>
                                                </div>

                                                <div class="form-control">
                                                    <label class="label py-1"><span class="label-text font-bold text-xs">Stok Sisa</span></label>
                                                    <input type="number" name="stock" value="{{ old('stock', $p->stock) }}" min="0" required class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">
                                                </div>

                                                <div class="form-control md:col-span-2">
                                                    <label class="label py-1"><span class="label-text font-bold text-xs">Sumber Material Dasar</span></label>
                                                    <input type="text" name="material_source" value="{{ old('material_source', $p->material_source) }}" class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">
                                                </div>
                                            </div>

                                            <div class="form-control">
                                                <label class="label py-1"><span class="label-text font-bold text-xs">Deskripsi Lengkap</span></label>
                                                <textarea name="description" rows="3" class="textarea textarea-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">{{ old('description', $p->description) }}</textarea>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                                                <div class="form-control">
                                                    <label class="label py-1"><span class="label-text font-bold text-xs">Ganti Foto (Biarkan kosong jika tetap)</span></label>
                                                    <input type="file" name="photo" accept="image/*" class="file-input file-input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20" />
                                                </div>

                                                <div class="flex flex-col gap-2">
                                                    <select name="status" class="select select-bordered w-full rounded-xl text-xs font-bold focus:outline-none focus:border-forest bg-cream/20 mb-1">
                                                        <option value="available" {{ $p->status == 'available' ? 'selected' : '' }}>Set Status: Tersedia</option>
                                                        <option value="sold_out" {{ $p->status == 'sold_out' ? 'selected' : '' }}>Set Status: Habis / Sold Out</option>
                                                    </select>
                                                    <label class="cursor-pointer label justify-start gap-3 bg-cream/30 p-2.5 rounded-xl border border-ink/5">
                                                        <input type="checkbox" name="is_featured" class="checkbox checkbox-sm checkbox-primary border-forest checked:border-forest [--chkbg:theme(colors.forest.DEFAULT)]" {{ $p->is_featured ? 'checked' : '' }} />
                                                        <span class="label-text font-bold text-xs">Rekomendasi Unggulan ⭐</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn w-full bg-forest text-white border-none rounded-xl font-extrabold normal-case mt-4 shadow-md">
                                                Simpan Perubahan Data Produk
                                            </button>
                                        </form>
                                    </div>

                                    <input type="radio" name="tabs_{{ $p->id }}" class="tab font-bold text-xs" aria-label="Histori Penjualan ({{ $allTransactions->count() }})" />
                                    <div class="tab-content bg-white border-base-300 rounded-box p-4 pt-6 overflow-y-auto flex-1">
                                        <div class="overflow-x-auto rounded-xl border border-ink/5">
                                            <table class="table w-full text-xs">
                                                <thead>
                                                    <tr class="bg-sand/20 text-ink border-b border-ink/5">
                                                        <th class="font-bold py-3 pl-4">Tanggal</th>
                                                        <th class="font-bold py-3">ID Invoice</th>
                                                        <th class="font-bold py-3">Nama Pembeli</th>
                                                        <th class="font-bold py-3 text-center">Qty</th>
                                                        <th class="font-bold py-3 text-center">Status Pickup</th>
                                                        <th class="font-bold py-3 text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="font-medium text-ink-soft">
                                                    @forelse($allTransactions as $sh)
                                                        @php
                                                            $buyer = \App\Models\User::find($sh->user_id);
                                                        @endphp
                                                        <tr class="border-b border-ink/5 hover:bg-cream/5">
                                                            <td class="py-3 pl-4 font-mono">{{ $sh->created_at->format('d M Y') }}</td>
                                                            <td class="py-3 font-mono font-bold text-ink">{{ $sh->order_id }}</td>
                                                            <td class="py-3 font-bold text-ink">{{ $buyer->name ?? 'Warga SulapaKarya' }}</td>
                                                            <td class="py-3 text-center font-mono font-bold text-ink">{{ $sh->quantity ?? 1 }}x</td>
                                                            
                                                            <td class="py-3 text-center">
                                                                @if(str_contains(strtolower($sh->status), 'pending'))
                                                                    <span class="badge bg-amber-500/10 border-none text-amber-600 font-extrabold rounded-md text-[10px] py-2 px-2">⏳ Belum Bayar</span>
                                                                @elseif(str_contains(strtolower($sh->status), 'siap') || str_contains(strtolower($sh->status), 'paid') || str_contains(strtolower($sh->status), 'success'))
                                                                    <span class="badge bg-maritime/10 border-none text-maritime font-extrabold rounded-md text-[10px] py-2 px-2">📦 Siap Diambil</span>
                                                                @elseif(str_contains(strtolower($sh->status), 'picked'))
                                                                    <span class="badge bg-forest/10 border-none text-forest font-extrabold rounded-md text-[10px] py-2 px-2">✓ Selesai Diambil</span>
                                                                @else
                                                                    <span class="badge bg-terracotta/10 border-none text-terracotta font-extrabold rounded-md text-[10px] py-2 px-2">✕ Batal</span>
                                                                @endif
                                                            </td>

                                                            <td class="py-3 text-center flex items-center justify-center gap-1.5">
                                                                @if(str_contains(strtolower($sh->status), 'siap') || str_contains(strtolower($sh->status), 'paid') || str_contains(strtolower($sh->status), 'success'))
                                                                    <form action="/admin/transaksi/pickup/{{ $sh->id }}" method="POST" class="m-0 p-0">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit" class="btn btn-xs bg-forest border-none text-white hover:bg-forest-dark rounded-md font-bold px-2">
                                                                            Konfirmasi Serah Barang 🤝
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <span class="text-[10px] text-gray-400 font-semibold italic">Tidak ada aksi</span>
                                                                @endif
                                                                
                                                                <button onclick="document.getElementById('receipt_modal_{{ $sh->id }}').showModal()" class="btn btn-xs btn-outline border-ink/10 hover:bg-ink hover:text-white rounded-md font-bold px-2">
                                                                    🔍 Nota
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="py-8 text-center text-gray-400 font-medium">
                                                                📦 Belum ada lembar transaksi untuk karya kriya ini.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <form method="dialog" class="modal-backdrop bg-ink/30 backdrop-blur-sm"><button>close</button></form>
                        </dialog>

                        @foreach($allTransactions as $sh)
                            @php
                                $buyer = \App\Models\User::find($sh->user_id);
                            @endphp
                            <dialog id="receipt_modal_{{ $sh->id }}" class="modal modal-bottom sm:modal-middle z-[70]">
                                <div class="modal-box bg-cream max-w-md rounded-[2rem] border border-ink/10 p-6 text-left relative shadow-2xl">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft">✕</button>
                                    </form>

                                    <div class="text-center border-b border-dashed border-ink/20 pb-4">
                                        <h4 class="font-display font-black text-xl text-ink">SulapaKarya Macca</h4>
                                        <p class="text-[10px] text-ink-soft font-semibold tracking-wide uppercase mt-0.5">Pusat Kerajinan Daur Ulang Makassar</p>
                                        <div class="badge border-none text-[9px] font-bold mt-2 uppercase px-2.5 py-2
                                            {{ str_contains(strtolower($sh->status), 'picked') ? 'bg-forest/10 text-forest' : '' }}
                                            {{ str_contains(strtolower($sh->status), 'siap') || str_contains(strtolower($sh->status), 'paid') || str_contains(strtolower($sh->status), 'success') ? 'bg-maritime/10 text-maritime' : '' }}
                                            {{ str_contains(strtolower($sh->status), 'pending') ? 'bg-amber-500/10 text-amber-600' : '' }}
                                            {{ str_contains(strtolower($sh->status), 'cancel') ? 'bg-terracotta/10 text-terracotta' : '' }} ">
                                            Status: {{ $sh->status }}
                                        </div>
                                    </div>

                                    <div class="py-4 space-y-2 border-b border-dashed border-ink/20 text-[11px] font-medium text-ink-soft">
                                        <div class="flex justify-between"><span>Nomor Transaksi</span> <span class="font-mono font-bold text-ink">{{ $sh->order_id }}</span></div>
                                        <div class="flex justify-between"><span>Waktu Transaksi</span> <span class="font-mono text-ink">{{ $sh->created_at->format('d M Y, H:i') }} WITA</span></div>
                                        <div class="flex justify-between"><span>Nama Pembeli (Warga)</span> <span class="font-bold text-ink">{{ $buyer->name ?? 'Warga SulapaKarya' }}</span></div>
                                        <div class="flex justify-between"><span>No. Telepon Kontak</span> <span class="font-mono text-ink">{{ $buyer->phone ?? '082XXXXXXXXX' }}</span></div>
                                    </div>

                                    <div class="py-4 space-y-3 border-b border-ink/10 text-xs">
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-ink/40 block">Item Produk</span>
                                        <div class="flex justify-between items-start gap-4">
                                            <div>
                                                <p class="font-bold text-ink leading-tight">{{ $p->name }}</p>
                                                <p class="text-[10px] text-ink-soft font-mono mt-0.5">Harga Satuan: Rp {{ number_format($p->price, 0, ',', '.') }}</p>
                                            </div>
                                            <span class="font-mono font-bold text-ink shrink-0">{{ $sh->quantity ?? 1 }}x</span>
                                        </div>
                                    </div>

                                    <div class="py-4 space-y-2 text-xs font-semibold">
                                        <div class="flex justify-between text-ink-soft">
                                            <span>Subtotal Tagihan</span> 
                                            <span class="font-mono text-ink">Rp {{ number_format($sh->original_price ?? ($p->price * ($sh->quantity ?? 1)), 0, ',', '.') }}</span>
                                        </div>
                                        
                                        @if(($sh->points_used ?? 0) > 0)
                                            <div class="flex justify-between text-terracotta">
                                                <span>Potongan Poin Kriya</span> 
                                                <span class="font-mono font-bold">- Rp {{ number_format($sh->points_used, 0, ',', '.') }}</span>
                                            </div>
                                        @endif

                                        <div class="flex justify-between items-center text-sm font-bold border-t border-ink/5 pt-3 mt-1">
                                            <span class="text-ink">Total Nominal Pembayaran</span>
                                            <span class="font-mono font-black text-forest text-base">Rp {{ number_format($sh->final_price ?? 0, 0, ',', '.') }}</span>
                                        </div>
                                    </div>

                                    <div class="text-center pt-2 text-[10px] text-ink-soft/60 font-medium">
                                        <p>Terima kasih telah mendukung pengrajin lokal kriya</p>
                                        <p class="mt-0.5">& melestarikan ekosistem lingkungan kota Makassar!</p>
                                    </div>
                                </div>
                                <form method="dialog" class="modal-backdrop bg-ink/20 backdrop-blur-xs"><button>close</button></form>
                            </dialog>
                        @endforeach

                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-ink-soft/60 font-medium text-xs">
                                📭 Belum ada produk kriya yang diunggah ke dalam sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <dialog id="add_product_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white max-w-2xl rounded-[2rem] border border-ink/5 p-6 text-left relative">
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft">✕</button></form>
            <h3 class="font-display font-extrabold text-xl text-ink">Upload Produk Baru</h3>
            <p class="text-xs text-ink-soft font-semibold mt-1">Masukkan data produk kriya hasil daur ulang (upcycling) beserta tarif Rupiahnya.</p>
            
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 mt-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text font-bold text-xs">Nama Produk <span class="text-terracotta">*</span></span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Tas Anyaman Plastik" required class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">
                    </div>
                    
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text font-bold text-xs">Kategori <span class="text-terracotta">*</span></span></label>
                        <select name="product_category" required class="select select-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20 font-bold">
                            <option value="Tas & Dompet">Tas & Dompet</option>
                            <option value="Dekorasi Rumah">Dekorasi Rumah</option>
                            <option value="Aksesoris Diri">Aksesoris Diri</option>
                            <option value="Perlengkapan">Perlengkapan Lainnya</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="label-text font-bold text-xs text-forest">Harga Jual Asli (Rupiah Rp) <span class="text-terracotta">*</span></span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-ink-soft">Rp</span>
                            <input type="number" name="price" value="{{ old('price') }}" placeholder="Contoh: 25000" required class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20 font-mono font-bold text-forest pl-10">
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label py-1"><span class="label-text font-bold text-xs">Stok Awal <span class="text-terracotta">*</span></span></label>
                        <input type="number" name="stock" value="{{ old('stock', 1) }}" min="0" required class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">
                    </div>

                    <div class="form-control md:col-span-2">
                        <label class="label py-1"><span class="label-text font-bold text-xs">Sumber Material Dasar (Opsional)</span></label>
                        <input type="text" name="material_source" value="{{ old('material_source') }}" placeholder="Contoh: 100% Limbah Botol Plastik HDPE kota Makassar" class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">
                    </div>
                </div>

                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-bold text-xs">Deskripsi Lengkap</span></label>
                    <textarea name="description" rows="3" placeholder="Ceritakan keunikan seni dan proses pembuatan upcycling produk ini..." class="textarea textarea-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text font-bold text-xs">Foto Hasil Karya (Max 3MB) <span class="text-terracotta">*</span></span></label>
                        <input type="file" name="photo" accept="image/*" required class="file-input file-input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <select name="status" class="select select-bordered w-full rounded-xl text-xs font-bold focus:outline-none focus:border-forest bg-cream/20 mb-1">
                            <option value="available">Set Status: Tersedia</option>
                            <option value="sold_out">Set Status: Habis / Sold Out</option>
                        </select>
                        <label class="cursor-pointer label justify-start gap-3 bg-cream/30 p-2.5 rounded-xl border border-ink/5">
                            <input type="checkbox" name="is_featured" class="checkbox checkbox-sm checkbox-primary border-forest checked:border-forest [--chkbg:theme(colors.forest.DEFAULT)]" />
                            <span class="label-text font-bold text-xs">Tandai sebagai Rekomendasi Unggulan ⭐</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn w-full bg-forest text-white border-none rounded-xl font-extrabold normal-case mt-4 shadow-md shadow-forest/10">
                    Publish Produk Baru 🚀
                </button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-ink/30 backdrop-blur-sm"><button>close</button></form>
    </dialog>

</div>
@endsection