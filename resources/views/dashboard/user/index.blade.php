@extends('layouts.dashboard', ['title' => 'Dashboard User — SulapaKarya Macca'])

@section('dashboard-content')
@php
    $currentUser = \App\Models\User::find(session('user_id'));
    $safePointsBalance = ($currentUser && $currentUser->points_balance > 0) ? $currentUser->points_balance : 0;

    $activeDeposits = \App\Models\WasteDeposit::where('user_id', session('user_id'))
        ->whereIn('status', ['pending', 'menunggu_penjemput', 'penjemput_menuju_lokasi', 'penjemput_tiba'])
        ->orderBy('created_at', 'desc')
        ->get();
@endphp

<div class="space-y-8 animate-fadeIn text-left">
    
    <!-- ============ HEADER SAMBUTAN & QR CODE ============ -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gradient-to-r from-white to-cream p-6 rounded-[1.5rem] border border-ink/5 shadow-md shadow-ink/[0.01]">
        <div class="flex items-center gap-4">
            
            <!-- Avatar Lingkaran Besar Menarik Foto Profil Riil Database -->
            <div class="avatar {{ $currentUser && $currentUser->foto_profil ? '' : 'placeholder' }}">
                <div class="bg-gradient-to-tr from-forest to-forest-dark text-white rounded-full w-16 h-16 shadow-lg shadow-forest/20 ring-4 ring-white overflow-hidden flex items-center justify-center">
                    @if($currentUser && $currentUser->foto_profil)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($currentUser->foto_profil) }}?v={{ time() }}" alt="Foto Profil {{ $currentUser->name }}" class="w-full h-full object-cover" />
                    @else
                        <span class="text-xl font-bold font-display">{{ strtoupper(substr(session('name', 'A'), 0, 1)) }}</span>
                    @endif
                </div>
            </div>
            
            <div>
                <h1 class="font-display font-extrabold text-2xl text-ink leading-tight">Halo, {{ session('name', 'Andi') }}</h1>
                <p class="text-xs text-ink-soft font-semibold mt-1">Mari pilah sampah bersama untuk lestarikan lingkungan Makassar!</p>
            </div>
        </div>

        <div class="flex items-center gap-3 bg-white border border-forest/10 p-3 rounded-2xl w-full sm:w-auto shadow-sm">
            <div class="bg-forest-light text-forest p-2 rounded-xl border border-forest/10">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><path d="M7 7h.01M17 7h.01M7 17h.01M17 17h.01"/></svg>
            </div>
            <div class="text-left">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-ink-soft/60 block">Setoran Aktif</span>
                <span class="text-xs font-bold text-forest">{{ $activeDeposits->count() }} <span class="text-ink-soft font-medium">setoran</span></span>
            </div>
        </div>
    </div>

    <!-- ============ SPANDUK PERINGATAN ALAMAT (BULLETPROOF CHECK) ============ -->
    @if(empty($currentUser) || empty($currentUser->address))
        <div class="alert bg-terracotta-light/70 border border-terracotta/20 rounded-2xl p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 shadow-sm">
            <div class="flex gap-3 items-start text-sm text-terracotta font-semibold">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0 mt-0.5"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 14h.01M12 8v5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <div>
                    <p class="text-ink font-extrabold">Lokasi Penjemputan Belum Diatur!</p>
                    <p class="text-xs text-ink-soft/90 font-medium mt-0.5">Lengkapi alamat rumah Anda di Makassar agar armada penjemput kami bisa memproses setoran sampah Anda.</p>
                </div>
            </div>
            <a href="/profile" class="btn btn-sm bg-terracotta border-none text-white hover:bg-terracotta-dark rounded-xl normal-case w-full sm:w-auto shadow-md shadow-terracotta/10 font-bold px-4">Lengkapi Profil</a>
        </div>
    @endif

    <!-- ============ RINGKASAN DATA & METRIK ============ -->
    <div>
        <div class="mb-4">
            <h2 class="font-display font-extrabold text-xl text-ink">Ringkasan Aktivitas</h2>
            <p class="text-xs text-ink-soft font-medium">Pantau akumulasi volume sampah serta simpanan saldo poin reward kriya Anda.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Card 1: Total Setor (Aksen Hijau Kebun) -->
            <div class="bg-gradient-to-br from-white via-white to-forest-light/60 border border-ink/5 border-l-4 border-l-forest rounded-2xl p-6 flex items-center gap-5 shadow-sm transition-all hover:shadow-md">
                <div class="w-14 h-14 rounded-xl bg-forest text-white flex items-center justify-center shrink-0 shadow-md shadow-forest/20">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                </div>
                <div>
                    <span class="text-xs text-ink-soft font-bold tracking-wide block">Total Setor Sampah</span>
                    <p class="text-3xl font-extrabold text-forest-dark mt-0.5">
                        {{ number_format($totalWeight ?? 0, 2, ',', '.') }} <span class="text-sm font-bold text-ink-soft">Kg</span>
                    </p>
                    <span class="text-[10px] bg-forest/10 text-forest font-extrabold px-2.5 py-0.5 rounded-full mt-2 inline-block">Riwayat Akumulasi</span>
                </div>
            </div>

            <!-- Card 2: Saldo Poin Tahan Minus -->
            <div class="bg-gradient-to-br from-white via-white to-maritime-light/60 border border-ink/5 border-l-4 border-l-maritime rounded-2xl p-6 flex items-center gap-5 shadow-sm transition-all hover:shadow-md">
                <div class="w-14 h-14 rounded-xl bg-maritime text-white flex items-center justify-center shrink-0 shadow-md shadow-maritime/20">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="8"/><path d="M12 7v10M9 12h6"/></svg>
                </div>
                <div>
                    <span class="text-xs text-ink-soft font-bold tracking-wide block">Saldo Poin Kriya</span>
                    <p class="text-3xl font-extrabold text-maritime-dark mt-0.5">
                        {{ number_format($safePointsBalance, 0, ',', '.') }} 
                        <span class="text-sm font-bold text-ink-soft">Poin</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ============ TOMBOL UTAMA SETOR SAMPAH (ACTION BANNER EMAS/HIJAU) ============ -->
    <div class="bg-gradient-to-r from-forest via-forest to-forest-dark p-6 rounded-[1.5rem] text-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-xl shadow-forest/10 relative overflow-hidden group">
        <div class="absolute inset-0 dot-grid text-white/[0.03] pointer-events-none"></div>
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-sand/10 blur-2xl rounded-full group-hover:scale-125 transition-all duration-700"></div>

        <div class="relative z-10">
            <h3 class="font-display font-bold text-xl text-cream">Ingin melakukan penyetoran hari ini?</h3>
            <p class="text-xs text-cream/75 mt-1 font-medium">Akses cepat untuk menjadwalkan penjemputan baru oleh kurir armada SulapaKarya Macca.</p>
        </div>
        <a href="/setor-sampah" class="btn bg-white hover:bg-cream text-forest border-none px-6 rounded-xl font-extrabold text-sm normal-case shadow-md shrink-0 w-full sm:w-auto relative z-10 active:scale-95 transition-all">
            Setor Sampah Baru
        </a>
    </div>

    <!-- ============ QR CODE SETORAN AKTIF ============ -->
    @if($activeDeposits->count() > 0)
    <div class="bg-white border border-ink/5 rounded-[1.5rem] p-6 shadow-sm">
        <div class="mb-5 text-left">
            <h2 class="font-display font-extrabold text-xl text-ink">QR Code Setoran Aktif</h2>
            <p class="text-xs text-ink-soft font-medium">Tunjukkan QR Code setoran kepada kurir saat penjemputan sampah. Setiap setoran memiliki QR unik.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($activeDeposits as $dep)
                <div class="border border-ink/5 rounded-2xl p-4 bg-cream/20 hover:bg-cream/40 transition-all">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="text-left">
                            <span class="font-mono text-xs font-extrabold text-forest block">{{ $dep->deposit_code }}</span>
                            <span class="text-[10px] text-ink-soft block mt-0.5">{{ ucfirst($dep->category) }} &middot; {{ number_format($dep->estimated_weight, 1) }} Kg</span>
                            <span class="text-[10px] text-ink-soft/60 block">{{ $dep->created_at->translatedFormat('d M Y') }}</span>
                        </div>
                        @if($dep->status === 'pending')
                            <span class="badge bg-amber-100 text-amber-700 border-none text-[9px] font-bold px-1.5 py-1 rounded-md shrink-0">Menunggu</span>
                        @elseif($dep->status === 'menunggu_penjemput')
                            <span class="badge bg-maritime/10 text-maritime border-none text-[9px] font-bold px-1.5 py-1 rounded-md shrink-0">Dijemput</span>
                        @elseif($dep->status === 'penjemput_menuju_lokasi')
                            <span class="badge bg-blue-100 text-blue-700 border-none text-[9px] font-bold px-1.5 py-1 rounded-md shrink-0">Kurir OTW</span>
                        @elseif($dep->status === 'penjemput_tiba')
                            <span class="badge bg-purple-100 text-purple-700 border-none text-[9px] font-bold px-1.5 py-1 rounded-md shrink-0">Kurir Tiba</span>
                        @endif
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="bg-white p-3 rounded-xl border border-ink/5 shadow-inner">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($dep->deposit_code) }}&color=241F18&bgcolor=FFFFFF"
                                 alt="QR {{ $dep->deposit_code }}"
                                 class="w-36 h-36 rounded-lg object-contain" loading="lazy" />
                        </div>
                        <span class="text-[10px] text-ink-soft font-medium mt-2 italic">Scan kode ini saat kurir tiba</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ============ TABEL RIWAYAT MUTASI POIN MASUK & KELUAR DENGAN FILTER MULTI-OPSI ============ -->
    <div class="bg-white border border-ink/5 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">
            <div>
                <h2 class="font-display font-extrabold text-xl text-ink">Buku Kas & Aktivitas Poin Kriya</h2>
                <p class="text-xs text-ink-soft font-medium">Lacak seluruh riwayat mutasi kredit (masuk) dan debit (keluar) poin kriya Anda secara real-time.</p>
            </div>
            
            <!-- PANEL SELEKSI FILTER INTERAKTIF (PERTANGGAL & STATUS MUTASI) -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full xl:w-auto text-xs font-bold">
                <!-- Filter Jangkauan Tanggal -->
                <div class="flex items-center gap-2 bg-cream/40 border border-ink/10 px-3 py-1.5 rounded-xl">
                    <span class="text-ink-soft text-[11px] uppercase tracking-wider">Tanggal:</span>
                    <input type="date" id="filter_start_date" onchange="filterMutationTable()" class="bg-transparent focus:outline-none font-mono text-ink">
                    <span class="text-ink-soft">-</span>
                    <input type="date" id="filter_end_date" onchange="filterMutationTable()" class="bg-transparent focus:outline-none font-mono text-ink">
                </div>

                <!-- Tab Segment Opsi Mutasi -->
                <div class="join border border-ink/10 rounded-xl overflow-hidden bg-white shrink-0">
                    <button id="tab_all" onclick="changeMutationTab('all')" class="join-item btn btn-xs bg-forest text-white border-none normal-case font-bold px-3">Semua</button>
                    <button id="tab_in" onclick="changeMutationTab('masuk')" class="join-item btn btn-xs bg-white text-ink-soft hover:bg-cream border-none normal-case font-bold px-3">Masuk (+)</button>
                    <button id="tab_out" onclick="changeMutationTab('keluar')" class="join-item btn btn-xs bg-white text-ink-soft hover:bg-cream border-none normal-case font-bold px-3">Keluar (-)</button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-ink/5">
            <table class="table w-full text-sm" id="mutation_table">
                <thead>
                    <tr class="bg-sand/30 text-ink border-b border-ink/5 text-xs uppercase tracking-wider">
                        <th class="font-extrabold py-3.5 pl-4">Tanggal & Waktu</th>
                        <th class="font-extrabold py-3.5">Pihak Terkait</th>
                        <th class="font-extrabold py-3.5">Keterangan Aktivitas</th>
                        <th class="font-extrabold py-3.5 text-right pr-4">Jumlah Mutasi</th>
                    </tr>
                </thead>
                <tbody class="font-medium text-ink/90">
                    @php $hasRows = false; @endphp
                    
                    <!-- 1. MERGE LOGIKA DATA MASUK: Dari Pengiriman Poin oleh Kurir -->
                    @foreach($pointHistory as $history)
                        @php 
                            $hasRows = true; 
                            $kurir = \App\Models\User::find($history->sender_id);
                            $timestamp = \Carbon\Carbon::parse($history->created_at);
                        @endphp
                        <tr class="mutation-row border-b border-ink/5 hover:bg-sand/10 transition-colors" 
                            data-type="masuk" 
                            data-date="{{ $timestamp->format('Y-m-d') }}">
                            <td class="py-3.5 pl-4">
                                <span class="text-xs font-bold text-ink block">{{ $timestamp->translatedFormat('d M Y') }}</span>
                                <span class="text-[10px] text-ink-soft/60 block font-mono mt-0.5">{{ $timestamp->format('H:i') }} WITA</span>
                            </td>
                            <td class="py-3.5 text-xs">
                                <span class="font-bold text-forest flex items-center gap-1">🟢 {{ $kurir->name ?? 'Armada Kurir' }}</span>
                                <span class="text-[10px] text-ink-soft/50 block mt-0.5 font-mono">{{ $history->reference_number }}</span>
                            </td>
                            <td class="py-3.5 text-xs text-ink-soft max-w-xs truncate" title="{{ $history->note }}">
                                {{ $history->note ?? 'Insentif pilah sampah daur ulang' }}
                            </td>
                            <td class="py-3.5 text-right pr-4">
                                <span class="text-forest font-extrabold font-mono text-xs bg-forest/10 px-2.5 py-1 rounded-lg inline-block">
                                    + {{ number_format($history->amount, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach

                    <!-- 2. MERGE LOGIKA DATA KELUAR: Dari Pembelian Kriya Menggunakan Poin (Gunakan variabel $buyHistory dari controller) -->
                    @if(isset($buyHistory))
                        @foreach($buyHistory as $spent)
                            @php 
                                $hasRows = true; 
                                $timestamp = \Carbon\Carbon::parse($spent->created_at);
                                $produk = \App\Models\Product::find($spent->product_id);
                            @endphp
                            <tr class="mutation-row border-b border-ink/5 hover:bg-sand/10 transition-colors" 
                                data-type="keluar" 
                                data-date="{{ $timestamp->format('Y-m-d') }}">
                                <td class="py-3.5 pl-4">
                                    <span class="text-xs font-bold text-ink block">{{ $timestamp->translatedFormat('d M Y') }}</span>
                                    <span class="text-[10px] text-ink-soft/60 block font-mono mt-0.5">{{ $timestamp->format('H:i') }} WITA</span>
                                </td>
                                <td class="py-3.5 text-xs">
                                    <span class="font-bold text-terracotta flex items-center gap-1">🔴 Toko UMKM Kriya</span>
                                    <span class="text-[10px] text-ink-soft/50 block mt-0.5 font-mono">{{ $spent->order_id }}</span>
                                </td>
                                <td class="py-3.5 text-xs text-ink-soft max-w-xs truncate">
                                    Tukar Poin untuk: {{ $produk->name ?? 'Produk Kerajinan' }}
                                </td>
                                <td class="py-3.5 text-right pr-4">
                                    <span class="text-terracotta font-extrabold font-mono text-xs bg-terracotta/10 px-2.5 py-1 rounded-lg inline-block">
                                        - {{ number_format($spent->points_used, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    <!-- Placeholder baris jika kedua riwayat kosong -->
                    <tr id="empty_mutation_placeholder" class="{{ $hasRows ? 'hidden' : '' }}">
                        <td colspan="4" class="py-10 text-center text-ink-soft/50 text-xs font-medium">
                            📭 Belum ada rekaman mutasi poin masuk ataupun keluar pada akun Anda.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============ KATALOG KERAJINAN PREVIEW ============ -->
    <div>
        <div class="flex justify-between items-end mb-4">
            <div>
                <h2 class="font-display font-extrabold text-xl text-ink">Katalog Kerajinan Pilihan</h2>
                <p class="text-xs text-ink-soft font-medium">Tukarkan poin akumulasi Anda dengan produk UMKM kriya binaan kota kita.</p>
            </div>
            <a href="/katalog" class="btn btn-sm btn-outline border-forest/20 text-forest hover:bg-forest hover:text-white rounded-xl text-xs normal-case font-bold px-4 transition-all shadow-sm">Lihat Katalog</a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @for ($i = 1; $i <= 4; $i++)
                <div class="bg-white border border-ink/5 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all group">
                    <div class="aspect-video bg-sand/20 border-b border-ink/5 flex items-center justify-center text-ink-soft/30 relative">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                    <div class="p-4 text-left">
                        <div class="h-3.5 bg-ink/10 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-ink/5 rounded w-1/2"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>


</div>

<!-- ================= ENGINE FILTER INTERAKTIF REAL-TIME (JAVASCRIPT) ================= -->
<script>
    let currentActiveTab = 'all';

    function changeMutationTab(tabType) {
        // Atur styling tombol tab active
        const tabs = {
            'all': document.getElementById('tab_all'),
            'masuk': document.getElementById('tab_in'),
            'keluar': document.getElementById('tab_out')
        };

        Object.keys(tabs).forEach(key => {
            if (tabs[key]) {
                if (key === tabType) {
                    tabs[key].className = "join-item btn btn-xs bg-forest text-white border-none normal-case font-bold px-3";
                } else {
                    tabs[key].className = "join-item btn btn-xs bg-white text-ink-soft hover:bg-cream border-none normal-case font-bold px-3";
                }
            }
        });

        currentActiveTab = tabType;
        filterMutationTable();
    }

    function filterMutationTable() {
        const rows = document.querySelectorAll('.mutation-row');
        const startDateVal = document.getElementById('filter_start_date').value;
        const endDateVal = document.getElementById('filter_end_date').value;
        const placeholder = document.getElementById('empty_mutation_placeholder');
        
        let visibleRowsCount = 0;

        rows.forEach(row => {
            const rowType = row.getAttribute('data-type');
            const rowDate = row.getAttribute('data-date');
            
            let matchTab = (currentActiveTab === 'all' || rowType === currentActiveTab);
            let matchStartDate = (!startDateVal || rowDate >= startDateVal);
            let matchEndDate = (!endDateVal || rowDate <= endDateVal);

            if (matchTab && matchStartDate && matchEndDate) {
                row.classList.remove('hidden');
                visibleRowsCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        // Manajemen tampilan row placeholder kosong jika data setelah difilter nihil
        if (visibleRowsCount === 0) {
            placeholder.classList.remove('hidden');
        } else {
            placeholder.classList.add('hidden');
        }
    }
</script>
@endsection