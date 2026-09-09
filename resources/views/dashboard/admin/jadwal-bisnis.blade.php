@extends('layouts.dashboard', ['title' => 'Jadwal Penjemputan Mitra PRO — SulapaKarya'])

@section('dashboard-content')
<div class="space-y-6 text-left">
    
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-ink/5">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-forest animate-pulse"></span>
                <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-forest bg-forest/10 px-2 py-0.5 rounded">
                    Logistik B2B
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight mt-1">Jadwal Penjemputan Mitra PRO</h1>
            <p class="text-xs text-ink-soft mt-0.5">Matriks operasional armada berdasarkan hari operasional dan rute kecamatan di Makassar.</p>
        </div>
        <div class="bg-white border border-ink/10 px-4 py-2 rounded-2xl text-right shrink-0">
            <span class="text-[10px] text-ink-soft font-bold uppercase tracking-wider block">Target Hari Ini</span>
            <span class="text-base font-black font-mono text-forest">{{ $schedulesForDay->count() }} <span class="text-xs font-sans">Titik Usaha</span></span>
        </div>
    </div>

    <!-- Navigasi Hari (Weekly Bar) -->
    <div class="flex items-center gap-2 border-b border-ink/10 pb-3 overflow-x-auto">
        @foreach($daysOfWeek as $day)
            @php $count = $dayCounts[$day] ?? 0; @endphp
            <a href="?hari={{ $day }}" 
               class="px-4 py-2.5 rounded-2xl text-xs font-bold transition-all shrink-0 flex items-center gap-2 {{ $selectedDay === $day ? 'bg-forest text-white shadow-sm' : 'bg-white border border-ink/5 text-ink hover:bg-cream/60' }}">
                <span>{{ $day }}</span>
                <span class="badge badge-xs {{ $selectedDay === $day ? 'bg-white/20 text-white' : 'bg-ink/5 text-ink-soft' }} border-none font-mono px-1.5">
                    {{ $count }}
                </span>
            </a>
        @endforeach
    </div>

    <!-- Tampilan Pengelompokan Berdasarkan Rute Kecamatan -->
    @if($schedulesForDay->count() > 0)
        <div class="space-y-6">
            @foreach($groupedByKecamatan as $kecamatan => $items)
                <div class="bg-white border border-ink/10 rounded-3xl p-5 sm:p-6 shadow-none space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-ink/5">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-xl bg-forest/10 text-forest flex items-center justify-center font-bold text-xs">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </div>
                            <h2 class="text-sm font-bold text-ink">Kecamatan {{ $kecamatan }}</h2>
                        </div>
                        <span class="text-[11px] font-mono text-ink-soft font-semibold">{{ $items->count() }} Titik Jemput</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($items as $s)
                            <div class="p-4 rounded-2xl bg-cream/20 border border-ink/5 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-xs text-ink truncate">{{ $s->user->business_name }}</h3>
                                            <span class="text-[10px] text-ink-soft font-mono block">{{ $s->user->business_type }}</span>
                                        </div>
                                        <span class="badge badge-sm bg-forest/10 text-forest border-none font-mono font-bold text-[10px]">
                                            {{ $s->pickup_time }} WITA
                                        </span>
                                    </div>

                                    <div class="mt-3 text-[11px] text-ink-soft space-y-1">
                                        <div><strong class="text-ink">Kelurahan:</strong> {{ $s->user->kelurahan ?? '-' }}</div>
                                        <div class="line-clamp-2" title="{{ $s->user->address }}"><strong class="text-ink">Alamat:</strong> {{ $s->user->address }}</div>
                                        <div><strong class="text-ink">Fokus Sampah:</strong> <span class="text-forest font-semibold">{{ $s->category_focus ?? 'Semua' }}</span></div>
                                        @if($s->notes)
                                            <div class="text-[10px] text-forest/90 bg-white p-2 rounded-xl border border-ink/5 mt-1.5">
                                                <strong>Catatan Kurir:</strong> {{ $s->notes }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-ink/5 flex items-center justify-between text-[10px] text-ink-soft font-mono">
                                    <span>PIC: {{ $s->user->name }}</span>
                                    <span>{{ $s->user->phone ?? '-' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white border border-ink/10 rounded-3xl p-12 text-center text-xs text-ink-soft/60">
            Tidak ada agenda penjemputan rutin untuk hari <strong>{{ $selectedDay }}</strong>.
        </div>
    @endif

</div>
@endsection