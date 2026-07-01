@extends('layouts.dashboard', ['title' => 'Riwayat Setoran — SulapaKarya Macca'])

@section('dashboard-content')
<div class="space-y-6 animate-fadeIn">
    
    <div class="text-left">
        <h1 class="font-display font-extrabold text-2xl text-ink tracking-tight">Riwayat Setoran Anda</h1>
        <p class="text-xs text-ink-soft/80 font-medium mt-1">Pantau status penjemputan dan keuntungan dari setiap sampah yang Anda setor.</p>
    </div>

    <div class="bg-white border border-ink/5 rounded-[1.5rem] p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto rounded-xl border border-ink/5">
            <table class="table w-full text-sm">
                <thead>
                    <tr class="border-b border-ink/5 text-ink/70 font-bold uppercase tracking-wider text-xs bg-cream/60">
                        <th class="py-3.5 pl-5">Tanggal & Kode</th>
                        <th class="py-3.5">Jenis Sampah</th>
                        <th class="py-3.5">Berat Timbangan</th>
                        <th class="py-3.5 text-center">Status</th>
                        <th class="py-3.5 pr-5 text-right">Hadiah / Profit</th>
                    </tr>
                </thead>
                <tbody class="font-medium text-ink/90">
                    @forelse($deposits as $d)
                        <tr class="hover:bg-cream/10 border-b border-ink/5 transition-colors">
                            
                            <td class="py-4 pl-5">
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

                            <td class="py-4 text-center">
                                @if($d->status === 'pending')
                                    <span class="badge bg-ink/5 border-none text-ink-soft text-[11px] font-bold px-2.5 py-2 rounded-lg shadow-inner">Menunggu Jadwal</span>
                                @elseif($d->status === 'scheduled')
                                    <span class="badge bg-maritime/10 border-none text-maritime text-[11px] font-bold px-2.5 py-2 rounded-lg shadow-inner">Dijadwalkan</span>
                                @elseif($d->status === 'picked_up')
                                    <span class="badge bg-purple-100 border-none text-purple-700 text-[11px] font-bold px-2.5 py-2 rounded-lg shadow-inner">Dalam Perjalanan</span>
                                @elseif($d->status === 'verified')
                                    <span class="badge bg-forest/10 border-none text-forest text-[11px] font-bold px-2.5 py-2 rounded-lg shadow-inner">Diverifikasi</span>
                                @elseif($d->status === 'completed')
                                    <span class="badge bg-forest/20 border-none text-forest-dark text-[11px] font-bold px-2.5 py-2 rounded-lg shadow-inner">Selesai</span>
                                @elseif($d->status === 'rejected')
                                    <span class="badge bg-terracotta/10 border-none text-terracotta text-[11px] font-bold px-2.5 py-2 rounded-lg shadow-inner">Ditolak</span>
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
                            <td colspan="5" class="py-12 text-center text-ink-soft/60 font-medium text-xs">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="text-2xl opacity-50">🍃</span>
                                    <p>Belum ada riwayat setoran. Ayo mulai pilah sampahmu hari ini!</p>
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