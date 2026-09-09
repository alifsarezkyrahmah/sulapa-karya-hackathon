@extends('layouts.dashboard', ['title' => 'Riwayat Setoran — SulapaKarya'])

@section('dashboard-content')
<div class="space-y-6 text-left">
    
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-ink/5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">{{ ($isAdmin ?? false) ? 'Riwayat Setoran Semua Warga' : 'Riwayat Setoran Sampah' }}</h1>
            <p class="text-xs text-ink-soft mt-0.5">{{ ($isAdmin ?? false) ? 'Rekapitulasi seluruh transaksi setoran sampah dan status pencairan poin.' : 'Pantau status penjemputan armada, hasil uji mutu QC, dan perolehan saldo poin Anda.' }}</p>
        </div>
        @if(!($isAdmin ?? false))
            <a href="/setor-sampah" class="btn btn-sm bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold px-4 h-10 shadow-sm transition-all w-full sm:w-auto">
                Setor Sampah Baru
            </a>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- 1. TAMPILAN MOBILE (< md) -->
    <!-- ========================================================================= -->
    <div class="space-y-3.5 md:hidden">
        @forelse($deposits as $d)
            <div class="bg-white border border-ink/5 rounded-2xl p-4 shadow-none space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <span class="font-mono font-bold text-xs text-ink block">
                            {{ $d->pickup_date ? \Carbon\Carbon::parse($d->pickup_date)->translatedFormat('d M Y') : $d->created_at->translatedFormat('d M Y') }}
                        </span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[10px] text-ink-soft font-mono">{{ $d->deposit_code }}</span>
                            @if($d->pickup_time)
                                <span class="text-[10px] text-forest font-mono font-semibold">• {{ \Carbon\Carbon::parse($d->pickup_time)->format('H:i') }} WITA</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        @if($d->status === 'pending' || $d->status === 'menunggu_penjemput')
                            <span class="inline-flex items-center bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-2 py-0.5 rounded whitespace-nowrap">Menunggu Penjemput</span>
                        @elseif($d->status === 'penjemput_menuju_lokasi')
                            <span class="inline-flex items-center bg-maritime/10 text-maritime border border-maritime/20 text-[10px] font-bold px-2 py-0.5 rounded whitespace-nowrap">Kurir Menuju Lokasi</span>
                        @elseif($d->status === 'penjemput_tiba')
                            <span class="inline-flex items-center bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-bold px-2 py-0.5 rounded whitespace-nowrap">Kurir di Lokasi</span>
                        @elseif($d->status === 'sedang_diproses' || ($d->status === 'selesai' && ($d->pointTransfer->status ?? '') === 'pending'))
                            <span class="inline-flex items-center bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-2 py-0.5 rounded whitespace-nowrap">Sedang Diproses</span>
                        @elseif($d->status === 'berhasil_dikirim' || ($d->status === 'completed' || $d->status === 'selesai'))
                            <span class="inline-flex items-center bg-forest/10 text-forest border border-forest/20 text-[10px] font-bold px-2 py-0.5 rounded whitespace-nowrap">Berhasil Dikirim</span>
                        @elseif($d->status === 'ditolak' || $d->status === 'ditolak_qc' || $d->status === 'rejected')
                            <span class="inline-flex items-center bg-terracotta/10 text-terracotta border border-terracotta/20 text-[10px] font-bold px-2 py-0.5 rounded whitespace-nowrap">Ditolak QC</span>
                        @else
                            <span class="inline-flex items-center bg-gray-100 text-ink-soft text-[10px] font-bold px-2 py-0.5 rounded uppercase whitespace-nowrap">{{ str_replace('_', ' ', $d->status) }}</span>
                        @endif
                    </div>
                </div>

                @if($isAdmin ?? false)
                    <div class="text-xs pt-1 border-t border-ink/5">
                        <span class="text-[10px] text-ink-soft block font-bold uppercase">Warga Pemohon:</span>
                        <span class="font-bold text-ink">{{ $d->user->name ?? 'Warga' }}</span>
                        <span class="text-ink-soft font-mono text-[11px] block">{{ $d->user->phone ?? '-' }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-2 bg-cream/30 p-2.5 rounded-xl text-xs border border-ink/5">
                    <div>
                        <span class="text-[10px] text-ink-soft block font-semibold uppercase">Komoditas</span>
                        <span class="font-bold text-ink block truncate">{{ $d->category }}</span>
                        @if($d->sub_category)
                            <span class="text-[10px] text-ink-soft truncate block">{{ $d->sub_category }}</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-[10px] text-ink-soft block font-semibold uppercase">Berat Timbangan</span>
                        @if($d->actual_weight)
                            <span class="font-bold text-ink font-mono">{{ number_format($d->actual_weight, 2) }} kg</span>
                            <span class="text-[9px] text-forest block font-sans font-medium">(Aktual)</span>
                        @else
                            <span class="font-semibold text-ink font-mono">{{ number_format($d->estimated_weight, 2) }} kg</span>
                            <span class="text-[9px] text-ink-soft/60 block font-sans">(Estimasi)</span>
                        @endif
                    </div>
                </div>

                @if(($d->status === 'ditolak' || $d->status === 'ditolak_qc' || $d->status === 'rejected') && $d->qc_notes)
                    <div class="p-2.5 rounded-xl bg-terracotta/10 border border-terracotta/20 text-[11px] text-terracotta leading-relaxed">
                        <span class="font-bold block text-[10px] uppercase">Alasan Penolakan:</span>
                        {{ $d->qc_notes }}
                    </div>
                @endif

                <div class="flex items-center justify-between pt-1 border-t border-ink/5">
                    <div>
                        <span class="text-[10px] text-ink-soft block">Poin Reward:</span>
                        @if(in_array($d->status, ['berhasil_dikirim', 'completed', 'selesai']))
                            <span class="text-forest font-bold font-mono text-sm">+{{ number_format($d->points_earned ?? 0) }} Poin</span>
                        @elseif($d->status === 'sedang_diproses')
                            <span class="text-amber-700 font-medium font-sans text-xs">+{{ number_format($d->points_earned ?? 0) }} (Pending)</span>
                        @elseif(in_array($d->status, ['ditolak', 'ditolak_qc', 'rejected']))
                            <span class="text-terracotta text-xs font-sans">0 Poin</span>
                        @else
                            <span class="text-ink-soft/40 text-[11px] font-sans">Menunggu kurir</span>
                        @endif
                    </div>

                    @if(in_array($d->status, ['pending', 'menunggu_penjemput', 'penjemput_menuju_lokasi', 'penjemput_tiba']))
                        <button type="button" onclick="document.getElementById('qr_modal_{{ $d->id }}').showModal()"
                            class="btn btn-xs bg-forest hover:bg-forest-dark text-white border-none rounded-lg text-[10px] font-semibold px-3 py-1 shadow-none">
                            Lihat QR
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-8 text-center text-ink-soft/60 text-xs border border-ink/5">
                Belum ada transaksi setoran sampah terdaftar.
            </div>
        @endforelse
    </div>

    <!-- ========================================================================= -->
    <!-- 2. TAMPILAN DESKTOP (>= md): TABEL TERSTRUKTUR & RAPI -->
    <!-- ========================================================================= -->
    <div class="hidden md:block bg-white border border-ink/10 rounded-2xl p-6 shadow-none">
        <div class="overflow-x-auto">
            <table class="table w-full text-xs">
                <thead>
                    <tr class="bg-cream/40 text-ink-soft border-b border-ink/5 font-semibold text-[10px] uppercase tracking-wider">
                        @if($isAdmin ?? false)<th class="py-3 pl-4 w-44">Warga</th>@endif
                        <th class="py-3 {{ ($isAdmin ?? false) ? '' : 'pl-4' }} w-48">Jadwal & Kode</th>
                        <th class="py-3">Komoditas</th>
                        <th class="py-3 w-36">Berat Timbangan</th>
                        <th class="py-3 text-center w-44">Status Alur</th>
                        <th class="py-3 text-center w-28">QR Code</th>
                        <th class="py-3 pr-4 text-right w-36">Poin Reward</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/5 font-medium">
                    @forelse($deposits as $d)
                        <tr class="hover:bg-cream/20 transition-colors">
                            @if($isAdmin ?? false)
                            <td class="py-4 pl-4 align-middle">
                                <span class="font-bold text-ink block text-xs">{{ $d->user->name ?? 'Warga' }}</span>
                                <span class="text-[10px] text-ink-soft font-mono">{{ $d->user->phone ?? '-' }}</span>
                            </td>
                            @endif

                            <!-- Kolom Jadwal & Kode -->
                            <td class="py-4 {{ ($isAdmin ?? false) ? '' : 'pl-4' }} align-middle">
                                <span class="font-bold text-ink text-xs block whitespace-nowrap">
                                    {{ $d->pickup_date ? \Carbon\Carbon::parse($d->pickup_date)->translatedFormat('d M Y') : $d->created_at->translatedFormat('d M Y') }}
                                </span>
                                <div class="flex items-center gap-1.5 mt-1 font-mono text-[11px]">
                                    <span class="text-ink-soft font-semibold">{{ $d->deposit_code }}</span>
                                    @if($d->pickup_time)
                                        <span class="text-forest font-bold whitespace-nowrap">• {{ \Carbon\Carbon::parse($d->pickup_time)->format('H:i') }} WITA</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Kolom Komoditas -->
                            <td class="py-4 align-middle">
                                <div class="space-y-1">
                                    <div class="inline-block bg-cream border border-ink/10 text-ink text-[11px] font-semibold px-2.5 py-1 rounded-lg leading-tight max-w-[240px]">
                                        {{ $d->category }}
                                    </div>
                                    @if($d->sub_category)
                                        <span class="text-[11px] text-ink-soft block truncate max-w-[220px]">{{ $d->sub_category }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Kolom Berat -->
                            <td class="py-4 align-middle font-mono">
                                @if($d->actual_weight)
                                    <span class="font-black text-sm text-ink block">{{ number_format($d->actual_weight, 2) }} kg</span>
                                    <span class="text-[10px] text-forest font-sans font-semibold block mt-0.5">(Hasil Timbang)</span>
                                @else
                                    <span class="font-bold text-sm text-ink block">{{ number_format($d->estimated_weight, 2) }} kg</span>
                                    <span class="text-[10px] text-ink-soft/70 font-sans block mt-0.5">(Estimasi)</span>
                                @endif
                            </td>

                            <!-- Kolom Status Alur -->
                            <td class="py-4 text-center align-middle">
                                @if($d->status === 'pending' || $d->status === 'menunggu_penjemput')
                                    <span class="inline-flex items-center justify-center bg-amber-50 text-amber-800 border border-amber-300 text-[10px] font-bold px-3 py-1 rounded-md whitespace-nowrap">
                                        Menunggu Penjemput
                                    </span>
                                @elseif($d->status === 'penjemput_menuju_lokasi')
                                    <span class="inline-flex items-center justify-center bg-maritime/10 text-maritime border border-maritime/20 text-[10px] font-bold px-3 py-1 rounded-md whitespace-nowrap">
                                        Kurir Menuju Lokasi
                                    </span>
                                @elseif($d->status === 'penjemput_tiba')
                                    <span class="inline-flex items-center justify-center bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-bold px-3 py-1 rounded-md whitespace-nowrap">
                                        Kurir di Lokasi
                                    </span>
                                @elseif($d->status === 'sedang_diproses' || ($d->status === 'selesai' && ($d->pointTransfer->status ?? '') === 'pending'))
                                    <div class="inline-block">
                                        <span class="inline-flex items-center justify-center bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-3 py-1 rounded-md whitespace-nowrap">
                                            Sedang Diproses
                                        </span>
                                        <span class="text-[9px] text-ink-soft block mt-0.5">Audit Poin (Maks. 24 Jam)</span>
                                    </div>
                                @elseif($d->status === 'berhasil_dikirim' || ($d->status === 'completed' || $d->status === 'selesai'))
                                    <span class="inline-flex items-center justify-center bg-forest/10 text-forest border border-forest/20 text-[10px] font-bold px-3 py-1 rounded-md whitespace-nowrap">
                                        Berhasil Dikirim
                                    </span>
                                @elseif($d->status === 'ditolak' || $d->status === 'ditolak_qc' || $d->status === 'rejected')
                                    <div class="inline-block">
                                        <span class="inline-flex items-center justify-center bg-terracotta/10 text-terracotta border border-terracotta/20 text-[10px] font-bold px-3 py-1 rounded-md whitespace-nowrap">
                                            Ditolak QC
                                        </span>
                                        @if($d->qc_notes)
                                            <span class="text-[9px] text-terracotta block mt-0.5 max-w-[140px] mx-auto truncate" title="{{ $d->qc_notes }}">
                                                {{ $d->qc_notes }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center justify-center bg-cream text-ink border border-ink/10 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase whitespace-nowrap">
                                        {{ str_replace('_', ' ', $d->status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Kolom QR Code -->
                            <td class="py-4 text-center align-middle">
                                @if(in_array($d->status, ['pending', 'menunggu_penjemput', 'penjemput_menuju_lokasi', 'penjemput_tiba']))
                                    <button type="button" onclick="document.getElementById('qr_modal_{{ $d->id }}').showModal()" 
                                        class="btn btn-xs bg-cream hover:bg-forest hover:text-white text-ink border border-ink/15 rounded-lg text-[10px] font-bold px-3 py-1 shadow-none transition-colors whitespace-nowrap">
                                        Tampilkan QR
                                    </button>
                                @else
                                    <span class="text-xs text-ink-soft/40 font-mono">-</span>
                                @endif
                            </td>

                            <!-- Kolom Poin Reward -->
                            <td class="py-4 pr-4 text-right align-middle font-mono font-bold">
                                @if(in_array($d->status, ['berhasil_dikirim', 'completed', 'selesai']))
                                    <span class="text-forest text-sm">+{{ number_format($d->points_earned ?? 0) }} Poin</span>
                                @elseif($d->status === 'sedang_diproses')
                                    <span class="text-amber-700 text-xs font-medium font-sans">+{{ number_format($d->points_earned ?? 0) }} (Pending)</span>
                                @elseif(in_array($d->status, ['ditolak', 'ditolak_qc', 'rejected']))
                                    <span class="text-terracotta text-xs font-sans font-medium">0 Poin</span>
                                @else
                                    <span class="text-ink-soft/50 text-[11px] font-sans font-normal whitespace-nowrap">Menunggu timbang</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ ($isAdmin ?? false) ? 7 : 6 }}" class="py-12 text-center text-ink-soft/60 text-xs font-medium">
                                Belum ada transaksi setoran sampah terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL QR CODE: PRESISI DI TENGAH LAYAR -->
<!-- ========================================================================= -->
@foreach($deposits as $d)
    @if(in_array($d->status, ['pending', 'menunggu_penjemput', 'penjemput_menuju_lokasi', 'penjemput_tiba']))
        <dialog id="qr_modal_{{ $d->id }}" class="modal modal-middle">
            <div class="modal-box w-11/12 max-w-xs bg-white rounded-3xl border border-ink/10 p-5 sm:p-6 text-center relative shadow-2xl overflow-hidden my-auto">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-3.5 top-3.5 text-ink-soft hover:bg-cream">✕</button>
                </form>

                <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-forest bg-forest/10 px-2 py-0.5 rounded">
                    Verifikasi Setoran
                </span>
                <h3 class="font-bold text-base text-ink mt-1.5">QR Code Penjemputan</h3>
                <p class="text-[11px] text-ink-soft mt-0.5">Tunjukkan barcode ini kepada kurir saat tiba di lokasi untuk validasi.</p>
                
                <div class="bg-cream/40 p-3.5 rounded-2xl border border-ink/5 my-3.5 flex items-center justify-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($d->deposit_code) }}&color=241F18&bgcolor=FAF6EF"
                         alt="QR {{ $d->deposit_code }}"
                         class="w-36 h-36 sm:w-40 sm:h-40 rounded-xl object-contain shadow-2xs" loading="lazy" />
                </div>
                
                <div class="bg-cream/30 py-2 px-3 rounded-xl border border-ink/5 font-mono text-xs font-bold text-forest select-all">
                    {{ $d->deposit_code }}
                </div>
            </div>
            <form method="dialog" class="modal-backdrop bg-ink/40 backdrop-blur-xs"><button>close</button></form>
        </dialog>
    @endif
@endforeach

@endsection