@extends('layouts.dashboard', ['title' => 'Dashboard Kurir — SulapaKarya'])

@section('dashboard-content')
@php
    $currentUser = \App\Models\User::find(session('user_id'));
    
    // Sortir koleksi: Utamakan status aktif lapangan, lalu utamakan tipe business
    $sortedTasks = $activeTasks->sort(function($a, $b) {
        $aActive = in_array($a->status, ['penjemput_menuju_lokasi', 'penjemput_tiba']) ? 1 : 0;
        $bActive = in_array($b->status, ['penjemput_menuju_lokasi', 'penjemput_tiba']) ? 1 : 0;
        if ($aActive !== $bActive) return $bActive <=> $aActive;

        $aBiz = ($a->deposit_type === 'business' || (($a->user->business_status ?? '') === 'approved')) ? 1 : 0;
        $bBiz = ($b->deposit_type === 'business' || (($b->user->business_status ?? '') === 'approved')) ? 1 : 0;
        return $bBiz <=> $aBiz;
    });

    // Misi Utama yang wajib dikerjakan saat ini
    $currentMission = $sortedTasks->first();

    // Sisa antrean berikutnya
    $upcomingTasks = $currentMission 
        ? $sortedTasks->where('id', '!=', $currentMission->id) 
        : collect();

    $totalCompleted = $completedLogs->count();
@endphp

