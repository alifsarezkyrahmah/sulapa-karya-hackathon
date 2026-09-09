@extends('layouts.dashboard', ['title' => 'Tarif & Nilai Tukar Sampah — SulapaKarya'])

@section('dashboard-content')
<div class="space-y-6 text-left">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-ink/5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">Katalog Tarif & Nilai Sampah</h1>
            <p class="text-xs text-ink-soft mt-0.5">Konversi nilai tukar poin reward otomatis dihitung pada rasio <strong>40% dari harga dasar per kg</strong>.</p>
        </div>
        <button type="button" onclick="document.getElementById('add_waste_price_modal').showModal()" 
            class="btn btn-sm bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold px-4 h-10 shadow-sm transition-all w-full sm:w-auto">
            <svg class="w-4 h-4 mr-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Jenis Sampah
        </button>
    </div>

    <!-- Alert Notifikasi -->
    @if($errors->any())
        <div class="alert alert-error bg-terracotta/10 border-terracotta/20 text-terracotta rounded-2xl text-xs font-semibold p-4 shadow-none">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success bg-forest/10 border-forest/20 text-forest rounded-2xl text-xs font-semibold p-4 shadow-none">
            {{ session('success') }}
        </div>
    @endif

    <!-- Toolbar Pencarian & Filter Urutan -->
    <div class="bg-white border border-ink/5 rounded-2xl p-4 shadow-none">
        <form method="GET" action="{{ route('admin.waste-prices.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs">
            <!-- Input Pencarian -->
            <div class="sm:col-span-6 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama komoditas sampah..." 
                    class="input input-bordered input-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 pl-9 text-ink font-medium">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-ink-soft" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>

            <!-- Dropdown Filter Pengurutan -->
            <div class="sm:col-span-3">
                <select name="sort" onchange="this.form.submit()" class="select select-bordered select-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 font-semibold text-ink">
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Abjad (A - Z)</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Tarif Tertinggi (Rp)</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Tarif Terendah (Rp)</option>
                    <option value="points_desc" {{ request('sort') == 'points_desc' ? 'selected' : '' }}>Poin Konversi Tertinggi</option>
                    <option value="latest_updated" {{ request('sort') == 'latest_updated' ? 'selected' : '' }}>Baru Diperbarui</option>
                </select>
            </div>

            <!-- Tombol Terapkan & Reset -->
            <div class="sm:col-span-3 flex items-center gap-2">
                <button type="submit" class="btn btn-sm bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold flex-1 h-9 shadow-none">
                    Terapkan
                </button>
                @if(request()->filled('search') || request()->filled('sort'))
                    <a href="{{ route('admin.waste-prices.index') }}" class="btn btn-sm bg-white hover:bg-cream text-ink border border-ink/10 rounded-xl text-xs font-semibold h-9 px-3 shadow-none">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. TAMPILAN MOBILE / TABLET (< md): KARTU LIST RINGKAS -->
    <!-- ========================================================================= -->
    <div class="space-y-3 md:hidden">
        @forelse($wastePrices as $index => $wp)
            <div class="bg-white border border-ink/5 rounded-2xl p-4 shadow-none space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <span class="font-bold text-sm text-ink block">{{ $wp->name }}</span>
                        @if($wp->description)
                            <p class="text-[11px] text-ink-soft mt-0.5 leading-relaxed">{{ $wp->description }}</p>
                        @endif
                    </div>
                    <span class="text-[10px] font-mono text-ink-soft bg-cream px-2 py-0.5 rounded">#{{ $index + 1 }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2 bg-cream/30 p-2.5 rounded-xl border border-ink/5 text-xs">
                    <div>
                        <span class="text-[10px] text-ink-soft uppercase font-bold block">Harga Dasar</span>
                        <span class="font-mono font-bold text-forest text-xs">Rp {{ number_format($wp->price_per_kg, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-ink-soft">/{{ $wp->unit }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-ink-soft uppercase font-bold block">Poin Warga (40%)</span>
                        <span class="font-mono font-bold text-maritime text-xs">{{ number_format($wp->point_per_kg, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-ink-soft font-sans">Poin/{{ $wp->unit }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1 border-t border-ink/5 text-xs">
                    <span class="text-[10px] text-ink-soft font-mono">
                        {{ $wp->updated_at ? $wp->updated_at->translatedFormat('d M Y, H:i') : '-' }} WITA
                    </span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="document.getElementById('edit_waste_price_modal_{{ $wp->id }}').showModal()" 
                            class="btn btn-xs bg-maritime hover:bg-maritime-dark text-white border-none rounded-lg text-[10px] font-semibold px-2.5 shadow-none">
                            Edit
                        </button>
                        <form action="{{ route('admin.waste-prices.destroy', $wp->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tarif {{ $wp->name }}?')" class="inline m-0 p-0">
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
                Data tarif jenis sampah tidak ditemukan.
            </div>
        @endforelse
    </div>

    <!-- ========================================================================= -->
    <!-- 2. TAMPILAN DESKTOP (>= md): TABEL LEBAR PROPORSIONAL -->
    <!-- ========================================================================= -->
    <div class="hidden md:block bg-white border border-ink/5 rounded-2xl p-6 shadow-none">
        <div class="overflow-x-auto">
            <table class="table w-full text-xs">
                <thead>
                    <tr class="bg-cream/40 text-ink-soft border-b border-ink/5 font-semibold text-[10px] uppercase">
                        <th class="py-3 pl-3 w-12 text-center">No</th>
                        <th class="py-3">Nama Jenis Sampah</th>
                        <th class="py-3 text-forest font-bold">Harga Barang (Rp/Kg)</th>
                        <th class="py-3 text-maritime font-bold">Poin Konversi (40%)</th>
                        <th class="py-3 text-ink-soft">Terakhir Diperbarui</th>
                        <th class="py-3 pr-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/5 font-medium">
                    @forelse($wastePrices as $index => $wp)
                        <tr class="hover:bg-cream/20 transition-colors">
                            <td class="py-3.5 pl-3 text-center font-mono font-bold text-xs text-ink-soft">{{ $index + 1 }}</td>
                            <td class="py-3.5">
                                <span class="font-bold text-ink block text-xs">{{ $wp->name }}</span>
                                @if($wp->description)
                                    <p class="text-[11px] text-ink-soft/70 mt-0.5 max-w-[260px] truncate" title="{{ $wp->description }}">{{ $wp->description }}</p>
                                @endif
                            </td>
                            <td class="py-3.5 font-mono font-bold text-forest">
                                Rp {{ number_format($wp->price_per_kg, 0, ',', '.') }} <span class="text-[10px] font-sans font-normal text-ink-soft">/{{ $wp->unit }}</span>
                            </td>
                            <td class="py-3.5 font-mono font-bold text-maritime">
                                {{ number_format($wp->point_per_kg, 0, ',', '.') }} <span class="text-[10px] font-sans font-medium text-maritime/70">Poin/{{ $wp->unit }}</span>
                            </td>
                            <td class="py-3.5">
                                <span class="font-mono text-xs text-ink block">
                                    {{ $wp->updated_at ? $wp->updated_at->translatedFormat('d M Y, H:i') : '-' }} WITA
                                </span>
                                <span class="text-[10px] text-ink-soft/60 block">
                                    {{ $wp->updated_at ? $wp->updated_at->diffForHumans() : '' }}
                                </span>
                            </td>
                            <td class="py-3.5 pr-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="document.getElementById('edit_waste_price_modal_{{ $wp->id }}').showModal()" 
                                        class="btn btn-xs bg-maritime hover:bg-maritime-dark text-white border-none rounded-lg text-[10px] font-semibold px-2.5 shadow-none">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.waste-prices.destroy', $wp->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tarif {{ $wp->name }}?')" class="inline m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs bg-white hover:bg-terracotta/10 text-terracotta border border-terracotta/30 rounded-lg text-[10px] font-semibold px-2.5 shadow-none">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-ink-soft/60 text-xs font-medium">
                                Data tarif jenis sampah tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT TARIF (PRESISI DI TENGAH LAYAR) -->
<!-- ========================================================================= -->
@foreach($wastePrices as $wp)
    <dialog id="edit_waste_price_modal_{{ $wp->id }}" class="modal modal-middle">
        <div class="modal-box w-11/12 max-w-lg bg-white rounded-3xl border border-ink/10 p-6 sm:p-7 text-left shadow-2xl overflow-y-auto max-h-[88vh] my-auto">
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft hover:bg-cream">✕</button></form>
            
            <div class="border-b border-ink/5 pb-3 mb-4">
                <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-forest bg-forest/10 px-2 py-0.5 rounded">
                    Pengaturan Tarif
                </span>
                <h3 class="font-bold text-base sm:text-lg text-ink mt-1">Perbarui Tarif Sampah</h3>
                <p class="text-xs text-ink-soft mt-0.5">Poin konversi reward warga otomatis dihitung 40% dari harga per kg.</p>
            </div>

            <form action="{{ route('admin.waste-prices.update', $wp->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">Nama Jenis Sampah <span class="text-terracotta">*</span></label>
                    <input type="text" name="name" value="{{ $wp->name }}" required 
                        class="input input-bordered input-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 font-semibold text-ink">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">Harga Barang (Rp/Kg) <span class="text-terracotta">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-ink-soft">Rp</span>
                            <input type="number" name="price_per_kg" id="edit_price_{{ $wp->id }}" value="{{ $wp->price_per_kg }}" required 
                                oninput="document.getElementById('edit_point_{{ $wp->id }}').innerText = Math.round(this.value * 0.4).toLocaleString('id-ID')"
                                class="input input-bordered input-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 font-mono font-bold text-forest pl-9">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">Estimasi Poin (40%)</label>
                        <div class="h-8 rounded-xl bg-forest/[0.05] border border-forest/20 flex items-center justify-between px-3 font-mono font-bold text-forest text-xs">
                            <span id="edit_point_{{ $wp->id }}">{{ number_format($wp->point_per_kg, 0, ',', '.') }}</span>
                            <span class="text-[10px] font-sans font-medium text-forest">Poin</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">Catatan Tambahan <span class="text-ink-soft/60 font-normal">(Opsional)</span></label>
                    <textarea name="description" rows="2" class="textarea textarea-bordered w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 text-ink leading-relaxed">{{ $wp->description }}</textarea>
                </div>

                <button type="submit" class="btn btn-sm w-full bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold h-11 shadow-sm transition-all mt-2">
                    Simpan Perubahan Tarif &rarr;
                </button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-ink/40 backdrop-blur-xs"><button>close</button></form>
    </dialog>
@endforeach

<!-- ========================================================================= -->
<!-- MODAL TAMBAH JENIS SAMPAH BARU (PRESISI DI TENGAH LAYAR) -->
<!-- ========================================================================= -->
<dialog id="add_waste_price_modal" class="modal modal-middle">
    <div class="modal-box w-11/12 max-w-lg bg-white rounded-3xl border border-ink/10 p-6 sm:p-7 text-left shadow-2xl overflow-y-auto max-h-[88vh] my-auto">
        <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft hover:bg-cream">✕</button></form>
        
        <div class="border-b border-ink/5 pb-3 mb-4">
            <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-forest bg-forest/10 px-2 py-0.5 rounded">
                Komoditas Baru
            </span>
            <h3 class="font-bold text-base sm:text-lg text-ink mt-1">Tambah Jenis Sampah Baru</h3>
            <p class="text-xs text-ink-soft mt-0.5">Sistem secara otomatis menghitung 40% dari harga dasar sebagai poin insentif warga.</p>
        </div>

        <form action="{{ route('admin.waste-prices.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-ink mb-1">Nama Jenis Sampah <span class="text-terracotta">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Gelas Plastik (PP Berwarna)" required 
                    class="input input-bordered input-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 font-semibold text-ink">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">Harga Barang (Rp/Kg) <span class="text-terracotta">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-ink-soft">Rp</span>
                        <input type="number" name="price_per_kg" placeholder="2500" required 
                            oninput="document.getElementById('add_point_preview').innerText = Math.round(this.value * 0.4).toLocaleString('id-ID')"
                            class="input input-bordered input-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 font-mono font-bold text-forest pl-9">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">Estimasi Poin (40%)</label>
                    <div class="h-8 rounded-xl bg-forest/[0.05] border border-forest/20 flex items-center justify-between px-3 font-mono font-bold text-forest text-xs">
                        <span id="add_point_preview">0</span>
                        <span class="text-[10px] font-sans font-medium text-forest">Poin</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-ink mb-1">Catatan Tambahan <span class="text-ink-soft/60 font-normal">(Opsional)</span></label>
                <textarea name="description" rows="2" placeholder="Ketentuan fisik (bersih, kering, terlepas dari tutup, dsb)..." 
                    class="textarea textarea-bordered w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 text-ink leading-relaxed">{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="btn btn-sm w-full bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold h-11 shadow-sm transition-all mt-2">
                Simpan & Publikasikan Komoditas &rarr;
            </button>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop bg-ink/40 backdrop-blur-xs"><button>close</button></form>
</dialog>
@endsection