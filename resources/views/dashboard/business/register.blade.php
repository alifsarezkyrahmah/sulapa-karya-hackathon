@extends('layouts.dashboard', ['title' => 'SulapaKarya PRO — Mitra Bisnis'])

@section('dashboard-content')
<div class="max-w-5xl mx-auto space-y-5 sm:space-y-6 text-left px-1 sm:px-0">

    <!-- Notifikasi -->
    @if(session('success'))
        <div class="p-3.5 sm:p-4 rounded-2xl bg-forest/10 border border-forest/20 text-forest text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-3.5 sm:p-4 rounded-2xl bg-terracotta/10 border border-terracotta/20 text-terracotta text-xs font-semibold">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- KONDISI 1: STATUS PENDING (MENUNGGU VERIFIKASI ADMIN)                     --}}
    {{-- ========================================================================= --}}
    @if($user->business_status === 'pending')
        <div class="bg-white rounded-3xl border border-ink/10 p-6 sm:p-12 text-center max-w-xl mx-auto space-y-4 shadow-none">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center mx-auto text-xs font-bold font-mono">
                WAIT
            </div>
            <div>
                <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-amber-800 bg-amber-100 px-2.5 py-1 rounded">
                    Tahap 2: Verifikasi Admin
                </span>
                <h2 class="text-lg sm:text-xl font-bold text-ink tracking-tight mt-2">Pendaftaran Bisnis Sedang Ditinjau</h2>
                <p class="text-xs text-ink-soft leading-relaxed mt-1">
                    Anda sudah mendaftarkan unit usaha <strong>{{ $user->business_name }}</strong>. Tim admin sedang memverifikasi titik lokasi dan akses operasional armada.
                </p>
            </div>

            <div class="bg-cream/30 p-4 rounded-2xl border border-ink/5 text-left text-xs space-y-2 font-mono">
                <div class="flex justify-between gap-2"><span class="text-ink-soft font-sans">Kategori:</span> <span class="font-bold text-ink truncate">{{ $user->business_type }}</span></div>
                <div class="flex justify-between gap-2"><span class="text-ink-soft font-sans">Estimasi Sampah:</span> <span class="font-bold text-forest">{{ $user->waste_estimate_kg }} Kg / pekan</span></div>
                <div class="flex justify-between gap-2"><span class="text-ink-soft font-sans">Wilayah:</span> <span class="text-ink truncate max-w-[200px] sm:max-w-[240px]">Kec. {{ $user->kecamatan }}, Kel. {{ $user->kelurahan }}</span></div>
            </div>
        </div>

    {{-- ========================================================================= --}}
    {{-- KONDISI 2: LOLOS VERIFIKASI -> PEMBAYARAN MIDTRANS SNAP                   --}}
    {{-- ========================================================================= --}}
    @elseif($user->business_status === 'verified_unpaid')
        <div class="bg-white rounded-3xl border border-forest/20 p-6 sm:p-10 max-w-xl mx-auto space-y-6 text-center shadow-none">
            <div class="w-12 h-12 rounded-2xl bg-forest/10 text-forest flex items-center justify-center mx-auto text-xs font-bold font-mono">
                PASS
            </div>
            <div>
                <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-forest bg-forest/10 px-2.5 py-1 rounded">
                    Tahap 3: Pembayaran Layanan
                </span>
                <h2 class="text-lg sm:text-xl font-bold text-ink tracking-tight mt-2">Verifikasi Lolos &bull; Aktivasi PRO</h2>
                <p class="text-xs text-ink-soft mt-1">Selesaikan pembayaran biaya langganan bulanan melalui Midtrans Snap untuk membuka akses penjemputan rutin.</p>
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-cream/40 border border-ink/5 text-left space-y-3">
                <div class="flex justify-between text-xs pb-2 border-b border-ink/5">
                    <span class="text-ink-soft">Nama Unit Usaha</span>
                    <span class="font-bold text-ink">{{ $user->business_name }}</span>
                </div>
                <div class="flex justify-between text-xs pb-2 border-b border-ink/5">
                    <span class="text-ink-soft">Fasilitas</span>
                    <span class="font-semibold text-ink">Penjemputan Rutin + Laporan ESG</span>
                </div>
                <div class="flex justify-between text-xs pt-1">
                    <span class="font-bold text-ink">Total Tagihan</span>
                    <span class="font-mono font-bold text-forest text-sm">Rp 67.000 / bulan</span>
                </div>
            </div>

            <button type="button" id="pay-button" class="btn btn-sm w-full bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-bold h-11 shadow-sm">
                Bayar via Midtrans Snap &rarr;
            </button>
        </div>

    {{-- ========================================================================= --}}
    {{-- KONDISI 3: STATUS APPROVED (PRO AKTIF)                                    --}}
    {{-- ========================================================================= --}}
    @elseif($user->business_status === 'approved')
        <!-- Header Info Bisnis & Statistik Responsif -->
        <div class="bg-white border border-ink/10 p-5 sm:p-6 rounded-3xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="w-full md:w-auto">
                <div class="flex items-center gap-2 mb-1">
                    <span class="badge badge-xs bg-forest text-white border-none font-bold font-mono text-[9px] px-2 py-0.5 uppercase">PRO PARTNER AKTIF</span>
                    <span class="text-xs text-ink-soft font-mono">{{ $user->business_type }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">{{ $user->business_name }}</h1>
                <p class="text-xs text-ink-soft mt-0.5 leading-relaxed">{{ $user->address }} (Kec. {{ $user->kecamatan }}, Kel. {{ $user->kelurahan }})</p>
            </div>

            <!-- Kartu Statistik -->
            <div class="grid grid-cols-2 gap-2.5 w-full md:w-auto shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-ink/5">
                <div class="bg-cream/40 border border-ink/5 p-3 rounded-2xl text-left md:text-right">
                    <span class="text-[10px] text-ink-soft font-bold uppercase tracking-wider block">Limbah Bisnis</span>
                    <span class="text-base sm:text-lg font-black font-mono text-forest">{{ number_format($totalWeight, 1) }} <span class="text-xs font-sans">Kg</span></span>
                </div>
                <div class="bg-amber-500/10 border border-amber-400/30 p-3 rounded-2xl text-left md:text-right">
                    <span class="text-[10px] text-amber-900 font-bold uppercase tracking-wider block">Poin Dari Bisnis</span>
                    <span class="text-base sm:text-lg font-black font-mono text-amber-700">+{{ number_format($businessPointsEarned ?? 0) }}</span>
                </div>
            </div>
        </div>

        <!-- Kartu Metrik Dampak ESG Unit Usaha -->
<div class="bg-white border border-ink/10 rounded-3xl p-6 sm:p-7 space-y-5 shadow-none">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-ink/5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-forest/10 text-forest flex items-center justify-center font-bold text-base shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-ink">Ringkasan Dampak Lingkungan (ESG)</h3>
                <p class="text-xs text-ink-soft mt-0.5">Akumulasi kontribusi nyata unit usaha dalam pengelolaan limbah sisa operasional.</p>
            </div>
        </div>

        <a href="{{ route('bisnis.report.export') }}" target="_blank" class="btn btn-sm bg-forest hover:bg-forest-dark text-white border-none rounded-xl font-bold px-4 h-10 shadow-none inline-flex items-center gap-2 justify-center shrink-0">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span>Unduh Laporan Sertifikat ↗</span>
        </a>
    </div>

    <!-- Grid 3 Metrik Hijau Profesional -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-left">
        <div class="p-4 bg-cream/30 border border-ink/5 rounded-2xl space-y-1">
            <span class="text-[10px] uppercase font-bold text-ink-soft block tracking-wider font-mono">Reduksi Emisi Karbon</span>
            <div class="flex items-baseline gap-1.5">
                <span class="text-xl font-black font-mono text-forest">{{ number_format($co2Saved ?? 0, 1) }}</span>
                <span class="text-xs font-semibold text-ink-soft">kg CO₂e</span>
            </div>
            <p class="text-[11px] text-ink-soft leading-tight">Menekan timbulan gas rumah kaca.</p>
        </div>

        <div class="p-4 bg-cream/30 border border-ink/5 rounded-2xl space-y-1">
            <span class="text-[10px] uppercase font-bold text-ink-soft block tracking-wider font-mono">Ruang TPA Dihemat</span>
            <div class="flex items-baseline gap-1.5">
                <span class="text-xl font-black font-mono text-forest">{{ number_format($landfillSavedM3 ?? 0, 2) }}</span>
                <span class="text-xs font-semibold text-ink-soft">m³</span>
            </div>
            <p class="text-[11px] text-ink-soft leading-tight">Divergen dari TPA Tamangapa.</p>
        </div>

        <div class="p-4 bg-cream/30 border border-ink/5 rounded-2xl space-y-1">
            <span class="text-[10px] uppercase font-bold text-ink-soft block tracking-wider font-mono">Kunjungan Armada</span>
            <div class="flex items-baseline gap-1.5">
                <span class="text-xl font-black font-mono text-forest">{{ $totalPickups ?? 0 }}</span>
                <span class="text-xs font-semibold text-ink-soft">Tuntas</span>
            </div>
            <p class="text-[11px] text-ink-soft leading-tight">Logistik terjadwal rutin.</p>
        </div>
    </div>
</div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6 items-start">
            <!-- Kolom Jadwal Rutin -->
            <div class="lg:col-span-2 space-y-4">
                
                @if($schedule)
                    <!-- TAMPILAN 1: STATUS JADWAL AKTIF / RINGKASAN -->
                    <div id="schedule_view_card" class="bg-white border border-ink/10 p-5 sm:p-6 rounded-3xl space-y-4 shadow-none">
                        
                        <!-- Header Box Responsif -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-ink/5">
                            <div class="flex items-start sm:items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $schedule->is_active ? 'bg-forest animate-pulse' : 'bg-terracotta' }} shrink-0 mt-1 sm:mt-0"></span>
                                <div>
                                    <h2 class="text-sm font-bold text-ink">Jadwal Operasional Rutin</h2>
                                    <span class="text-[11px] font-mono {{ $schedule->is_active ? 'text-forest font-semibold' : 'text-terracotta font-semibold' }} block sm:inline">
                                        {{ $schedule->is_active ? 'Status: Aktif Beroperasi' : 'Status: Sedang Dijeda Sementara' }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Tombol Aksi -->
                            <div class="flex items-center gap-2 w-full sm:w-auto pt-1 sm:pt-0">
                                <button type="button" onclick="toggleScheduleEditMode(true)" 
                                    class="btn btn-xs flex-1 sm:flex-none bg-white hover:bg-cream border border-ink/15 text-ink rounded-xl font-bold px-3.5 py-1.5 h-8 shadow-2xs">
                                    <svg class="w-3 h-3 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Ubah Jadwal
                                </button>
                                
                                <form action="{{ route('bisnis.schedule.toggle') }}" method="POST" class="m-0 p-0 flex-1 sm:flex-none">
                                    @csrf
                                    <button type="submit" 
                                        class="btn btn-xs w-full sm:w-auto rounded-xl font-bold px-3.5 py-1.5 h-8 border shadow-2xs {{ $schedule->is_active ? 'bg-terracotta/10 text-terracotta border-terracotta/30 hover:bg-terracotta hover:text-white' : 'bg-forest/15 text-forest border-forest/30 hover:bg-forest hover:text-white' }}">
                                        {{ $schedule->is_active ? 'Jeda Penjemputan' : 'Aktifkan Kembali' }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Grid Ringkasan Jadwal -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="p-3.5 bg-cream/30 border border-ink/5 rounded-2xl">
                                <span class="text-[10px] text-ink-soft uppercase font-bold tracking-wider block">Hari Penjemputan</span>
                                <span class="font-bold text-ink text-sm mt-0.5 block">
                                    {{ implode(', ', $schedule->pickup_days ?? []) }}
                                </span>
                            </div>
                            <div class="p-3.5 bg-cream/30 border border-ink/5 rounded-2xl">
                                <span class="text-[10px] text-ink-soft uppercase font-bold tracking-wider block">Slot Jam Kerja</span>
                                <span class="font-mono font-bold text-forest text-sm mt-0.5 block">
                                    {{ $schedule->pickup_time }} WITA
                                </span>
                            </div>
                            <div class="p-3.5 bg-cream/30 border border-ink/5 rounded-2xl sm:col-span-2">
                                <span class="text-[10px] text-ink-soft uppercase font-bold tracking-wider block">Fokus Komoditas & Akses</span>
                                <span class="font-semibold text-ink block mt-0.5">{{ $schedule->category_focus ?? 'Semua Jenis Sampah Daur Ulang' }}</span>
                                @if($schedule->notes)
                                    <p class="text-[11px] text-ink-soft mt-1 bg-white p-2.5 rounded-xl border border-ink/5 leading-relaxed">
                                        <strong>Catatan Kurir:</strong> {{ $schedule->notes }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- TAMPILAN 2: FORM SIMPAN / EDIT JADWAL -->
                <div id="schedule_form_card" class="{{ $schedule ? 'hidden' : '' }} bg-white border border-ink/10 p-5 sm:p-6 rounded-3xl space-y-4 shadow-none">
                    <div class="flex items-center justify-between pb-3 border-b border-ink/5">
                        <div>
                            <h2 class="text-sm font-bold text-ink">{{ $schedule ? 'Ubah Pengaturan Jadwal' : 'Atur Jadwal Penjemputan Rutin' }}</h2>
                            <p class="text-[11px] text-ink-soft">Pilih hari kerja dan slot operasional pengambilan limbah oleh armada kurir.</p>
                        </div>
                        @if($schedule)
                            <button type="button" onclick="toggleScheduleEditMode(false)" class="btn btn-xs bg-cream text-ink border border-ink/10 rounded-xl font-semibold">
                                Batal
                            </button>
                        @endif
                    </div>

                    <form action="{{ route('bisnis.schedule.save') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-ink mb-2">Pilih Hari Operasional Jemput <span class="text-terracotta">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                                @php 
                                    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                    $selectedDays = $schedule->pickup_days ?? ['Senin', 'Kamis'];
                                @endphp
                                @foreach($days as $day)
                                    <label class="flex items-center gap-2 p-2 bg-cream/20 border border-ink/5 rounded-xl cursor-pointer hover:border-forest transition-colors">
                                        <input type="checkbox" name="pickup_days[]" value="{{ $day }}" {{ in_array($day, $selectedDays) ? 'checked' : '' }} class="checkbox checkbox-xs checkbox-success rounded">
                                        <span class="font-semibold text-ink">{{ $day }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1">Slot Jam Penjemputan (08:00 - 17:00 WITA) <span class="text-terracotta">*</span></label>
                                <select name="pickup_time" required class="select select-sm select-bordered w-full rounded-xl text-xs bg-cream/20 font-mono font-bold h-11 focus:outline-none focus:border-forest">
                                    <option value="" disabled {{ !isset($schedule->pickup_time) ? 'selected' : '' }}>-- Pilih Slot Jam --</option>
                                    @for($h = 8; $h <= 16; $h++)
                                        @php
                                            $startHour = sprintf('%02d:00', $h);
                                            $endHour   = sprintf('%02d:00', $h + 1);
                                            $slotLabel = "{$startHour} - {$endHour} WITA";
                                        @endphp
                                        <option value="{{ $startHour }}" {{ (isset($schedule->pickup_time) && str_starts_with($schedule->pickup_time, $startHour)) ? 'selected' : '' }}>
                                            {{ $slotLabel }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-ink mb-1">Fokus Jenis Sampah Utama <span class="text-terracotta">*</span></label>
                                <input type="text" name="category_focus" value="{{ old('category_focus', $schedule->category_focus ?? 'Kardus, Kaca & Plastik') }}" required class="input input-sm input-bordered w-full rounded-xl text-xs bg-cream/20 h-11 focus:outline-none focus:border-forest font-semibold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink mb-1">Catatan Akses Lokasi untuk Kurir (Opsional)</label>
                            <textarea name="notes" rows="2" placeholder="Contoh: Titik parkir motor roda tiga di samping ruko, jemput sebelum jam 11 siang..." class="textarea textarea-bordered w-full rounded-xl text-xs bg-cream/20 leading-relaxed">{{ old('notes', $schedule->notes ?? '') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-sm w-full sm:w-auto bg-forest hover:bg-forest-dark text-white rounded-xl text-xs font-bold px-6 h-11 border-none shadow-sm">
                            {{ $schedule ? 'Perbarui Jadwal Operasional →' : 'Simpan Jadwal Rutin →' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Rekapitulasi Komposisi Sampah -->
            <div class="bg-white border border-ink/10 p-5 sm:p-6 rounded-3xl space-y-4 shadow-none">
                <h2 class="text-sm font-bold text-ink pb-2 border-b border-ink/5">Rekapitulasi Limbah Bisnis</h2>
                <div class="space-y-2.5">
                    @forelse($wasteStats as $stat)
                        <div class="p-3 bg-cream/30 border border-ink/5 rounded-2xl flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-ink block capitalize">{{ $stat->category }}</span>
                                <span class="text-[10px] text-ink-soft">{{ $stat->total_pickup }}x penjemputan</span>
                            </div>
                            <span class="font-mono font-bold text-forest text-xs">{{ number_format($stat->total_weight, 1) }} Kg</span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-ink-soft/60">Belum ada data setoran bisnis selesai.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Riwayat Penjemputan + QR Validasi Transaksi -->
        <div class="bg-white border border-ink/10 p-5 sm:p-6 rounded-3xl space-y-4 shadow-none">
            <div class="flex items-center justify-between pb-2 border-b border-ink/5">
                <div>
                    <h2 class="text-sm font-bold text-ink">Riwayat Kunjungan Kurir & Validasi QR</h2>
                    <p class="text-[11px] text-ink-soft">Tunjukkan QR kode transaksi kepada kurir saat penjemputan tiba di lokasi.</p>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="table table-sm w-full text-xs">
                    <thead>
                        <tr class="border-b border-ink/10 text-ink-soft text-[10px] uppercase font-bold">
                            <th class="py-2.5">Kode Setoran</th>
                            <th class="py-2.5">Kategori</th>
                            <th class="py-2.5">Berat Timbangan</th>
                            <th class="py-2.5">Poin Diperoleh</th>
                            <th class="py-2.5">Status</th>
                            <th class="py-2.5 text-right">Validasi Kurir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/5 font-mono">
                        @forelse($pickupHistory as $p)
                            <tr>
                                <td class="font-bold text-ink py-3">{{ $p->deposit_code }}</td>
                                <td class="capitalize font-sans py-3">{{ $p->category }}</td>
                                <td class="font-bold text-ink py-3">{{ number_format($p->actual_weight ?? $p->estimated_weight, 1) }} Kg</td>
                                <td class="text-forest font-bold py-3">+{{ number_format($p->points_earned ?? 0) }} Poin</td>
                                <td class="py-3">
                                    <span class="badge badge-xs {{ in_array($p->status, ['berhasil_dikirim', 'completed', 'selesai']) ? 'bg-forest/15 text-forest' : 'bg-amber-400/20 text-amber-900' }} border-none uppercase">
                                        {{ str_replace('_', ' ', $p->status) }}
                                    </span>
                                </td>
                                <td class="text-right py-3">
                                    <button type="button" onclick="document.getElementById('qr_modal_{{ $p->id }}').showModal()" 
                                        class="btn btn-xs bg-cream hover:bg-forest/15 text-ink hover:text-forest border border-ink/10 rounded-lg text-[10px] font-bold px-2.5 shadow-none">
                                        <svg class="w-3 h-3 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                        Buka QR
                                    </button>

                                    <!-- Modal QR Code Transaksi Bisnis -->
                                    <dialog id="qr_modal_{{ $p->id }}" class="modal modal-middle">
                                        <div class="modal-box w-11/12 max-w-xs bg-white rounded-3xl border border-ink/10 p-6 text-center shadow-2xl space-y-4">
                                            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-3 top-3 text-ink-soft">✕</button></form>
                                            
                                            <div>
                                                <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-forest bg-forest/10 px-2 py-0.5 rounded">
                                                    Token Setoran
                                                </span>
                                                <h3 class="font-bold text-base text-ink mt-1">Pindai QR Validasi</h3>
                                                <p class="text-xs text-ink-soft">Tunjukkan QR ini ke kurir penjemput di lokasi.</p>
                                            </div>

                                            <div class="p-4 bg-white border border-ink/10 rounded-2xl inline-block mx-auto shadow-inner">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($p->deposit_code) }}" 
                                                     alt="QR Code {{ $p->deposit_code }}" 
                                                     class="w-36 h-36 sm:w-40 sm:h-40 object-contain mx-auto" />
                                            </div>

                                            <div class="bg-cream/40 p-2.5 rounded-xl border border-ink/5">
                                                <span class="text-[10px] text-ink-soft uppercase font-bold tracking-wider block font-sans">Kode Transaksi Manual</span>
                                                <span class="font-mono font-black text-sm text-ink tracking-wider">{{ $p->deposit_code }}</span>
                                            </div>
                                        </div>
                                        <form method="dialog" class="modal-backdrop bg-ink/30"><button>close</button></form>
                                    </dialog>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-6 text-ink-soft/60 font-sans">Belum ada kunjungan penjemputan bisnis.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    {{-- ========================================================================= --}}
    {{-- KONDISI 4: PENGAJUAN DITOLAK                                              --}}
    {{-- ========================================================================= --}}
    @elseif($user->business_status === 'rejected')
        <div class="bg-white rounded-3xl border border-terracotta/20 p-6 sm:p-8 max-w-lg mx-auto text-center space-y-3">
            <span class="text-[10px] font-mono font-bold uppercase text-terracotta bg-terracotta/10 px-2 py-0.5 rounded">Pengajuan Ditolak</span>
            <h2 class="text-base sm:text-lg font-bold text-ink">Kemitraan Belum Disetujui</h2>
            <p class="text-xs text-ink-soft">Catatan Admin: <em>"{{ $user->business_admin_notes ?? 'Data lokasi belum memenuhi syarat rute armada.' }}"</em></p>
        </div>

    {{-- ========================================================================= --}}
    {{-- KONDISI 5: BELUM PERNAH DAFTAR (FORM PENDAFTARAN AKTIF)                   --}}
    {{-- ========================================================================= --}}
    @else
        <div class="rounded-3xl bg-[#1C1A16] text-[#E5DFD5] border border-white/10 p-6 sm:p-10 shadow-sm">
            <div class="max-w-2xl space-y-2">
                <span class="text-[10px] font-mono font-semibold tracking-widest uppercase text-[#A8A095] border border-white/15 px-3 py-1 rounded-full">
                    SulapaKarya PRO &bull; Kemitraan B2B
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Daftarkan Unit Usaha Anda</h1>
                <p class="text-xs sm:text-sm text-[#A8A095]">Satu akun hanya dapat mendaftarkan 1 unit bisnis. Dapatkan kepastian penjemputan berkala tanpa harus membuat pesanan manual setiap hari.</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-ink/5 p-5 sm:p-8 shadow-none space-y-5">
            <form action="{{ route('bisnis.register') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-ink mb-1.5">Nama Perusahaan / Unit Usaha <span class="text-terracotta">*</span></label>
                        <input type="text" name="business_name" value="{{ old('business_name') }}" required placeholder="Contoh: Kopi Teori Makassar" class="input input-sm input-bordered w-full rounded-xl text-xs bg-cream/20 font-semibold h-11 focus:border-forest">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-ink mb-1.5">Jenis Usaha <span class="text-terracotta">*</span></label>
                        <select name="business_type" required class="select select-sm select-bordered w-full rounded-xl text-xs bg-cream/20 font-semibold h-11 focus:border-forest">
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <option value="Kafe & Coffee Shop">Kafe & Coffee Shop</option>
                            <option value="Warkop Tradisional">Warkop Tradisional</option>
                            <option value="Restoran & Rumah Makan">Restoran & Rumah Makan</option>
                            <option value="Toko Ritel & Swalayan">Toko Ritel & Swalayan</option>
                            <option value="Perkantoran / Coworking Space">Perkantoran / Coworking Space</option>
                            <option value="Hotel & Penginapan">Hotel & Penginapan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-ink mb-1.5">Kecamatan <span class="text-terracotta">*</span></label>
                        <select name="kecamatan" id="select_kecamatan" required onchange="populateKelurahan()" class="select select-sm select-bordered w-full rounded-xl text-xs bg-cream/20 font-semibold h-11 focus:border-forest">
                            <option value="" disabled selected>-- Pilih Kecamatan --</option>
                            <option value="Panakkukang">Panakkukang</option>
                            <option value="Rappocini">Rappocini</option>
                            <option value="Tamalanrea">Tamalanrea</option>
                            <option value="Biringkanaya">Biringkanaya</option>
                            <option value="Ujung Pandang">Ujung Pandang</option>
                            <option value="Mamajang">Mamajang</option>
                            <option value="Makassar">Makassar</option>
                            <option value="Mariso">Mariso</option>
                            <option value="Manggala">Manggala</option>
                            <option value="Tallo">Tallo</option>
                            <option value="Wajo">Wajo</option>
                            <option value="Bontoala">Bontoala</option>
                            <option value="Tamalate">Tamalate</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-ink mb-1.5">Kelurahan <span class="text-terracotta">*</span></label>
                        <select name="kelurahan" id="select_kelurahan" required class="select select-sm select-bordered w-full rounded-xl text-xs bg-cream/20 font-semibold h-11 focus:border-forest">
                            <option value="" disabled selected>-- Pilih Kecamatan Dulu --</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-ink mb-1.5">Alamat Lengkap Titik Jemput <span class="text-terracotta">*</span></label>
                    <textarea name="address" rows="2" required placeholder="Nama jalan, nomor toko, patokan lokasi armada..." class="textarea textarea-bordered w-full rounded-xl text-xs bg-cream/20 leading-relaxed">{{ old('address', $user->address ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-ink mb-1.5">Estimasi Limbah / Pekan (Kg) <span class="text-terracotta">*</span></label>
                        <input type="number" name="waste_estimate_kg" min="1" value="{{ old('waste_estimate_kg', 15) }}" required class="input input-sm input-bordered w-full rounded-xl text-xs bg-cream/20 font-mono font-bold h-11">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-ink mb-1.5">Foto Tempat Usaha <span class="text-terracotta">*</span></label>
                        <input type="file" name="business_photo" accept="image/*" required class="file-input file-input-bordered file-input-sm w-full rounded-xl text-xs bg-cream/20 h-11">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-ink mb-1.5">Catatan Akses Armada (Opsional)</label>
                    <textarea name="business_notes" rows="2" placeholder="Misal: Akses jalan muat motor roda tiga, jemput sebelum kafe buka pukul 10.00 WITA..." class="textarea textarea-bordered w-full rounded-xl text-xs bg-cream/20 leading-relaxed"></textarea>
                </div>

                <button type="submit" class="btn btn-sm w-full bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-bold h-11 shadow-sm mt-2">
                    Kirim Pengajuan Kemitraan Bisnis &rarr;
                </button>
            </form>
        </div>
    @endif

</div>

<!-- Toggle Switch Edit vs View Jadwal -->
<script>
    function toggleScheduleEditMode(isEdit) {
        const viewCard = document.getElementById('schedule_view_card');
        const formCard = document.getElementById('schedule_form_card');
        if (isEdit) {
            if (viewCard) viewCard.classList.add('hidden');
            if (formCard) formCard.classList.remove('hidden');
        } else {
            if (viewCard) viewCard.classList.remove('hidden');
            if (formCard) formCard.classList.add('hidden');
        }
    }
</script>

<!-- Script Data Kelurahan Makassar -->
<script>
    const dataKelurahan = {
        'Panakkukang': ['Karampuang', 'Masale', 'Pampang', 'Panaikang', 'Pandang', 'Sinrijawa', 'Tamamaung', 'Tellumpanuae', 'Tello Baru'],
        'Rappocini': ['Banta-Bantaeng', 'Bonto Makkio', 'Buakana', 'Gunung Sari', 'Karunrung', 'Kassi-Kassi', 'Mapala', 'Minasa Upa', 'Rappocini', 'Tidung'],
        'Tamalanrea': ['Buntusu', 'Kapasa', 'Kapasa Raya', 'Parang Tambung', 'Tamalanrea', 'Tamalanrea Indah', 'Tamalanrea Jaya'],
        'Biringkanaya': ['Bakung', 'Berua', 'Birobuli', 'Bulurokeng', 'Daya', 'Paccerakkang', 'Pai', 'Sudiang', 'Sudiang Raya', 'Untia'],
        'Ujung Pandang': ['Baru', 'Bulogading', 'Kajaolalido', 'Lae-Lae', 'Lajangiru', 'Losari', 'Maloku', 'Mangkura', 'Pisang Selatan', 'Pisang Utara', 'Sawerigading'],
        'Mamajang': ['Baji Mappakasunggu', 'Bonto Biraeng', 'Bonto Lebang', 'Karang Anyar', 'Mamajang Dalam', 'Mamajang Luar', 'Maricaya Selatan', 'Pa\'batang', 'Parang', 'Sambung Jawa', 'Tamparang Keke'],
        'Makassar': ['Bara-Baraya', 'Bara-Baraya Selatan', 'Bara-Baraya Timur', 'Bara-Baraya Utara', 'Barana', 'Lariang Bangi', 'Maccini', 'Maccini Gusung', 'Maccini Parang', 'Maradekaya', 'Maradekaya Selatan', 'Maradekaya Utara', 'Maricaya', 'Maricaya Baru'],
        'Mariso': ['Baji Pamai', 'Bontorannu', 'Kampung Buyang', 'Kunjung Mae', 'Lette', 'Mariso', 'Mattoangin', 'Panambungan', 'Tamarunang'],
        'Manggala': ['Antang', 'Bangkala', 'Batua', 'Bitowa', 'Borangloe', 'Manggala', 'Tamangapa'],
        'Tallo': ['Buloa', 'Bunga Eja Beru', 'Kaluku Bodoa', 'Kalukuang', 'Karuwisi', 'Karuwisi Utara', 'Lakkang', 'Lembo', 'Pannampu', 'Rappojawa', 'Rappokalling', 'Suangga', 'Tallo', 'Tammua', 'Ujung Pandang Baru'],
        'Wajo': ['Butung', 'Ende', 'Malimongan', 'Mampu', 'Melayu', 'Melayu Baru', 'Pattunuang'],
        'Bontoala': ['Baraya', 'Bontoala', 'Bontoala Parang', 'Bontoala Tua', 'Bunga Ejaya', 'Gaddong', 'Layang', 'Malimongan Baru', 'Parang Layang', 'Timungan Lompoa', 'Tompo Balang', 'Wajo Baru'],
        'Tamalate': ['Balang Baru', 'Barombong', 'Bongaya', 'Bonto Duri', 'Jongaya', 'Maccini Sombala', 'Mangasa', 'Manuruki', 'Pa\'baeng-Baeng', 'Parang Tambung']
    };

    function populateKelurahan() {
        const kec = document.getElementById('select_kecamatan');
        const kel = document.getElementById('select_kelurahan');
        if (!kec || !kel) return;
        kel.innerHTML = '<option value="" disabled selected>-- Pilih Kelurahan --</option>';
        if (dataKelurahan[kec.value]) {
            dataKelurahan[kec.value].forEach(item => {
                const opt = document.createElement('option');
                opt.value = item;
                opt.textContent = item;
                kel.appendChild(opt);
            });
        }
    }
</script>

<!-- Script Midtrans Snap -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    document.getElementById('pay-button')?.addEventListener('click', function () {
        const btn = this;
        btn.disabled = true;
        btn.innerText = 'Memuat Midtrans...';

        fetch("{{ route('bisnis.snap-token') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.snap_token) {
                window.snap.pay(data.snap_token, {
                    onSuccess: function (result) {
                        fetch("{{ route('bisnis.pay.success') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            }
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    onPending: function (result) {
                        alert('Menunggu penyelesaian pembayaran.');
                        window.location.reload();
                    },
                    onError: function (result) {
                        alert('Pembayaran gagal, silakan coba lagi.');
                        window.location.reload();
                    },
                    onClose: function () {
                        btn.disabled = false;
                        btn.innerText = 'Bayar via Midtrans Snap →';
                    }
                });
            } else {
                alert('Gagal mengambil token Midtrans: ' + (data.error || 'Terjadi kesalahan'));
                btn.disabled = false;
                btn.innerText = 'Bayar via Midtrans Snap →';
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerText = 'Bayar via Midtrans Snap →';
        });
    });
</script>
@endsection