<div class="space-y-6 text-left">
    
    <!-- Header Ringkas Kurir -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-ink/5">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-forest text-white font-bold flex items-center justify-center text-base shrink-0 overflow-hidden ring-1 ring-forest/20 shadow-xs">
                @if($currentUser && $currentUser->foto_profil)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($currentUser->foto_profil) }}?v={{ time() }}" alt="Profil" class="w-full h-full object-cover" />
                @else
                    {{ strtoupper(substr(session('name', 'K'), 0, 1)) }}
                @endif
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">Misi Penjemputan Armada</h1>
                    <span class="inline-flex items-center whitespace-nowrap bg-forest/10 text-forest text-[10px] font-bold px-2 py-0.5 rounded">Mode Lapangan</span>
                </div>
                <p class="text-xs text-ink-soft mt-0.5">Petugas: <span class="font-semibold text-ink">{{ session('name', 'Kurir') }}</span> &bull; Wilayah Makassar</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="https://drive.google.com/file/d/1wmkeqhpKQgUlFeEltqR7b7xnTHrDIE32/view?usp=sharing" target="_blank" rel="noopener noreferrer" class="btn btn-sm bg-white hover:bg-cream/60 text-ink border border-ink/10 rounded-xl text-xs font-semibold px-3.5 shadow-none w-full sm:w-auto">
                Panduan Standar QC ↗
            </a>
        </div>
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

    <!-- 1. KARTU MISI UTAMA -->
    @if($currentMission)
        @php 
            $wargaCurrent = \App\Models\User::find($currentMission->user_id); 
            $isBusiness = ($currentMission->deposit_type === 'business') || (($wargaCurrent->business_status ?? '') === 'approved');
        @endphp
        <div class="bg-white rounded-3xl border-2 {{ $isBusiness ? 'border-amber-400 bg-amber-50/[0.12] shadow-sm' : 'border-forest/20 shadow-xs' }} p-5 sm:p-7 relative overflow-hidden">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-ink/5">
                <div class="flex items-center gap-2 flex-wrap">
                    @if($isBusiness)
                        <span class="inline-flex items-center gap-1.5 bg-amber-400 text-amber-950 font-black text-[10px] font-mono px-3 py-1 rounded-lg uppercase tracking-wider shadow-2xs">
                            ★ PRIORITAS 1: PENJEMPUTAN MITRA PRO B2B
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 bg-forest/15 text-forest font-black text-[10px] font-mono px-3 py-1 rounded-lg uppercase tracking-wider">
                            PENJEMPUTAN WARGA REGULER
                        </span>
                    @endif

                    <span class="text-[11px] text-ink-soft font-mono font-semibold bg-cream/80 px-2.5 py-1 rounded-lg border border-ink/5">
                        Kode: {{ $currentMission->deposit_code }}
                    </span>
                </div>

                <div>
                    @if($currentMission->status === 'menunggu_penjemput')
                        <span class="inline-flex items-center whitespace-nowrap bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold px-3 py-1 rounded-lg">
                            1. Belum Berangkat
                        </span>
                    @elseif($currentMission->status === 'penjemput_menuju_lokasi')
                        <span class="inline-flex items-center whitespace-nowrap bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold px-3 py-1 rounded-lg">
                            2. Dalam Perjalanan
                        </span>
                    @elseif($currentMission->status === 'penjemput_tiba')
                        <span class="inline-flex items-center whitespace-nowrap bg-purple-50 text-purple-700 border border-purple-200 text-xs font-bold px-3 py-1 rounded-lg">
                            3. Tiba di Titik Jemput
                        </span>
                    @endif
                </div>
            </div>

            <!-- Detail Pemohon & Lokasi Titik Jemput -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 py-5 border-b border-ink/5 items-start">
                <div class="space-y-2.5">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-ink-soft block tracking-wider">
                            {{ $isBusiness ? 'Unit Usaha / Mitra Komersial' : 'Warga Pemohon' }}
                        </span>
                        <h3 class="text-base sm:text-lg font-bold text-ink mt-0.5">
                            {{ $isBusiness && $wargaCurrent->business_name ? $wargaCurrent->business_name : ($wargaCurrent->name ?? 'Pengguna') }}
                        </h3>
                        @if($isBusiness)
                            <span class="text-[11px] text-amber-900 font-semibold block mt-0.5">
                                PIC: {{ $wargaCurrent->name ?? '-' }} ({{ $wargaCurrent->business_type ?? 'Komersial' }})
                            </span>
                        @endif
                        <a href="tel:{{ $wargaCurrent->phone ?? '' }}" class="text-xs text-forest font-mono font-semibold hover:underline inline-flex items-center gap-1 mt-1">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            {{ $wargaCurrent->phone ?? 'Nomor HP tidak ada' }}
                        </a>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <span class="badge bg-cream border border-ink/10 text-ink text-xs font-semibold px-2.5 py-1.5 rounded-lg capitalize">
                            {{ $currentMission->category }}
                        </span>
                        <span class="text-xs font-mono font-bold text-forest bg-forest/10 px-2.5 py-1 rounded-lg">
                            Est. {{ number_format($currentMission->estimated_weight, 1) }} kg
                        </span>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-2.5">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] uppercase font-bold text-ink-soft block tracking-wider">Titik Alamat Penjemputan</span>
                            <a href="https://maps.google.com/?q={{ urlencode($currentMission->pickup_address . ', ' . $currentMission->kelurahan . ', Makassar') }}" 
                               target="_blank" 
                               class="text-[11px] font-bold text-forest hover:underline inline-flex items-center gap-1">
                                Petunjuk Arah Google Maps ↗
                            </a>
                        </div>
                        <p class="text-xs sm:text-sm font-semibold text-ink mt-0.5 leading-relaxed">
                            {{ $currentMission->pickup_address }}
                        </p>
                        <span class="text-[11px] text-ink-soft font-mono block mt-1">
                            Kec. {{ $currentMission->kecamatan ?? '-' }}, Kel. {{ $currentMission->kelurahan ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi Lapangan -->
            <div class="pt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="text-xs text-ink-soft leading-relaxed">
                    @if($currentMission->status === 'menunggu_penjemput')
                        Tekan konfirmasi untuk memulai perjalanan armada ke titik lokasi di atas.
                    @elseif($currentMission->status === 'penjemput_menuju_lokasi')
                        Tekan konfirmasi ketika armada kurir telah tiba di depan toko/rumah.
                    @elseif($currentMission->status === 'penjemput_tiba')
                        Pindai kode QR atau input kode manual pengguna untuk menimbang sampah.
                    @endif
                </div>

                <div class="w-full sm:w-auto shrink-0">
                    @if($currentMission->status === 'menunggu_penjemput')
                        <form action="{{ route('penjemput.updateStatus', $currentMission->id) }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="status" value="penjemput_menuju_lokasi">
                            <button type="submit" class="btn btn-sm w-full sm:w-auto bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-bold px-6 h-11 shadow-sm">
                                Mulai Perjalanan Menuju Lokasi &rarr;
                            </button>
                        </form>
                    @elseif($currentMission->status === 'penjemput_menuju_lokasi')
                        <form action="{{ route('penjemput.updateStatus', $currentMission->id) }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="status" value="penjemput_tiba">
                            <button type="submit" class="btn btn-sm w-full sm:w-auto bg-maritime hover:bg-maritime-dark text-white border-none rounded-xl text-xs font-bold px-6 h-11 shadow-sm">
                                Saya Sudah Tiba di Lokasi &rarr;
                            </button>
                        </form>
                    @elseif($currentMission->status === 'penjemput_tiba')
                        <button type="button" onclick="openWeightModal('{{ $currentMission->id }}')" class="btn btn-sm w-full sm:w-auto bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-bold px-6 h-11 shadow-sm">
                            Pindai QR & Input Timbangan
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- MODAL EVALUASI QC & VALIDASI SCAN QR -->
        <dialog id="timbang_modal_{{ $currentMission->id }}" class="modal modal-middle">
            <div class="modal-box w-11/12 max-w-lg bg-white rounded-3xl border border-ink/10 p-5 sm:p-7 text-left shadow-2xl overflow-y-auto max-h-[88vh] my-auto">
                <button type="button" onclick="closeWeightModal('{{ $currentMission->id }}')" class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft hover:bg-cream">✕</button>
                
                <div class="border-b border-ink/5 pb-3 mb-4">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-forest bg-forest/10 px-2 py-0.5 rounded">
                        Verifikasi Lapangan
                    </span>
                    <h3 class="font-bold text-base sm:text-lg text-ink mt-1">Evaluasi Mutu & Scan QR</h3>
                    <p class="text-xs text-ink-soft mt-0.5">{{ $isBusiness ? 'Mitra PRO: ' . ($wargaCurrent->business_name ?? $wargaCurrent->name) : 'Warga: ' . ($wargaCurrent->name ?? 'Warga') }}</p>
                </div>

                <div class="space-y-2 mb-4">
                    <label class="text-xs font-semibold text-ink block">Kondisi Fisik Sampah: <span class="text-terracotta">*</span></label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" id="btn_qc_pass_{{ $currentMission->id }}" onclick="setQcDecision('{{ $currentMission->id }}', 'pass')"
                            class="btn btn-sm bg-forest text-white border-none rounded-xl text-xs font-semibold normal-case h-10 shadow-none">
                            Lolos QC (Terima)
                        </button>
                        <button type="button" id="btn_qc_reject_{{ $currentMission->id }}" onclick="setQcDecision('{{ $currentMission->id }}', 'reject')"
                            class="btn btn-sm bg-cream text-ink-soft border border-ink/10 rounded-xl text-xs font-semibold normal-case h-10 shadow-none">
                            Tidak Layak (Tolak)
                        </button>
                    </div>
                </div>
                
                <!-- FORM SKENARIO 1: LOLOS QC -->
                <form id="form_qc_pass_{{ $currentMission->id }}" action="{{ route('penjemput.completeTransaction', $currentMission->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="qc_action" value="accept">
                    
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-semibold text-ink">Berat Timbangan Aktual (kg) <span class="text-terracotta">*</span></label>
                            <span class="text-[11px] text-ink-soft font-mono">Estimasi: {{ number_format($currentMission->estimated_weight, 2) }} kg</span>
                        </div>
                        <div class="relative">
                            <input type="number" step="0.01" min="0.01" name="actual_weight" id="actual_weight_{{ $currentMission->id }}" 
                                   value="{{ old('actual_weight', $currentMission->estimated_weight) }}" required 
                                   oninput="updateLivePoints('{{ $currentMission->id }}')"
                                   class="input input-bordered w-full rounded-xl font-mono font-bold text-lg text-ink bg-cream/20 focus:outline-none focus:border-forest pr-12 h-11">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-semibold text-ink-soft">kg</span>
                        </div>
                    </div>

                    @php
                        $wastePrice = \App\Models\WastePrice::where('name', $currentMission->category)->first();
                        $taskRate = $wastePrice ? ($wastePrice->point_per_kg ?? 0) : 400;
                    @endphp

                    <div class="bg-forest/[0.04] border border-forest/20 p-3 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-ink-soft font-bold uppercase tracking-wider block">Poin Reward Diperoleh</span>
                            <span class="text-xs text-ink-soft font-mono">{{ $currentMission->category }}: {{ number_format($taskRate) }} poin/kg</span>
                        </div>
                        <div class="text-right">
                            <span id="live_points_{{ $currentMission->id }}" class="text-xl font-bold font-mono text-forest" data-rate="{{ $taskRate }}">0</span>
                            <span class="text-[10px] font-bold text-forest block">Poin Reward</span>
                        </div>
                    </div>

                    <div class="p-3.5 bg-cream/30 border border-ink/10 rounded-2xl">
                        <label class="label cursor-pointer justify-start gap-3 p-0">
                            <input type="checkbox" name="is_qc_passed" value="1" required 
                                   class="checkbox checkbox-sm checkbox-success rounded-lg border-ink/20 shrink-0" />
                            <div class="text-left">
                                <span class="text-xs font-bold text-ink block">Sampah Memenuhi Standar QC</span>
                                <span class="text-[11px] text-ink-soft leading-tight block">Kondisi fisik material bersih, kering, dan terpilah sesuai SOP.</span>
                            </div>
                        </label>
                    </div>

                    <div class="space-y-2 pt-1">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-ink">Pindai QR Code di HP Pengguna: <span class="text-terracotta">*</span></label>
                            <span class="text-[10px] font-mono text-forest font-bold">Wajib di Lokasi</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-1.5 border border-ink/10 p-1 rounded-xl bg-white text-xs font-semibold">
                            <button type="button" id="btn_mode_scan_{{ $currentMission->id }}" onclick="toggleVerificationMode('{{ $currentMission->id }}', 'scan')" 
                                class="btn btn-xs bg-forest text-white border-none normal-case rounded-lg h-8">
                                Buka Scanner Kamera
                            </button>
                            <button type="button" id="btn_mode_paste_{{ $currentMission->id }}" onclick="toggleVerificationMode('{{ $currentMission->id }}', 'paste')" 
                                class="btn btn-xs bg-white text-ink-soft hover:bg-cream border-none normal-case rounded-lg h-8">
                                Masukkan Kode Manual
                            </button>
                        </div>

                        <!-- KONTEN SCANNER KANVAS PERBAIKAN -->
                        <div id="container_scan_{{ $currentMission->id }}" class="space-y-2 pt-1">
                            <div class="w-full max-w-[260px] aspect-square mx-auto bg-black rounded-2xl overflow-hidden border border-ink/10 relative shadow-inner flex flex-col items-center justify-center p-2">
                                <div id="reader_{{ $currentMission->id }}" class="w-full h-full"></div>
                            </div>

                            <div id="camera_error_msg_{{ $currentMission->id }}" class="hidden p-2.5 bg-amber-500/10 border border-amber-300 rounded-xl text-[11px] text-amber-900 text-center font-medium">
                                Kamera tidak terdeteksi atau diblokir browser. <br>
                                <button type="button" onclick="startScanner('{{ $currentMission->id }}')" class="underline font-bold text-forest mt-1 inline-block">Coba Buka Ulang Kamera</button> 
                                atau gunakan <strong class="underline cursor-pointer" onclick="toggleVerificationMode('{{ $currentMission->id }}', 'paste')">Input Kode Manual</strong>.
                            </div>

                            <p class="text-[10px] text-ink-soft text-center font-medium">Arahkan kamera belakang ke layar ponsel setoran pengguna.</p>
                        </div>

                        <div id="container_paste_{{ $currentMission->id }}" class="hidden space-y-1">
                            <input type="text" name="qr_code_warga" id="qr_input_{{ $currentMission->id }}" 
                                placeholder="Masukkan kode transaksi setoran..." 
                                class="input input-bordered w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 font-mono h-11">
                            <span class="text-[10px] text-ink-soft block">Kode tercantum di detail riwayat setoran akun pengguna.</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm w-full bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-bold normal-case h-11 shadow-sm mt-2">
                        Selesaikan Penjemputan & Simpan Timbangan &rarr;
                    </button>
                </form>

                <!-- FORM SKENARIO 2: TOLAK SETORAN -->
                <form id="form_qc_reject_{{ $currentMission->id }}" action="{{ route('penjemput.completeTransaction', $currentMission->id) }}" method="POST" class="hidden space-y-4">
                    @csrf
                    <input type="hidden" name="qc_action" value="reject">

                    <div class="p-3 bg-terracotta/10 border border-terracotta/20 rounded-xl text-terracotta text-xs font-medium leading-relaxed">
                        Setoran yang ditolak tidak akan menghasilkan poin. Notifikasi alasan akan dikirimkan langsung ke akun pengguna untuk evaluasi.
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">Alasan Penolakan QC <span class="text-terracotta">*</span></label>
                        <select name="reject_reason" required class="select select-bordered select-sm w-full rounded-xl text-xs bg-cream/20 font-medium focus:outline-none focus:border-terracotta h-10">
                            <option value="" disabled selected>-- Pilih Alasan Penolakan --</option>
                            <option value="Sampah basah kuyup atau lembap berjamur">Sampah basah kuyup atau lembap berjamur</option>
                            <option value="Tercemar sisa makanan, minyak, atau zat kimia">Tercemar sisa makanan, minyak, atau zat berbau</option>
                            <option value="Bercampur sampah residu kotor dan tidak dipilah">Bercampur sampah residu kotor dan tidak dipilah</option>
                            <option value="Material bukan kategori sampah daur ulang terdaftar">Bukan kategori sampah daur ulang terdaftar</option>
                            <option value="Lainnya">Alasan fisik lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">Catatan Tambahan untuk Pengguna (Opsional)</label>
                        <textarea name="reject_notes" rows="2" placeholder="Tuliskan catatan kondisi fisik sampah secara singkat..."
                            class="textarea textarea-bordered w-full rounded-xl text-xs bg-cream/20 focus:outline-none focus:border-terracotta leading-relaxed"></textarea>
                    </div>

                    <button type="submit" class="btn btn-sm w-full bg-terracotta hover:bg-red-700 text-white border-none rounded-xl text-xs font-bold normal-case h-11 shadow-none mt-2">
                        Konfirmasi Penolakan QC &rarr;
                    </button>
                </form>

            </div>
        </dialog>
    @else
        <div class="bg-white rounded-3xl border border-ink/5 p-8 text-center space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-forest/10 text-forest flex items-center justify-center mx-auto text-xl font-bold">✓</div>
            <h3 class="text-lg font-bold text-ink">Semua Penjemputan Selesai</h3>
            <p class="text-xs text-ink-soft max-w-md mx-auto">Tidak ada antrean penjemputan sampah aktif untuk Anda saat ini.</p>
        </div>
    @endif

    <!-- ANTREAN PENJEMPUTAN BERIKUTNYA -->
    <div class="bg-white border border-ink/10 rounded-2xl p-5 sm:p-6 shadow-none space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-ink/5">
            <div>
                <h2 class="text-sm font-bold text-ink">Antrean Rute Selanjutnya</h2>
                <p class="text-[11px] text-ink-soft">Urutan penjemputan diprioritaskan untuk Mitra Bisnis PRO sebelum warga reguler.</p>
            </div>
            <span class="inline-flex items-center whitespace-nowrap text-[11px] font-mono font-bold text-ink bg-cream px-2.5 py-1 rounded-lg">
                {{ $upcomingTasks->count() }} Menunggu
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-xs">
                <thead>
                    <tr class="bg-cream/40 text-ink-soft border-b border-ink/5 font-semibold text-[10px] uppercase">
                        <th class="py-2.5 pl-3 w-16">Prioritas</th>
                        <th class="py-2.5 w-24">Tipe</th>
                        <th class="py-2.5">Pemohon / Unit Usaha</th>
                        <th class="py-2.5">Kategori</th>
                        <th class="py-2.5">Estimasi</th>
                        <th class="py-2.5">Alamat Penjemputan</th>
                        <th class="py-2.5 pr-3 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/5 font-medium">
                    @forelse($upcomingTasks as $index => $task)
                        @php 
                            $wargaNext = \App\Models\User::find($task->user_id); 
                            $isTaskBusiness = ($task->deposit_type === 'business') || (($wargaNext->business_status ?? '') === 'approved');
                        @endphp
                        <tr class="hover:bg-cream/20 transition-colors {{ $isTaskBusiness ? 'bg-amber-400/[0.04]' : '' }}">
                            <td class="py-3 pl-3 font-mono text-xs font-bold {{ $isTaskBusiness ? 'text-amber-800' : 'text-ink-soft' }}">
                                #{{ $loop->iteration + 1 }}
                            </td>
                            <td class="py-3">
                                @if($isTaskBusiness)
                                    <span class="inline-flex items-center bg-amber-400 text-amber-950 font-black text-[9px] font-mono px-2 py-0.5 rounded shadow-2xs">★ PRO B2B</span>
                                @else
                                    <span class="inline-flex items-center bg-cream text-ink border border-ink/10 font-bold text-[9px] font-mono px-2 py-0.5 rounded">WARGA</span>
                                @endif
                            </td>
                            <td class="py-3 font-bold text-ink text-xs">
                                {{ $isTaskBusiness && $wargaNext->business_name ? $wargaNext->business_name : ($wargaNext->name ?? 'Pengguna') }}
                            </td>
                            <td class="py-3"><span class="badge bg-cream border border-ink/10 text-ink text-[10px] px-2 py-0.5 rounded capitalize">{{ $task->category }}</span></td>
                            <td class="py-3 font-mono text-ink font-bold">{{ number_format($task->estimated_weight, 1) }} kg</td>
                            <td class="py-3 text-ink-soft max-w-[200px] truncate" title="{{ $task->pickup_address }}">{{ $task->pickup_address }}</td>
                            <td class="py-3 pr-3 text-right"><span class="inline-flex items-center text-[10px] font-semibold text-ink-soft bg-cream/60 px-2 py-1 rounded border border-ink/5">Antrean Berikutnya</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-6 text-center text-ink-soft/60 text-xs font-medium">Tidak ada antrean rute penjemputan tambahan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- RIWAYAT PENJEMPUTAN SELESAI HARI INI -->
    <div class="bg-white border border-ink/10 rounded-2xl p-5 sm:p-6 shadow-none space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-ink/5">
            <div>
                <h2 class="text-sm font-bold text-ink">Riwayat Tugas Tuntas Hari Ini</h2>
                <p class="text-[11px] text-ink-soft">Daftar transaksi yang sudah Anda evaluasi QC & timbang hari ini.</p>
            </div>
            <span class="inline-flex items-center whitespace-nowrap text-[11px] font-mono font-bold text-forest bg-forest/10 px-2.5 py-1 rounded-lg">{{ $totalCompleted }} Selesai</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-xs">
                <thead>
                    <tr class="bg-cream/30 text-ink-soft border-b border-ink/5 font-semibold text-[10px] uppercase">
                        <th class="py-2.5 pl-3">Pengguna / Mitra</th>
                        <th class="py-2.5">Tipe</th>
                        <th class="py-2.5">Kategori</th>
                        <th class="py-2.5">Berat Aktual</th>
                        <th class="py-2.5">Poin Reward</th>
                        <th class="py-2.5 pr-3 text-right">Hasil QC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/5 font-medium">
                    @forelse($completedLogs as $log)
                        @php 
                            $pemohon = \App\Models\User::find($log->user_id); 
                            $isLogBusiness = ($log->deposit_type === 'business') || (($pemohon->business_status ?? '') === 'approved');
                        @endphp
                        <tr class="hover:bg-cream/20 transition-colors">
                            <td class="py-3 pl-3 font-semibold text-ink">{{ $isLogBusiness && $pemohon->business_name ? $pemohon->business_name : ($pemohon->name ?? 'Pengguna') }}</td>
                            <td class="py-3">
                                @if($isLogBusiness)
                                    <span class="badge badge-xs bg-amber-400 text-amber-950 font-bold border-none text-[8px] font-mono">PRO</span>
                                @else
                                    <span class="text-ink-soft text-[10px] font-mono">Warga</span>
                                @endif
                            </td>
                            <td class="py-3 capitalize">{{ $log->category }}</td>
                            <td class="py-3 font-semibold text-ink font-mono">{{ number_format($log->actual_weight ?? 0, 1) }} kg</td>
                            <td class="py-3 font-mono font-bold">
                                @if(($log->points_earned ?? 0) > 0)
                                    <span class="text-forest">+{{ number_format($log->points_earned) }}</span>
                                @else
                                    <span class="text-ink-soft/50">0 Poin</span>
                                @endif
                            </td>
                            <td class="py-3 pr-3 text-right">
                                @if($log->status === 'ditolak' || $log->status === 'ditolak_qc' || $log->status === 'rejected')
                                    <span class="inline-flex items-center whitespace-nowrap bg-terracotta/10 text-terracotta border border-terracotta/20 text-[10px] font-bold px-2 py-0.5 rounded">Ditolak QC &bull; {{ $log->updated_at ? $log->updated_at->format('H:i') : '' }} WITA</span>
                                @else
                                    <span class="inline-flex items-center whitespace-nowrap bg-forest/10 text-forest border border-forest/20 text-[10px] font-bold px-2 py-0.5 rounded">Lolos QC &bull; {{ $log->updated_at ? $log->updated_at->format('H:i') : '' }} WITA</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-ink-soft/60 text-xs font-medium">Belum ada penjemputan yang diselesaikan hari ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let html5QrScannerMap = {};

    function openWeightModal(taskId) {
        const modal = document.getElementById('timbang_modal_' + taskId);
        if (modal) {
            modal.showModal();
            updateLivePoints(taskId);
            setQcDecision(taskId, 'pass');
            setTimeout(() => {
                startScanner(taskId);
            }, 300);
        }
    }

    function closeWeightModal(taskId) {
        stopScanner(taskId);
        const modal = document.getElementById('timbang_modal_' + taskId);
        if (modal) modal.close();
    }

    function setQcDecision(taskId, decision) {
        const passBtn = document.getElementById('btn_qc_pass_' + taskId);
        const rejectBtn = document.getElementById('btn_qc_reject_' + taskId);
        const formPass = document.getElementById('form_qc_pass_' + taskId);
        const formReject = document.getElementById('form_qc_reject_' + taskId);

        if (!passBtn || !rejectBtn || !formPass || !formReject) return;

        if (decision === 'pass') {
            passBtn.className = "btn btn-sm bg-forest text-white border-none rounded-xl text-xs font-semibold normal-case h-10 shadow-none";
            rejectBtn.className = "btn btn-sm bg-cream text-ink-soft border border-ink/10 rounded-xl text-xs font-semibold normal-case h-10 shadow-none";
            formPass.classList.remove('hidden');
            formReject.classList.add('hidden');
            startScanner(taskId);
        } else {
            rejectBtn.className = "btn btn-sm bg-terracotta text-white border-none rounded-xl text-xs font-semibold normal-case h-10 shadow-none";
            passBtn.className = "btn btn-sm bg-cream text-ink-soft border border-ink/10 rounded-xl text-xs font-semibold normal-case h-10 shadow-none";
            formPass.classList.add('hidden');
            formReject.classList.remove('hidden');
            stopScanner(taskId);
        }
    }

    function updateLivePoints(taskId) {
        const weightInput = document.getElementById('actual_weight_' + taskId);
        const pointsDisplay = document.getElementById('live_points_' + taskId);
        if (!weightInput || !pointsDisplay) return;

        let weight = parseFloat(weightInput.value) || 0;
        let rate = parseInt(pointsDisplay.getAttribute('data-rate')) || 400;
        let finalPoints = Math.round(weight * rate);

        pointsDisplay.innerText = new Intl.NumberFormat('id-ID').format(finalPoints);
    }

    function toggleVerificationMode(taskId, mode) {
        const pasteBtn = document.getElementById('btn_mode_paste_' + taskId);
        const scanBtn = document.getElementById('btn_mode_scan_' + taskId);
        const pasteContainer = document.getElementById('container_paste_' + taskId);
        const scanContainer = document.getElementById('container_scan_' + taskId);
        const qrInput = document.getElementById('qr_input_' + taskId);

        if (mode === 'paste') {
            if (pasteBtn) pasteBtn.className = "btn btn-xs bg-forest text-white border-none normal-case rounded-lg h-8";
            if (scanBtn) scanBtn.className = "btn btn-xs bg-white text-ink-soft hover:bg-cream border-none normal-case rounded-lg h-8";
            if (pasteContainer) pasteContainer.classList.remove('hidden');
            if (scanContainer) scanContainer.classList.add('hidden');
            if (qrInput) qrInput.setAttribute('required', 'required');
            stopScanner(taskId);
        } else {
            if (scanBtn) scanBtn.className = "btn btn-xs bg-forest text-white border-none normal-case rounded-lg h-8";
            if (pasteBtn) pasteBtn.className = "btn btn-xs bg-white text-ink-soft hover:bg-cream border-none normal-case rounded-lg h-8";
            if (pasteContainer) pasteContainer.classList.add('hidden');
            if (scanContainer) scanContainer.classList.remove('hidden');
            if (qrInput) qrInput.removeAttribute('required');
            startScanner(taskId);
        }
    }

    // FUNGSI UTAMA SCANNER DENGAN DETEKSI FISIK KAMERA HP
    function startScanner(taskId) {
        const errorMsg = document.getElementById('camera_error_msg_' + taskId);
        if (errorMsg) errorMsg.classList.add('hidden');

        if (html5QrScannerMap[taskId]) {
            return;
        }

        const html5QrCode = new Html5Qrcode("reader_" + taskId);
        html5QrScannerMap[taskId] = html5QrCode;
        const config = { fps: 10, qrbox: { width: 180, height: 180 } };

        const onScanSuccess = (decodedText) => {
            const qrInput = document.getElementById('qr_input_' + taskId);
            if (qrInput) qrInput.value = decodedText;
            if (navigator.vibrate) navigator.vibrate(100);
            alert("Kode Terverifikasi: " + decodedText);
            toggleVerificationMode(taskId, 'paste');
        };

        // Deteksi semua perangkat kamera di HP
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                // Cari kamera yang berlabel back, rear, atau environment (kamera belakang)
                let backCamera = devices.find(device => 
                    device.label.toLowerCase().includes('back') || 
                    device.label.toLowerCase().includes('rear') || 
                    device.label.toLowerCase().includes('environment')
                );

                // Jika ketemu kamera belakang spesifik, pakai deviceId-nya
                let cameraId = backCamera ? backCamera.id : devices[devices.length - 1].id;

                html5QrCode.start(
                    cameraId,
                    config,
                    onScanSuccess,
                    () => {}
                ).catch(err => {
                    console.warn("Gagal memulai kamera via deviceId:", err);
                    fallbackFacingMode(html5QrCode, config, onScanSuccess, taskId);
                });
            } else {
                fallbackFacingMode(html5QrCode, config, onScanSuccess, taskId);
            }
        }).catch(err => {
            console.warn("Gagal mendapatkan daftar kamera:", err);
            fallbackFacingMode(html5QrCode, config, onScanSuccess, taskId);
        });
    }

    function fallbackFacingMode(html5QrCode, config, onScanSuccess, taskId) {
        const errorMsg = document.getElementById('camera_error_msg_' + taskId);
        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            () => {}
        ).catch(() => {
            return html5QrCode.start({ facingMode: "user" }, config, onScanSuccess, () => {});
        }).catch(err => {
            console.error("Semua metode kamera gagal:", err);
            if (errorMsg) errorMsg.classList.remove('hidden');
            toggleVerificationMode(taskId, 'paste');
        });
    }

    function stopScanner(taskId) {
        if (html5QrScannerMap[taskId]) {
            html5QrScannerMap[taskId].stop().then(() => {
                delete html5QrScannerMap[taskId];
            }).catch(err => {
                console.error("Gagal menghentikan kamera: ", err);
                delete html5QrScannerMap[taskId];
            });
        }
    }
</script>
@endsection