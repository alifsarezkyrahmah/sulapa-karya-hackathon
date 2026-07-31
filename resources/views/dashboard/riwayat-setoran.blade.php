@extends('layouts.dashboard', ['title' => 'Riwayat Setoran — SulapaKarya'])

@section('dashboard-content')
<div class="space-y-6 animate-fadeIn">
    
    <div class="text-left">
        <h1 class="font-display font-extrabold text-2xl text-ink tracking-tight">{{ ($isAdmin ?? false) ? 'Riwayat Setoran Semua Warga' : 'Riwayat Setoran Anda' }}</h1>
        <p class="text-xs text-ink-soft/80 font-medium mt-1">{{ ($isAdmin ?? false) ? 'Rekap seluruh setoran sampah yang diajukan oleh warga.' : 'Pantau status penjemputan dan keuntungan dari setiap sampah yang Anda setor.' }}</p>
    </div>

    <div class="bg-white border border-ink/5 rounded-[1.5rem] p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto rounded-xl border border-ink/5">
            <table class="table w-full text-sm">
                <thead>
                    <tr class="border-b border-ink/5 text-ink/70 font-bold uppercase tracking-wider text-xs bg-cream/60">
                        @if($isAdmin ?? false)<th class="py-3.5 pl-5">Warga</th>@endif
                        <th class="py-3.5 {{ ($isAdmin ?? false) ? '' : 'pl-5' }}">Tanggal & Kode</th>
                        <th class="py-3.5">Jenis Sampah</th>
                        <th class="py-3.5">Berat Timbangan</th>
                        <th class="py-3.5 text-center">Status</th>
                        <th class="py-3.5 pr-5 text-right">Hadiah / Profit</th>
                    </tr>
                </thead>
                <tbody class="font-medium text-ink/90">
                    @forelse($deposits as $d)
                        <tr class="hover:bg-cream/10 border-b border-ink/5 transition-colors">

                            @if($isAdmin ?? false)
                            <td class="py-4 pl-5">
                                <span class="font-bold text-ink block">{{ $d->user->name ?? 'Anonim' }}</span>
                                <span class="text-[10px] text-ink-soft/60 block">{{ $d->user->phone ?? '-' }}</span>
                            </td>
                            @endif

                            <td class="py-4 {{ ($isAdmin ?? false) ? '' : 'pl-5' }}">
                                <span class="text-ink-soft font-bold block">{{ $d->created_at->translatedFormat('d M Y') }}</span>
                                <span class="text-[10px] text-ink-soft/60 font-mono mt-0.5 block">{{ $d->deposit_code }}</span>
                            </td>

                            <td class="py-4">
                                <span class="inline-flex items-center gap-1.5 font-semibold capitalize">
                                    @if($d->category === 'plastik')
                                        <span class="w-2.5 h-2.5 rounded-full bg-maritime shadow-sm"></span> 
                                    @elseif($d->category === 'kertas')
                                        <span class="w-2.5 h-2.5 rounded-full bg-terracotta shadow-sm"></span> 
                                    @else
                                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500 shadow-sm"></span> 
                                    @endif
                                    {{ $d->category }}
                                </span>
                                @if($d->sub_category)
                                    <span class="text-[10px] text-ink-soft block mt-0.5 ml-4 truncate max-w-[150px]">{{ $d->sub_category }}</span>
                                @endif
                            </td>

                            <td class="py-4 font-mono font-bold text-ink">
                                @if($d->actual_weight)
                                    {{ $d->actual_weight }} kg <span class="text-[9px] text-forest font-sans block">(Aktual)</span>
                                @else
                                    {{ $d->estimated_weight }} kg <span class="text-[9px] text-ink-soft/60 font-sans block">(Estimasi)</span>
                                @endif
                            </td>

                            <!-- Ganti bagian penampil status di dalam loop tabel riwayat -->
                            <td class="py-4">
                                @if($d->status === 'pending' || $d->status === 'menunggu_admin')
                                    <span class="badge bg-amber-100 text-amber-700 border-none text-[10px] font-bold px-2 py-1.5 rounded-md">Menunggu Verifikasi Admin</span>
                                @elseif($d->status === 'menunggu_penjemput')
                                    <span class="badge bg-maritime/10 text-maritime border-none text-[10px] font-bold px-2 py-1.5 rounded-md">Kurir Segera Menjemput</span>
                                @elseif($d->status === 'penjemput_menuju_lokasi')
                                    <span class="badge bg-blue-100 text-blue-700 border-none text-[10px] font-bold px-2 py-1.5 rounded-md">Kurir dalam Perjalanan</span>
                                @elseif($d->status === 'penjemput_tiba')
                                    <span class="badge bg-purple-100 text-purple-700 border-none text-[10px] font-bold px-2 py-1.5 rounded-md">Kurir Tiba di Lokasi</span>
                                @elseif($d->status === 'selesai')
                                    <span class="badge bg-forest/20 text-forest-dark border-none text-[10px] font-bold px-2 py-1.5 rounded-md">✓ Berhasil & Poin Cair</span>
                                @elseif($d->status === 'ditolak')
                                    <span class="badge bg-terracotta/10 text-terracotta border-none text-[10px] font-bold px-2 py-1.5 rounded-md">Ditolak</span>
                                @else
                                    <span class="badge badge-ghost text-[10px]">{{ strtoupper($d->status) }}</span>
                                @endif
                            </td>

                            <td class="py-4 pr-5 text-right font-bold font-mono tracking-wide">
                                @if(in_array($d->status, ['verified', 'completed']))
                                    @if($d->reward_type === 'points')
                                        <span class="text-maritime">+{{ number_format($d->points_earned, 0, ',', '.') }} Poin</span>
                                    @else
                                        <span class="text-forest">Rp {{ number_format($d->cash_earned, 0, ',', '.') }}</span>
                                    @endif
                                @elseif($d->status === 'rejected')
                                    <span class="text-terracotta text-[11px] font-sans">-</span>
                                @else
                                    <span class="text-ink-soft/40 text-[11px] font-sans italic">Menunggu verifikasi</span>
                                @endif
                            </td>
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ ($isAdmin ?? false) ? 6 : 5 }}" class="py-12 text-center text-ink-soft/60 font-medium text-xs">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="text-2xl opacity-50">🍃</span>
                                    <p>{{ ($isAdmin ?? false) ? 'Belum ada riwayat setoran dari warga.' : 'Belum ada riwayat setoran. Ayo mulai pilah sampahmu hari ini!' }}</p>
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