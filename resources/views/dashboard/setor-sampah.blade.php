@extends('layouts.dashboard', ['title' => 'Setor Sampah — SulapaKarya'])

@section('dashboard-content')
@php
    $user = auth()->user() ?? \App\Models\User::find(session('user_id'));
    $hasAddress = !blank($user->address) && !blank($user->kecamatan) && !blank($user->kelurahan);
    $isProPartner = ($user->business_status === 'approved');
    
    // Ambil data harga/kategori sampah dari database jika belum di-pass dari controller
    $wasteCategories = $wastePrices ?? \App\Models\WastePrice::orderBy('name', 'asc')->get();
@endphp

<div class="max-w-3xl mx-auto space-y-6 text-left">
    
    <!-- Header Ringkas -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-ink/5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">Formulir Setor Sampah</h1>
            <p class="text-xs text-ink-soft mt-0.5">Pilah sampah kering, tentukan jadwal penjemputan, dan dapatkan poin reward.</p>
        </div>
        <span class="badge bg-forest/10 text-forest border border-forest/20 text-[11px] font-semibold px-2.5 py-1 rounded-lg">
            Wilayah Makassar
        </span>
    </div>

    <!-- Alert Notifikasi -->
    @if($errors->any())
        <div class="alert alert-error bg-terracotta/10 border-terracotta/20 text-terracotta rounded-2xl text-xs font-semibold p-4 shadow-none">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success bg-forest/10 border-forest/20 text-forest rounded-2xl text-xs font-semibold p-4 shadow-none">
            {{ session('success') }}
        </div>
    @endif

    {{-- VALIDASI: Jika user belum melengkapi alamat di profil --}}
    @if(!$hasAddress)
        <div class="bg-white border border-ink/5 rounded-2xl p-8 text-center shadow-none space-y-3">
            <div class="w-10 h-10 bg-terracotta/10 text-terracotta rounded-xl flex items-center justify-center mx-auto">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <h2 class="font-bold text-base text-ink">Alamat Penjemputan Belum Lengkap</h2>
            <p class="text-xs text-ink-soft max-w-md mx-auto leading-relaxed">
                Lengkapi kecamatan, kelurahan, dan detail alamat rumah Anda di profil terlebih dahulu sebelum mengajukan penjemputan armada.
            </p>
            <div class="pt-2">
                <a href="{{ route('profile.index') }}" class="btn btn-sm bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold px-5 h-10 shadow-none">
                    Lengkapi Alamat Profil
                </a>
            </div>
        </div>
    @else
        {{-- Form Pengajuan Setor Sampah --}}
        <div class="bg-white border border-ink/5 rounded-2xl p-6 sm:p-7 shadow-none">
                <form id="form_setor_sampah" action="{{ route('setor-sampah.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="reward_type" value="points">

                {{-- SECTION KHUSUS: PEMILIHAN TIPE SETORAN UNTUK MITRA PRO --}}
                @if($isProPartner)
                    <div class="p-4 bg-amber-500/10 border border-amber-400/30 rounded-2xl space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-amber-900 bg-amber-200/60 px-2 py-0.5 rounded">
                                    Mitra PRO Terdeteksi
                                </span>
                                <h3 class="text-xs font-bold text-ink mt-1">Setor Sampah Atas Nama</h3>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <label class="flex items-center gap-2.5 p-2.5 bg-white border border-amber-300/60 rounded-xl cursor-pointer hover:border-forest transition-colors">
                                <input type="radio" name="is_business_pickup" value="0" checked onchange="togglePickupOrigin(false)" class="radio radio-xs radio-success">
                                <div>
                                    <span class="font-bold text-ink block">Pribadi / Warga</span>
                                    <span class="text-[10px] text-ink-soft">Sampah harian rumah tangga</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-2.5 bg-white border border-amber-300/60 rounded-xl cursor-pointer hover:border-forest transition-colors">
                                <input type="radio" name="is_business_pickup" value="1" onchange="togglePickupOrigin(true)" class="radio radio-xs radio-success">
                                <div>
                                    <span class="font-bold text-ink block truncate">{{ $user->business_name }}</span>
                                    <span class="text-[10px] text-amber-800 font-medium">Limbah operasional unit usaha</span>
                                </div>
                            </label>
                        </div>
                    </div>
                @endif

                <!-- SECTION 1: INFORMASI SAMPAH -->
                <div class="space-y-4">
                    <div class="pb-2 border-b border-ink/5">
                        <h2 class="font-bold text-sm text-ink">1. Klasifikasi & Bobot Sampah</h2>
                        <p class="text-[11px] text-ink-soft">Pilih kategori sampah yang telah dipilah sesuai standar SOP QC.</p>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Kategori Sampah dari Database -->
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-semibold text-xs text-ink">Kategori Sampah <span class="text-terracotta">*</span></span></label>
                            <select name="category" id="category_select" required onchange="updatePointRatePreview()"
                                class="select select-bordered select-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 font-semibold text-ink @error('category') border-terracotta @enderror">
                                <option value="" disabled {{ old('category') ? '' : 'selected' }}>-- Pilih Kategori Sampah --</option>
                                @foreach($wasteCategories as $cat)
                                    <option value="{{ $cat->name }}" 
                                        data-rate="{{ $cat->point_per_kg ?? 0 }}"
                                        {{ old('category') === $cat->name ? 'selected' : '' }}>
                                        {{ $cat->name }} ({{ number_format($cat->point_per_kg ?? 0, 0, ',', '.') }} Poin/kg)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sub Kategori / Detail Barang -->
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-semibold text-xs text-ink">Detail / Nama Barang <span class="text-ink-soft/60 font-normal">(Opsional)</span></span></label>
                            <input type="text" name="sub_category" value="{{ old('sub_category') }}" placeholder="Contoh: Botol Kaca Sirup, Kardus Packing..."
                                class="input input-bordered input-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 font-medium text-ink">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Perkiraan Berat -->
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-semibold text-xs text-ink">Estimasi Berat (kg) <span class="text-terracotta">*</span></span></label>
                            <div class="relative">
                                <input type="number" step="0.1" name="estimated_weight" id="estimated_weight_input" value="{{ old('estimated_weight') }}" placeholder="0.0" required oninput="calculateEstimatedPoints()"
                                    class="input input-bordered input-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 pr-10 font-mono font-bold text-ink @error('estimated_weight') border-terracotta @enderror">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-semibold text-ink-soft">kg</span>
                            </div>
                        </div>

                        <!-- Preview Estimasi Poin -->
                        <div class="bg-cream/30 border border-ink/5 p-2.5 rounded-xl flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-ink-soft font-bold uppercase tracking-wider block">Estimasi Poin Reward</span>
                                <span id="rate_preview_text" class="text-[10px] text-ink-soft font-mono">Pilih jenis sampah</span>
                            </div>
                            <div class="text-right">
                                <span id="estimated_points_display" class="text-base font-bold font-mono text-forest">0</span>
                                <span class="text-[10px] font-semibold text-forest block">Poin</span>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Foto Fisik Sampah (Sudah Tanpa Capture) -->
                    <div class="form-control">
                        <label class="label py-1"><span class="label-text font-semibold text-xs text-ink">Foto Bukti Fisik Sampah <span class="text-terracotta">*</span></span></label>
                        <!-- Atribut capture="environment" DIHAPUS agar HP bisa membuka Galeri & Kamera -->
                        <input type="file" name="photo" id="photo_input" accept="image/png, image/jpeg, image/jpg, image/webp" required 
                            class="file-input file-input-bordered file-input-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 @error('photo') border-terracotta @enderror" />
                        <span class="text-[10px] text-ink-soft mt-1" id="photo_status">Sistem otomatis mengompresi gambar dari kamera HP.</span>
                    </div>
                </div>

                <!-- SECTION 2: ALAMAT PENJEMPUTAN -->
                <div class="space-y-4 pt-4 border-t border-ink/5">
                    <div class="flex items-center justify-between pb-2 border-b border-ink/5">
                        <div>
                            <h2 class="font-bold text-sm text-ink">2. Titik Penjemputan Armada</h2>
                            <p id="pickup_origin_subtitle" class="text-[11px] text-ink-soft">Alamat disesuaikan dengan data profil Anda.</p>
                        </div>
                        <a href="{{ route('profile.index') }}" class="text-[11px] font-semibold text-forest hover:underline">
                            Ubah di Profil &rarr;
                        </a>
                    </div>

                    <!-- Hidden input data wilayah untuk backend request -->
                    <input type="hidden" name="kecamatan" id="input_kecamatan" value="{{ $user->kecamatan }}">
                    <input type="hidden" name="kelurahan" id="input_kelurahan" value="{{ $user->kelurahan }}">

                    <div class="bg-cream/20 p-4 rounded-xl border border-ink/5 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-[10px] text-ink-soft font-bold uppercase block">Kecamatan</span>
                                <span id="display_kecamatan" class="font-semibold text-ink">{{ $user->kecamatan }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-ink-soft font-bold uppercase block">Kelurahan</span>
                                <span id="display_kelurahan" class="font-semibold text-ink">{{ $user->kelurahan }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1">Detail Alamat Lengkap & Patokan Lokasi <span class="text-terracotta">*</span></label>
                            <textarea name="pickup_address" id="input_pickup_address" rows="2" required 
                                class="textarea textarea-bordered w-full rounded-xl text-xs bg-white focus:outline-none focus:border-forest leading-relaxed text-ink @error('pickup_address') border-terracotta @enderror">{{ old('pickup_address', $user->address) }}</textarea>
                            <span class="text-[10px] text-ink-soft mt-0.5 block">Pastikan nomor bangunan atau patokan tercantum jelas agar kurir mudah menemukan lokasi.</span>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: RENCANA JADWAL & WAKTU PENJEMPUTAN -->
                <div class="space-y-4 pt-4 border-t border-ink/5">
                    <div class="pb-2 border-b border-ink/5">
                        <h2 class="font-bold text-sm text-ink">3. Jadwal Penjemputan</h2>
                        <p class="text-[11px] text-ink-soft">Pilih hari dan slot waktu penjemputan yang tersedia.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @php
                            $minPickupDate = date('Y-m-d', strtotime('+1 day'));
                        @endphp
                        
                        <!-- Input Tanggal -->
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-semibold text-xs text-ink">Tanggal Jemput <span class="text-terracotta">*</span></span></label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-soft pointer-events-none z-10">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                </span>
                                <input type="date" name="pickup_date" min="{{ $minPickupDate }}" value="{{ old('pickup_date') }}" required
                                    class="input input-bordered input-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 pl-10 cursor-pointer font-medium text-ink">
                            </div>
                            <span id="pickup-date-hint" class="text-[10px] text-ink-soft mt-1">Penjemputan paling cepat H+1 ({{ \Carbon\Carbon::parse($minPickupDate)->translatedFormat('d M Y') }}).</span>
                        </div>

                        <!-- Dropdown Slot Jam -->
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-semibold text-xs text-ink">Waktu Penjemputan <span class="text-terracotta">*</span></span></label>
                            <input type="hidden" name="pickup_time" id="pickup_time_input" value="{{ old('pickup_time') }}" required>

                            <div id="pickup-time-dropdown" class="relative">
                                <button type="button" id="pickup-time-trigger" aria-haspopup="listbox" aria-expanded="false"
                                    class="input input-bordered input-sm w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 pl-10 pr-8 flex items-center cursor-pointer text-left font-medium">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-soft pointer-events-none z-10">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    </span>
                                    <span id="pickup-time-label" class="truncate text-ink-soft">Pilih slot jam</span>
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-ink-soft pointer-events-none text-[10px]">▼</span>
                                </button>

                                <ul id="pickup-time-menu" role="listbox" class="hidden absolute z-30 mt-1 w-full max-h-56 overflow-auto rounded-xl border border-ink/10 bg-white shadow-lg py-1 text-xs">
                                    @for ($hour = 8; $hour <= 16; $hour++)
                                        @php
                                            $slotStart = sprintf('%02d:00', $hour);
                                            $slotEnd   = sprintf('%02d:00', $hour + 1);
                                        @endphp
                                        <li role="option" data-slot="{{ $slotStart }}"
                                            class="pickup-slot px-3.5 py-2 font-medium text-ink cursor-pointer hover:bg-cream/60 transition-colors {{ old('pickup_time') === $slotStart ? 'is-selected' : '' }}">
                                            {{ $slotStart }} - {{ $slotEnd }} WITA
                                        </li>
                                    @endfor
                                </ul>
                            </div>
                            <span id="pickup-time-hint" class="text-[10px] text-ink-soft mt-1">Pilih tanggal terlebih dahulu untuk memeriksa kuota armada.</span>
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                    <div class="pt-4 border-t border-ink/5">
                        <button type="submit" id="btn_submit_setor" class="btn btn-sm w-full bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold h-11 shadow-sm transition-all">
                            Kirim Pengajuan Setor Sampah
                        </button>
                    </div>
            </form>
        </div>
    @endif
</div>

{{-- Flatpickr Kalender --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/id.js"></script>

<style>
    .flatpickr-day.selected, .flatpickr-day.selected:hover { background: #2E7D32 !important; border-color: #2E7D32 !important; }
    .flatpickr-day.today { border-color: #2E7D32 !important; }
    .flatpickr-day.today:hover { background: #2E7D32 !important; color: #fff !important; }
    .flatpickr-months .flatpickr-month, .flatpickr-current-month .flatpickr-monthDropdown-months, .flatpickr-weekday { color: #2E7D32 !important; }
    .flatpickr-day.pickup-date-full { color: #D84315 !important; text-decoration: line-through; cursor: not-allowed !important; pointer-events: auto !important; }
    #pickup-time-menu .pickup-slot.is-selected { background: #2E7D32; color: #fff; }
    #pickup-time-menu .pickup-slot.is-full { color: #D84315; text-decoration: line-through; background: #FBE9E7; cursor: not-allowed; }
</style>

<script>
    // 1. Live Estimasi Poin
    function updatePointRatePreview() {
        calculateEstimatedPoints();
    }

    function calculateEstimatedPoints() {
        const catSelect = document.getElementById('category_select');
        const weightInput = document.getElementById('estimated_weight_input');
        const ratePreview = document.getElementById('rate_preview_text');
        const pointsDisplay = document.getElementById('estimated_points_display');

        if (!catSelect || !weightInput) return;

        const selectedOpt = catSelect.options[catSelect.selectedIndex];
        const rate = selectedOpt ? parseFloat(selectedOpt.getAttribute('data-rate')) || 0 : 0;
        const weight = parseFloat(weightInput.value) || 0;
        const total = Math.round(weight * rate);

        if (selectedOpt && selectedOpt.value) {
            ratePreview.textContent = `${new Intl.NumberFormat('id-ID').format(rate)} Poin/kg`;
        } else {
            ratePreview.textContent = 'Pilih jenis sampah';
        }

        pointsDisplay.textContent = new Intl.NumberFormat('id-ID').format(total);
    }

    // 2. Logika Toggle Alamat Jemput (Warga Personal vs Mitra Bisnis PRO)
    const personalAddress = @json($user->address ?? '');
    const personalKec     = @json($user->kecamatan ?? '');
    const personalKel     = @json($user->kelurahan ?? '');

    // Default data alamat usaha mitra PRO
    const businessAddress = @json($user->address ?? '');
    const businessKec     = @json($user->kecamatan ?? '');
    const businessKel     = @json($user->kelurahan ?? '');

    function togglePickupOrigin(isBusiness) {
        const addrField = document.getElementById('input_pickup_address');
        const kecInput  = document.getElementById('input_kecamatan');
        const kelInput  = document.getElementById('input_kelurahan');
        const kecDisp   = document.getElementById('display_kecamatan');
        const kelDisp   = document.getElementById('display_kelurahan');
        const subTitle  = document.getElementById('pickup_origin_subtitle');

        if (isBusiness) {
            addrField.value = businessAddress;
            kecInput.value  = businessKec;
            kelInput.value  = businessKel;
            kecDisp.textContent = businessKec || '-';
            kelDisp.textContent = businessKel || '-';
            if (subTitle) subTitle.textContent = "Alamat titik jemput unit usaha (SulapaKarya PRO).";
        } else {
            addrField.value = personalAddress;
            kecInput.value  = personalKec;
            kelInput.value  = personalKel;
            kecDisp.textContent = personalKec || '-';
            kelDisp.textContent = personalKel || '-';
            if (subTitle) subTitle.textContent = "Alamat titik jemput rumah tinggal pribadi Anda.";
        }
    }

    // 3. Script Kompresi Gambar Otomatis
    document.addEventListener('DOMContentLoaded', function() {
        const photoInput = document.getElementById('photo_input');
        const photoStatus = document.getElementById('photo_status');

        if (photoInput) {
            photoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                // Batas ukuran kompresi: Kompres jika foto > 2MB
                if (file.size > 2 * 1024 * 1024) {
                    photoStatus.innerHTML = '<span class="text-amber-600">Mengompresi gambar dari kamera, mohon tunggu...</span>';
                    
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    
                    reader.onload = function(event) {
                        const img = new Image();
                        img.src = event.target.result;
                        
                        img.onload = function() {
                            const canvas = document.createElement('canvas');
                            const MAX_WIDTH = 1200; // Resolusi maksimal
                            const scaleSize = MAX_WIDTH / img.width;
                            canvas.width = MAX_WIDTH;
                            canvas.height = img.height * scaleSize;

                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                            // Proses Kompresi menjadi format JPEG dengan kualitas 75%
                            canvas.toBlob(function(blob) {
                                // Ganti nama file untuk memastikan formatnya sesuai hasil kompresi
                                const newFileName = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                                const compressedFile = new File([blob], newFileName, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(compressedFile);
                                e.target.files = dataTransfer.files; // Ganti File Input asli
                                
                                // Kalkulasi ukuran baru untuk notifikasi
                                const newSizeMb = (compressedFile.size / (1024 * 1024)).toFixed(2);
                                photoStatus.innerHTML = `<span class="text-forest">Gambar berhasil dikompres ke ${newSizeMb} MB.</span>`;
                            }, 'image/jpeg', 0.75);
                        }
                    }
                } else {
                    photoStatus.innerHTML = '<span class="text-forest">Ukuran foto optimal.</span>';
                }
            });
        }
    });

    // 4. Kontrol Kalender & Jam Penjemputan
    document.addEventListener('DOMContentLoaded', function () {
        var pickupDateInput = document.querySelector('input[name="pickup_date"]');
        if (pickupDateInput && window.flatpickr) {
            if (window.flatpickr.l10ns && window.flatpickr.l10ns.id) {
                flatpickr.localize(flatpickr.l10ns.id);
            }
            var minDate = new Date().fp_incr(1);
            var fullDates = @json($fullPickupDates ?? []);
            var fullDatesSet = fullDates.reduce(function (acc, d) { acc[d] = true; return acc; }, {});

            var fp = flatpickr(pickupDateInput, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                minDate: minDate,
                disable: fullDates,
                disableMobile: true,
                onDayCreate: function (dObj, dStr, fpInstance, dayElem) {
                    var ymd = fpInstance.formatDate(dayElem.dateObj, 'Y-m-d');
                    if (fullDatesSet[ymd]) {
                        dayElem.classList.add('pickup-date-full');
                        dayElem.setAttribute('title', 'Kuota pengantaran penuh pada hari ini.');
                    }
                },
            });

            if (fp.altInput) {
                fp.altInput.setAttribute('placeholder', 'Pilih tanggal');
            }
        }

        // Slot Waktu Dropdown
        var fullTimeSlots = @json($fullTimeSlots ?? []);
        var pickupDateEl  = document.querySelector('input[name="pickup_date"]');
        var timeInput     = document.getElementById('pickup_time_input');
        var timeTrigger   = document.getElementById('pickup-time-trigger');
        var timeMenu      = document.getElementById('pickup-time-menu');
        var timeLabel     = document.getElementById('pickup-time-label');
        var timeHint      = document.getElementById('pickup-time-hint');
        var slotItems     = Array.prototype.slice.call(document.querySelectorAll('#pickup-time-menu .pickup-slot'));

        function setTriggerLabel() {
            var selected = slotItems.filter(function (i) { return i.classList.contains('is-selected'); })[0];
            if (selected) {
                timeLabel.textContent = selected.textContent.trim();
                timeLabel.classList.remove('text-ink-soft');
                timeLabel.classList.add('text-ink');
            } else {
                timeLabel.textContent = 'Pilih slot jam';
                timeLabel.classList.add('text-ink-soft');
                timeLabel.classList.remove('text-ink');
            }
        }

        function toggleMenu() {
            timeMenu.classList.contains('hidden') ? timeMenu.classList.remove('hidden') : timeMenu.classList.add('hidden');
        }

        function refreshTimeSlots() {
            var date = pickupDateEl ? pickupDateEl.value : '';
            var fullList = (date && fullTimeSlots[date]) ? fullTimeSlots[date] : [];

            slotItems.forEach(function (item) {
                var slot = item.getAttribute('data-slot');
                var isFull = fullList.indexOf(slot) !== -1;

                if (isFull) {
                    item.classList.add('is-full');
                    item.setAttribute('aria-disabled', 'true');
                    if (item.classList.contains('is-selected')) {
                        item.classList.remove('is-selected');
                        if (timeInput) timeInput.value = '';
                    }
                } else {
                    item.classList.remove('is-full');
                    item.removeAttribute('aria-disabled');
                }
            });

            setTriggerLabel();

            if (timeHint) {
                timeHint.textContent = date
                    ? 'Klik untuk memilih jam yang tersedia.'
                    : 'Pilih tanggal terlebih dahulu untuk memeriksa kuota armada.';
            }
        }

        if (timeTrigger) {
            timeTrigger.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleMenu();
            });
        }

        slotItems.forEach(function (item) {
            item.addEventListener('click', function () {
                if (item.classList.contains('is-full')) return;
                slotItems.forEach(function (i) { i.classList.remove('is-selected'); });
                item.classList.add('is-selected');
                if (timeInput) timeInput.value = item.getAttribute('data-slot');
                setTriggerLabel();
                timeMenu.classList.add('hidden');
            });
        });

        document.addEventListener('click', function (e) {
            if (timeMenu && !timeMenu.classList.contains('hidden')) {
                var wrap = document.getElementById('pickup-time-dropdown');
                if (wrap && !wrap.contains(e.target)) timeMenu.classList.add('hidden');
            }
        });

        if (pickupDateEl) {
            pickupDateEl.addEventListener('change', refreshTimeSlots);
        }
        refreshTimeSlots();
        calculateEstimatedPoints();
    });

    document.getElementById('form_setor_sampah')?.addEventListener('submit', function (e) {
    const btn = document.getElementById('btn_submit_setor');
    if (btn) {
        btn.disabled = true;
        btn.innerText = 'Mengunggah data & foto...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
    }
});
</script>
@endsection