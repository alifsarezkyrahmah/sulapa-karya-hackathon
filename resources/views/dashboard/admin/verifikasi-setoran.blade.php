@extends('layouts.dashboard', ['title' => 'Verifikasi Setoran — SulapaKarya'])

@section('dashboard-content')
@php
    $selectedTab = request('tab', 'all');
    
    // Filter koleksi berdasarkan tab
    $filteredDeposits = $deposits->filter(function($item) use ($selectedTab) {
        $isBiz = ($item->deposit_type === 'business') || (($item->user->business_status ?? '') === 'approved');
        if ($selectedTab === 'pro') return $isBiz;
        if ($selectedTab === 'regular') return !$isBiz;
        return true;
    });

    $countAll = $deposits->count();
    $countPro = $deposits->filter(fn($i) => ($i->deposit_type === 'business') || (($i->user->business_status ?? '') === 'approved'))->count();
    $countReg = $countAll - $countPro;
@endphp

<div class="space-y-6 text-left">
    
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-ink/5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">Verifikasi Setoran Masuk</h1>
            <p class="text-xs text-ink-soft mt-0.5">Tinjau kelayakan sampah daur ulang warga/mitra dan delegasikan armada kurir untuk penjemputan lapangan.</p>
        </div>
        <span class="badge bg-forest/10 text-forest border border-forest/20 text-[11px] font-semibold px-3 py-1 rounded-lg">
            Panel Verifikasi Admin
        </span>
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

    <!-- Tab Pembeda: Semua vs PRO vs Warga -->
    <div class="flex items-center gap-2 border-b border-ink/10 pb-3 overflow-x-auto">
        <a href="?tab=all" class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-2 {{ $selectedTab === 'all' ? 'bg-forest text-white shadow-sm' : 'bg-white border border-ink/10 text-ink hover:bg-cream/50' }}">
            <span>Semua Setoran</span>
            <span class="badge badge-xs {{ $selectedTab === 'all' ? 'bg-white/20 text-white' : 'bg-ink/5 text-ink-soft' }} border-none font-mono font-bold">{{ $countAll }}</span>
        </a>
        <a href="?tab=pro" class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-2 {{ $selectedTab === 'pro' ? 'bg-amber-400 text-amber-950 shadow-sm' : 'bg-white border border-amber-300 text-amber-900 hover:bg-amber-50' }}">
            <span>★ Mitra Bisnis PRO</span>
            <span class="badge badge-xs {{ $selectedTab === 'pro' ? 'bg-amber-950/20 text-amber-950' : 'bg-amber-100 text-amber-900' }} border-none font-mono font-bold">{{ $countPro }}</span>
        </a>
        <a href="?tab=regular" class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-2 {{ $selectedTab === 'regular' ? 'bg-ink text-white shadow-sm' : 'bg-white border border-ink/10 text-ink hover:bg-cream/50' }}">
            <span>Warga Reguler</span>
            <span class="badge badge-xs {{ $selectedTab === 'regular' ? 'bg-white/20 text-white' : 'bg-ink/5 text-ink-soft' }} border-none font-mono font-bold">{{ $countReg }}</span>
        </a>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. TAMPILAN MOBILE (< md) -->
    <!-- ========================================================================= -->
    <div class="space-y-3.5 md:hidden">
        @forelse($filteredDeposits as $d)
            @php 
                $warga = $d->user ?? \App\Models\User::find($d->user_id); 
                $kurir = $d->penjemput ?? ($d->penjemput_id ? \App\Models\User::find($d->penjemput_id) : null);
                $isBusiness = ($d->deposit_type === 'business') || (($warga->business_status ?? '') === 'approved');
            @endphp
            <div class="bg-white border-2 {{ $isBusiness ? 'border-amber-400/80 bg-amber-50/20' : 'border-ink/10' }} rounded-2xl p-4 shadow-none space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                        @if($isBusiness)
                            <span class="badge badge-sm bg-amber-400 text-amber-950 font-black border-none text-[9px] font-mono uppercase px-2">★ PRO B2B</span>
                        @else
                            <span class="badge badge-sm bg-cream text-ink border border-ink/10 font-bold text-[9px] font-mono px-2">WARGA</span>
                        @endif
                        <span class="text-[10px] text-ink-soft font-mono">{{ $d->deposit_code }}</span>
                    </div>

                    <div>
                        @if($d->status === 'pending' || $d->status === 'menunggu_admin')
                            <span class="inline-flex items-center bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-2 py-0.5 rounded-md">Menunggu Verifikasi</span>
                        @elseif($d->status === 'menunggu_penjemput')
                            <span class="inline-flex items-center bg-maritime/10 text-maritime border border-maritime/20 text-[10px] font-bold px-2 py-0.5 rounded-md">Menunggu Kurir</span>
                        @elseif($d->status === 'penjemput_menuju_lokasi')
                            <span class="inline-flex items-center bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold px-2 py-0.5 rounded-md">Kurir OTW</span>
                        @elseif($d->status === 'penjemput_tiba')
                            <span class="inline-flex items-center bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-bold px-2 py-0.5 rounded-md">Kurir di Lokasi</span>
                        @elseif($d->status === 'sedang_diproses')
                            <span class="inline-flex items-center bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-2 py-0.5 rounded-md">Sedang Diproses</span>
                        @elseif(in_array($d->status, ['berhasil_dikirim', 'completed', 'selesai']))
                            <span class="inline-flex items-center bg-forest/10 text-forest border border-forest/20 text-[10px] font-bold px-2 py-0.5 rounded-md">Selesai</span>
                        @elseif(in_array($d->status, ['ditolak', 'ditolak_qc', 'rejected']))
                            <span class="inline-flex items-center bg-terracotta/10 text-terracotta border border-terracotta/20 text-[10px] font-bold px-2 py-0.5 rounded-md">Ditolak</span>
                        @else
                            <span class="badge badge-xs bg-cream text-ink text-[10px]">{{ $d->status }}</span>
                        @endif
                    </div>
                </div>

                <div class="text-xs pt-1 border-t border-ink/5">
                    <span class="text-[10px] text-ink-soft block font-bold uppercase tracking-wider">{{ $isBusiness ? 'Unit Usaha Mitra:' : 'Warga Pemohon:' }}</span>
                    <span class="font-bold text-ink text-sm block mt-0.5">{{ $isBusiness && $warga->business_name ? $warga->business_name : ($warga->name ?? 'Anonim') }}</span>
                    @if($isBusiness)
                        <span class="text-[11px] text-amber-900 font-semibold block">PIC: {{ $warga->name ?? '-' }} ({{ $warga->phone ?? '-' }})</span>
                    @else
                        <span class="text-ink-soft font-mono text-[11px] block">{{ $warga->phone ?? '-' }}</span>
                    @endif
                </div>

                <div class="flex items-center gap-3 bg-cream/30 p-2.5 rounded-xl border border-ink/5">
                    <div class="w-14 h-14 rounded-lg overflow-hidden bg-white shrink-0 border border-ink/10 cursor-pointer"
                        onclick="document.getElementById('photo_modal_{{ $d->id }}').showModal()">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($d->photo_path) }}" alt="Foto" class="w-full h-full object-cover">
                    </div>
                    <div class="min-w-0 flex-1 text-xs">
                        <span class="font-bold text-ink block truncate">{{ $d->category }}</span>
                        <span class="font-mono font-bold text-forest text-xs mt-0.5 block">Est: {{ number_format($d->estimated_weight, 1) }} kg</span>
                        <span class="text-[10px] text-ink-soft block">Jadwal: {{ $d->pickup_date ? \Carbon\Carbon::parse($d->pickup_date)->translatedFormat('d M Y') : '-' }}</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-ink/5 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-ink-soft block">Kurir Ditugaskan:</span>
                        @if($kurir)
                            <strong class="text-xs text-maritime font-semibold">{{ $kurir->name }}</strong>
                        @else
                            <span class="text-xs text-ink-soft/60 italic">Belum ditentukan</span>
                        @endif
                    </div>

                    @if($d->status === 'pending' || $d->status === 'menunggu_admin')
                        <button type="button" onclick="document.getElementById('verify_modal_{{ $d->id }}').showModal()"
                            class="btn btn-xs bg-forest hover:bg-forest-dark text-white border-none rounded-lg text-xs font-bold px-3 py-1.5 shadow-none">
                            Tugaskan &rarr;
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-8 text-center text-ink-soft/60 text-xs border border-ink/5">
                Tidak ada data setoran yang sesuai dengan tab filter.
            </div>
        @endforelse
    </div>

    <!-- ========================================================================= -->
    <!-- 2. TAMPILAN DESKTOP (>= md): TABEL RAPI & TERSTRUKTUR -->
    <!-- ========================================================================= -->
    <div class="hidden md:block bg-white border border-ink/10 rounded-2xl p-6 shadow-none">
        <div class="overflow-x-auto">
            <table class="table w-full text-xs">
                <thead>
                    <tr class="bg-cream/40 text-ink-soft border-b border-ink/5 font-semibold text-[10px] uppercase tracking-wider">
                        <th class="py-3 pl-4 w-24">Tipe</th>
                        <th class="py-3">Pemohon / Unit Usaha</th>
                        <th class="py-3 w-56">Detail Sampah & Est.</th>
                        <th class="py-3 text-center w-20">Foto Wujud</th>
                        <th class="py-3 w-40">Jadwal & Wilayah</th>
                        <th class="py-3 text-center w-36">Status</th>
                        <th class="py-3 pr-4 text-right w-36">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/5 font-medium">
                    @forelse($filteredDeposits as $d)
                        @php 
                            $warga = $d->user ?? \App\Models\User::find($d->user_id); 
                            $kurir = $d->penjemput ?? ($d->penjemput_id ? \App\Models\User::find($d->penjemput_id) : null);
                            $isBusiness = ($d->deposit_type === 'business') || (($warga->business_status ?? '') === 'approved');
                        @endphp
                        <tr class="hover:bg-cream/20 transition-colors {{ $isBusiness ? 'bg-amber-400/[0.03]' : '' }}">
                            
                            <!-- Tipe Badge -->
                            <td class="py-3.5 pl-4 align-middle">
                                @if($isBusiness)
                                    <span class="inline-flex items-center justify-center bg-amber-400 text-amber-950 font-mono font-black text-[9px] px-2.5 py-1 rounded shadow-2xs whitespace-nowrap">
                                        ★ PRO B2B
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center bg-cream text-ink border border-ink/10 font-mono font-bold text-[9px] px-2.5 py-1 rounded whitespace-nowrap">
                                        WARGA
                                    </span>
                                @endif
                            </td>

                            <!-- Pemohon -->
                            <td class="py-3.5 align-middle">
                                <span class="font-bold text-ink block text-xs truncate max-w-[170px]">
                                    {{ $isBusiness && $warga->business_name ? $warga->business_name : ($warga->name ?? 'Anonim') }}
                                </span>
                                @if($isBusiness)
                                    <span class="text-[10px] text-amber-900 block truncate font-sans">
                                        PIC: {{ $warga->name ?? '-' }} ({{ $warga->phone ?? '-' }})
                                    </span>
                                @else
                                    <span class="text-[10px] text-ink-soft font-mono block">{{ $warga->phone ?? '-' }}</span>
                                @endif
                            </td>

                            <!-- Detail Sampah & Estimasi -->
                            <td class="py-3.5 align-middle">
                                <div class="space-y-1">
                                    <div class="inline-block bg-cream border border-ink/10 text-ink text-[10px] font-semibold px-2 py-0.5 rounded leading-tight">
                                        {{ $d->category }}
                                    </div>
                                    @if($d->sub_category)
                                        <span class="text-[10px] text-ink-soft block truncate max-w-[200px]">{{ $d->sub_category }}</span>
                                    @endif
                                    <div class="font-mono font-bold text-forest text-xs">
                                        Est: {{ number_format($d->estimated_weight, 1) }} kg
                                    </div>
                                </div>
                            </td>

                            <!-- Foto -->
                            <td class="py-3.5 text-center align-middle">
                                <div class="w-11 h-11 mx-auto rounded-xl overflow-hidden border border-ink/10 cursor-pointer hover:opacity-80 transition-opacity bg-cream/40 shrink-0"
                                    onclick="document.getElementById('photo_modal_{{ $d->id }}').showModal()" title="Klik untuk lihat foto asli">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($d->photo_path) }}" alt="Foto Sampah" class="w-full h-full object-cover">
                                </div>
                            </td>

                            <!-- Jadwal & Lokasi -->
                            <td class="py-3.5 align-middle">
                                <span class="font-semibold text-ink block text-xs">
                                    {{ $d->pickup_date ? \Carbon\Carbon::parse($d->pickup_date)->translatedFormat('d M Y') : 'Belum diisi' }}
                                </span>
                                <span class="text-[10px] text-ink-soft font-mono block">
                                    {{ $d->pickup_time ? \Carbon\Carbon::parse($d->pickup_time)->format('H:i') . ' WITA' : '-' }}
                                </span>
                                <span class="text-[10px] text-ink-soft/80 block truncate max-w-[140px] font-semibold">
                                    Kec. {{ $d->kecamatan ?? '-' }}
                                </span>
                            </td>

                            <!-- Status Alur -->
                            <td class="py-3.5 text-center align-middle">
                                @if($d->status === 'pending' || $d->status === 'menunggu_admin')
                                    <span class="inline-flex items-center justify-center bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">
                                        Menunggu Verifikasi
                                    </span>
                                @elseif($d->status === 'menunggu_penjemput')
                                    <span class="inline-flex items-center justify-center bg-maritime/10 text-maritime border border-maritime/20 text-[10px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">
                                        Menunggu Kurir
                                    </span>
                                @elseif($d->status === 'penjemput_menuju_lokasi')
                                    <span class="inline-flex items-center justify-center bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">
                                        Kurir OTW Lokasi
                                    </span>
                                @elseif($d->status === 'penjemput_tiba')
                                    <span class="inline-flex items-center justify-center bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">
                                        Kurir di Lokasi
                                    </span>
                                @elseif($d->status === 'sedang_diproses')
                                    <span class="inline-flex items-center justify-center bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">
                                        Sedang Diproses
                                    </span>
                                @elseif(in_array($d->status, ['berhasil_dikirim', 'completed', 'selesai']))
                                    <span class="inline-flex items-center justify-center bg-forest/10 text-forest border border-forest/20 text-[10px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">
                                        Selesai
                                    </span>
                                @elseif(in_array($d->status, ['ditolak', 'ditolak_qc', 'rejected']))
                                    <span class="inline-flex items-center justify-center bg-terracotta/10 text-terracotta border border-terracotta/20 text-[10px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center bg-cream text-ink border border-ink/10 text-[10px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap uppercase">
                                        {{ str_replace('_', ' ', $d->status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi / Info Kurir -->
                            <td class="py-3.5 pr-4 text-right align-middle">
                                @if($d->status === 'pending' || $d->status === 'menunggu_admin')
                                    <button type="button" onclick="document.getElementById('verify_modal_{{ $d->id }}').showModal()"
                                        class="btn btn-xs bg-forest hover:bg-forest-dark text-white border-none rounded-lg text-xs font-bold px-3 py-1.5 shadow-none whitespace-nowrap">
                                        Tugaskan Kurir
                                    </button>
                                @else
                                    @if($kurir)
                                        <span class="text-[10px] text-ink-soft block font-medium">Kurir:</span>
                                        <span class="font-bold text-maritime text-xs block truncate max-w-[120px] ml-auto">{{ $kurir->name }}</span>
                                    @else
                                        <span class="text-[10px] text-ink-soft/40 font-mono">-</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-ink-soft/60 text-xs font-medium">
                                Tidak ada antrean setoran yang cocok dengan filter saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL DETAIL POPUP -->
<!-- ========================================================================= -->
@foreach($deposits as $d)
    @php 
        $warga = $d->user ?? \App\Models\User::find($d->user_id); 
        $isBiz = ($d->deposit_type === 'business') || (($warga->business_status ?? '') === 'approved');
    @endphp

    <!-- 1. Modal Foto Bukti Fisik -->
    <dialog id="photo_modal_{{ $d->id }}" class="modal modal-middle">
        <div class="modal-box w-11/12 max-w-lg sm:max-w-xl bg-white rounded-3xl border border-ink/10 p-5 sm:p-6 text-center relative shadow-2xl overflow-hidden my-auto">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-3.5 top-3.5 text-ink-soft hover:bg-cream z-20">✕</button>
            </form>
            
            <div class="text-left pb-3 mb-3 border-b border-ink/5">
                <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-forest bg-forest/10 px-2 py-0.5 rounded">
                    Bukti Fisik Sampah
                </span>
            </div>
            
            <div class="w-full max-h-[60vh] rounded-2xl overflow-hidden bg-cream/30 border border-ink/5 flex items-center justify-center p-2">
                <img src="{{ \Illuminate\Support\Facades\Storage::url($d->photo_path) }}" 
                     alt="Foto Sampah {{ $d->deposit_code }}" 
                     class="w-auto h-auto max-w-full max-h-[56vh] object-contain rounded-xl shadow-xs" loading="lazy" />
            </div>

            <div class="mt-3 flex items-center justify-between text-[11px] text-ink-soft">
                <span>Est. Berat: <strong class="text-ink font-mono">{{ number_format($d->estimated_weight, 1) }} kg</strong></span>
                <a href="{{ \Illuminate\Support\Facades\Storage::url($d->photo_path) }}" target="_blank" class="text-forest font-semibold hover:underline">
                    Buka File Asli ↗
                </a>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-ink/40 backdrop-blur-xs"><button>close</button></form>
    </dialog>

    <!-- 2. Modal Verifikasi & Delegasi Kurir -->
    @if($d->status === 'pending' || $d->status === 'menunggu_admin')
    <dialog id="verify_modal_{{ $d->id }}" class="modal modal-middle">
        <div class="modal-box w-11/12 max-w-lg bg-white rounded-3xl border border-ink/10 p-5 sm:p-7 text-left shadow-2xl overflow-y-auto max-h-[90vh] my-auto">
            
            <div class="flex items-start justify-between pb-3 border-b border-ink/5">
                <div>
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-forest bg-forest/10 px-2 py-0.5 rounded">
                        Formulir Penugasan Kurir
                    </span>
                    <h3 class="font-bold text-base sm:text-lg text-ink mt-1">
                        Verifikasi Setoran {{ $isBiz ? 'Mitra PRO B2B' : 'Warga Reguler' }}
                    </h3>
                </div>
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost text-ink-soft hover:bg-cream">✕</button>
                </form>
            </div>

            <div class="space-y-4 pt-3.5">
                
                <!-- Ringkasan Pemohon & Sampah -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-cream/30 p-3.5 rounded-2xl border border-ink/5 text-xs">
                    <div>
                        <span class="text-[10px] text-ink-soft block font-bold uppercase tracking-wider">{{ $isBiz ? 'Unit Usaha' : 'Nama Pemohon' }}</span>
                        <span class="font-bold text-ink block text-sm mt-0.5">{{ $isBiz && $warga->business_name ? $warga->business_name : ($warga->name ?? 'Anonim') }}</span>
                        <span class="text-[10px] text-ink-soft font-mono">{{ $warga->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-ink-soft block font-bold uppercase tracking-wider">Kategori & Bobot</span>
                        <span class="font-bold text-ink block text-xs mt-0.5 capitalize">{{ $d->category }}</span>
                        <span class="font-mono text-forest font-bold text-xs">{{ number_format($d->estimated_weight, 1) }} kg (Estimasi)</span>
                    </div>
                </div>

                <!-- Alamat & Jadwal -->
                <div class="p-3.5 rounded-2xl border border-ink/5 bg-white space-y-2 text-xs">
                    <div>
                        <span class="text-[10px] text-ink-soft block font-bold uppercase tracking-wider">Alamat Titik Jemput:</span>
                        <p class="text-ink font-medium mt-0.5 leading-relaxed">{{ $d->pickup_address }}</p>
                        <span class="text-[11px] text-forest font-bold font-mono block mt-1">
                            Kecamatan {{ $d->kecamatan ?? '-' }}, Kelurahan {{ $d->kelurahan ?? '-' }}
                        </span>
                    </div>
                    <div class="pt-2 border-t border-ink/5 flex items-center justify-between text-xs">
                        <span class="text-ink-soft">Jadwal Diminta:</span>
                        <span class="font-bold text-ink font-mono">
                            {{ $d->pickup_date ? \Carbon\Carbon::parse($d->pickup_date)->translatedFormat('d M Y') : '-' }} 
                            ({{ $d->pickup_time ? \Carbon\Carbon::parse($d->pickup_time)->format('H:i') . ' WITA' : '-' }})
                        </span>
                    </div>
                </div>

                <!-- Formulir Keputusan Admin -->
                <form action="{{ route('admin.deposits.approve', $d->id) }}" method="POST" class="bg-forest/[0.03] p-4 rounded-2xl border border-forest/15 space-y-3">
                    @csrf
                    <span class="text-[10px] font-bold text-forest uppercase tracking-wider block">Tindakan Delegasi Armada</span>

                    <!-- Dropdown Keputusan -->
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">Keputusan <span class="text-terracotta">*</span></label>
                        <select name="keputusan" required onchange="toggleKurir(this.value, 'kurir_box_{{ $d->id }}')"
                            class="select select-bordered select-sm w-full rounded-xl text-xs bg-white text-ink font-semibold focus:outline-none focus:border-forest">
                            <option value="" disabled selected>-- Pilih Tindakan --</option>
                            <option value="terima">Terima & Tugaskan Kurir Lapangan</option>
                            <option value="tolak">Tolak Setoran (Tidak memenuhi standar SOP)</option>
                        </select>
                    </div>

                    <!-- Dropdown Pilihan Kurir Berdasarkan Beban Nyata -->
                    <div id="kurir_box_{{ $d->id }}" class="hidden space-y-1 pt-1">
                        <label class="block text-xs font-semibold text-maritime mb-1">
                            Pilih Armada Kurir <span class="text-terracotta">*</span>
                        </label>
                        <select name="penjemput_id" class="select select-bordered select-sm w-full rounded-xl text-xs bg-white text-ink font-medium focus:outline-none focus:border-maritime">
                            <option value="" disabled selected>-- Pilih Kurir yang Bertugas --</option>
                            @foreach($penjemputs as $kurirItem)
                                @php
                                    $isSameArea = strtolower($kurirItem->kecamatan ?? '') === strtolower($d->kecamatan ?? '');
                                    $activeCount = $kurirItem->active_missions_count ?? 0;
                                    $isOverloaded = ($activeCount >= 3);
                                @endphp
                                <option value="{{ $kurirItem->id }}" {{ $isOverloaded ? 'class=text-terracotta' : '' }}>
                                    {{ $kurirItem->name }} 
                                    &bull; Kec. {{ $kurirItem->kecamatan ?? 'Makassar' }}
                                    [{{ $activeCount }} Tugas Aktif]
                                    {{ $isSameArea ? '★ Zonasi Sesuai' : '' }}
                                    {{ $isOverloaded ? '(Penuh)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-ink-soft block mt-1">
                            Target rute: <strong>Kec. {{ $d->kecamatan }}</strong>. Utamakan kurir dengan beban terendah dan tanda zonasi sesuai.
                        </span>
                    </div>

                    <!-- Catatan Instruksi -->
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">Catatan Tambahan untuk Kurir (Opsional)</label>
                        <textarea name="admin_notes" rows="2" placeholder="Catatan panduan akses rute..."
                            class="textarea textarea-bordered w-full rounded-xl text-xs bg-white focus:outline-none focus:border-forest text-ink leading-relaxed"></textarea>
                    </div>

                    <!-- Tombol Eksekusi -->
                    <button type="submit" class="btn btn-sm w-full bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-bold h-11 shadow-sm transition-all mt-2">
                        Simpan & Kirim Misi ke Kurir &rarr;
                    </button>
                </form>

            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-ink/40 backdrop-blur-xs"><button>close</button></form>
    </dialog>
    @endif
@endforeach

<script>
    function toggleKurir(keputusan, boxId) {
        const box = document.getElementById(boxId);
        const kurirSelect = box ? box.querySelector('select[name="penjemput_id"]') : null;
        if (!box) return;

        if (keputusan === 'terima') {
            box.classList.remove('hidden');
            if (kurirSelect) kurirSelect.setAttribute('required', 'required');
        } else {
            box.classList.add('hidden');
            if (kurirSelect) {
                kurirSelect.removeAttribute('required');
                kurirSelect.value = '';
            }
        }
    }
</script>
@endsection