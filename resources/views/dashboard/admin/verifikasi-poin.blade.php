@extends('layouts.dashboard', ['title' => 'Verifikasi Poin — SulapaKarya'])

@section('dashboard-content')
<div class="space-y-6 animate-fadeIn">

    <div class="text-left">
        <h1 class="font-display font-extrabold text-2xl text-ink tracking-tight">Verifikasi Poin Pending</h1>
        <p class="text-xs text-ink-soft/80 font-medium mt-1">Setujui atau tolak poin hasil penjemputan sampah setelah diperiksa di warehouse. Poin pending otomatis disetujui setelah 24 jam.</p>
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
                        <th class="py-3.5 pl-5">Referensi & Waktu</th>
                        <th class="py-3.5">Warga Penerima</th>
                        <th class="py-3.5">Kurir Pengirim</th>
                        <th class="py-3.5 text-center">Jumlah Poin</th>
                        <th class="py-3.5">Keterangan</th>
                        <th class="py-3.5 text-center">Sisa Waktu</th>
                        <th class="py-3.5 text-center pr-5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="font-medium text-ink/90">
                    @forelse($pendingTransfers as $t)
                        @php
                            $autoApproveAt = $t->created_at->addHours(24);
                            $totalMenit = (int) now()->diffInMinutes($autoApproveAt, false);
                            $sisaJam = intdiv(max($totalMenit, 0), 60);
                            $sisaMenit = max($totalMenit, 0) % 60;
                        @endphp
                        <tr class="hover:bg-cream/10 border-b border-ink/5 transition-colors">
                            <td class="py-4 pl-5">
                                <span class="font-mono font-bold text-ink-soft text-xs block">{{ $t->reference_number }}</span>
                                <span class="text-[10px] text-ink-soft/60 block mt-0.5">{{ $t->created_at->translatedFormat('d M Y - H:i') }} WITA</span>
                            </td>
                            <td class="py-4">
                                <span class="font-bold text-ink text-xs">{{ $t->receiver->name ?? 'Anonim' }}</span>
                            </td>
                            <td class="py-4">
                                <span class="text-xs text-maritime font-bold">{{ $t->sender->name ?? '-' }}</span>
                            </td>
                            <td class="py-4 text-center">
                                <span class="text-forest font-extrabold font-mono text-lg">{{ number_format($t->amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="py-4 text-xs text-ink-soft max-w-[200px] truncate" title="{{ $t->note }}">
                                {{ $t->note }}
                            </td>
                            <td class="py-4 text-center">
                                @if($totalMenit > 0)
                                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">
                                        {{ $sisaJam }}j {{ $sisaMenit }}m
                                    </span>
                                @else
                                    <span class="text-xs font-bold text-forest bg-forest/10 px-2 py-1 rounded-lg">Auto-approve</span>
                                @endif
                            </td>
                            <td class="py-4 text-center pr-5">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('admin.points.approve', $t->id) }}" method="POST" class="m-0 p-0">
                                        @csrf
                                        <input type="hidden" name="keputusan" value="setujui">
                                        <button type="submit" class="btn btn-xs bg-forest border-none text-white font-bold rounded-md px-3 shadow-sm">Setujui</button>
                                    </form>
                                    <form action="{{ route('admin.points.approve', $t->id) }}" method="POST" class="m-0 p-0">
                                        @csrf
                                        <input type="hidden" name="keputusan" value="tolak">
                                        <button type="submit" class="btn btn-xs bg-terracotta border-none text-white font-bold rounded-md px-3 shadow-sm">Tolak</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-ink-soft/60 font-medium text-xs">
                                Tidak ada poin pending yang menunggu verifikasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
