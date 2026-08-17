@extends('layouts.dashboard', ['title' => 'Setor Sampah — SulapaKarya'])

@section('dashboard-content')
<div class="max-w-4xl mx-auto space-y-6 animate-fadeIn py-8 px-4 sm:px-6">
    
    <div class="text-left bg-gradient-to-r from-forest to-forest-dark p-8 rounded-[2rem] text-white shadow-lg shadow-forest/20 relative overflow-hidden">
        <div class="absolute inset-0 dot-grid text-white/[0.05] pointer-events-none"></div>
        <div class="relative z-10">
            <h1 class="font-display font-extrabold text-3xl tracking-tight">Setor Sampah Digital</h1>
            <p class="text-sm text-forest-light font-medium mt-2 max-w-xl">Pilah sampahmu dari rumah, tentukan titik jemput, dan dapatkan Koin Kriya otomatis dari tim Penjemput SulapaKarya Makassar.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-error bg-terracotta/10 border-terracotta/20 text-terracotta rounded-2xl text-xs font-bold text-left p-4 shadow-sm">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success bg-forest/10 border-forest/20 text-forest rounded-2xl text-xs font-bold text-left p-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- VALIDASI: Periksa apakah user sudah mengisi alamat di profilnya --}}
    @if(blank($user->address ?? $user->alamat ?? ''))
        {{-- Tampilan jika Alamat Profil Masih Kosong --}}
        <div class="bg-white border border-ink/5 rounded-[2rem] p-8 text-center shadow-sm space-y-4 py-12">
            <div class="w-16 h-16 bg-terracotta/10 text-terracotta rounded-full flex items-center justify-center mx-auto text-2xl">
                📍
            </div>
            <h3 class="font-display font-extrabold text-xl text-ink">Alamat Profil Belum Dilengkapi</h3>
            <p class="text-sm text-ink-soft max-w-md mx-auto leading-relaxed">
                Untuk menggunakan layanan Setor Sampah, Anda wajib mengisi alamat utama pada akun Anda terlebih dahulu sebagai titik dasar penjemputan armada kami.
            </p>
            <div class="pt-2">
                {{-- Sesuaikan nama route profile Anda di bawah ini --}}
                <a href="/profile" class="btn bg-forest hover:bg-forest-dark text-white border-none rounded-xl font-extrabold text-sm px-6 normal-case transition-all shadow-md shadow-forest/20">
                    Lengkapi Alamat Profil Sekarang
                </a>
            </div>
        </div>
    @else
        {{-- Tampilan Form jika Alamat Profil Sudah Ada --}}
        <div class="bg-white border border-ink/5 rounded-[2rem] p-6 sm:p-8 shadow-sm">
            <form action="{{ route('setor-sampah.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Hidden input untuk otomatisasi target imbalan menjadi poin --}}
                <input type="hidden" name="reward_type" value="points">

                <div class="space-y-4">
                    <h2 class="font-display font-extrabold text-lg text-ink border-b border-ink/5 pb-2 flex items-center gap-2">
                        <span>📦</span> Informasi Sampah
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-bold text-xs text-ink-soft">Kategori Utama <span class="text-terracotta">*</span></span></label>
                            <select name="category" required class="select select-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20 font-bold @error('category') border-terracotta @enderror">
                                <option value="" disabled {{ old('category') == null ? 'selected' : '' }}>-- Pilih Jenis Sampah --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category') == $cat->id ? 'selected' : '' }}>{{ $cat->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-bold text-xs text-ink-soft">Jenis Barang <span class="text-terracotta">*</span></span></label>
                            <select name="sub_category" id="sub_category_select" required class="select select-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20 font-bold">
                                <option value="" disabled selected>-- Pilih kategori dulu --</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5">
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-bold text-xs text-ink-soft">Perkiraan Berat <span class="text-terracotta">*</span></span></label>
                            <div class="relative">
                                <input type="number" step="0.1" name="estimated_weight" id="estimated_weight_input" value="{{ old('estimated_weight') }}" placeholder="0.0" required class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20 pr-12 @error('estimated_weight') border-terracotta @enderror">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-ink-soft">Kg</span>
                            </div>
                        </div>
                    </div>

                    <div id="point-estimation" class="hidden bg-forest/[0.04] border border-forest/10 p-4 rounded-2xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-ink-soft font-extrabold uppercase tracking-wider block">Estimasi Poin yang Didapat</span>
                                <span class="text-xs text-ink-soft font-medium" id="point-rate-info"></span>
                            </div>
                            <div class="text-right">
                                <span id="estimated-points" class="text-2xl font-black font-mono text-forest">0</span>
                                <span class="text-xs font-bold text-forest block">Poin</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-control mt-2">
                        <label class="label py-1"><span class="label-text font-bold text-xs text-ink-soft">Foto Bukti Fisik Sampah <span class="text-terracotta">*</span></span></label>
                        <input type="file" name="photo" accept="image/*" capture="environment" required class="file-input file-input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20 @error('photo') border-terracotta @enderror" />
                        <label class="label"><span class="label-text-alt text-[10px] text-ink-soft font-medium">Batas max 3MB. Anda bisa jepret dari kamera HP atau pilih dari galeri.</span></label>
                    </div>
                </div>

                <div class="space-y-4 pt-6 border-t border-ink/5 mt-6">
                    <h2 class="font-display font-extrabold text-lg text-ink border-b border-ink/5 pb-2 flex items-center gap-2">
                        <span>🛵</span> Detail Penjemputan Armada
                    </h2>
                    
                    <div class="form-control bg-cream/30 p-4 rounded-2xl border border-ink/5">
                        <label class="label py-1 flex justify-between items-center">
                            <span class="label-text font-bold text-xs text-ink">Titik Lokasi Penjemputan <span class="text-terracotta">*</span></span>
                            <span class="text-[9px] bg-maritime/10 text-maritime px-2 py-0.5 rounded font-extrabold uppercase tracking-wider">Otomatis dari Profil</span>
                        </label>
                        <textarea name="pickup_address" rows="3" placeholder="Masukkan alamat lengkap RT/RW, Patokan gedung, dll." required class="textarea textarea-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-white shadow-inner mt-1 @error('pickup_address') border-terracotta @enderror">{{ old('pickup_address', $user->address ?? $user->alamat ?? '') }}</textarea>
                        <label class="label"><span class="label-text-alt text-[10px] text-ink-soft">Silakan edit kotak di atas jika Anda ingin sampah dijemput di lokasi yang berbeda dari rumah Anda.</span></label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-2">
                        @php
                            // Fallback tanggal server bila JS mati; nilai sebenarnya diatur ulang oleh flatpickr (tanggal browser real-time).
                            $minPickupDate = date('Y-m-d', strtotime('+1 day'));
                        @endphp
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-bold text-xs text-ink-soft">Rencana Tanggal Jemput</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-soft pointer-events-none z-10">📅</span>
                                <input type="date" name="pickup_date" min="{{ $minPickupDate }}" value="{{ old('pickup_date') }}" placeholder="dd/mm/yyyy" class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20 pl-11 cursor-pointer">
                            </div>
                            <label class="label"><span id="pickup-date-hint" class="label-text-alt text-[10px] text-ink-soft">Penjemputan paling cepat besok ({{ \Carbon\Carbon::parse($minPickupDate)->translatedFormat('d M Y') }}).</span></label>
                        </div>

                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-bold text-xs text-ink-soft">Waktu Penjemputan</span></label>
                            <input type="hidden" name="pickup_time" id="pickup_time_input" value="{{ old('pickup_time') }}">

                            <div id="pickup-time-dropdown" class="relative">
                                {{-- Tombol pemicu (tampilan seperti select) --}}
                                <button type="button" id="pickup-time-trigger" aria-haspopup="listbox" aria-expanded="false"
                                    class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20 pl-11 pr-9 flex items-center cursor-pointer text-left">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-soft pointer-events-none z-10">⏰</span>
                                    <span id="pickup-time-label" class="truncate text-ink-soft">Pilih jam penjemputan</span>
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-ink-soft pointer-events-none">▾</span>
                                </button>

                                {{-- Panel opsi (muncul saat tombol ditekan) --}}
                                <ul id="pickup-time-menu" role="listbox" class="hidden absolute z-30 mt-1 w-full max-h-60 overflow-auto rounded-xl border border-ink/10 bg-white shadow-lg py-1">
                                    @for ($hour = 8; $hour <= 16; $hour++)
                                        @php
                                            $slotStart = sprintf('%02d:00', $hour);
                                            $slotEnd   = sprintf('%02d:00', $hour + 1);
                                        @endphp
                                        <li role="option" data-slot="{{ $slotStart }}"
                                            class="pickup-slot px-4 py-2.5 text-sm font-semibold text-ink cursor-pointer hover:bg-cream/60 transition-colors {{ old('pickup_time') === $slotStart ? 'is-selected' : '' }}">
                                            {{ $slotStart }} - {{ $slotEnd }}
                                        </li>
                                    @endfor
                                </ul>
                            </div>
                            <label class="label"><span id="pickup-time-hint" class="label-text-alt text-[10px] text-ink-soft">Pilih tanggal jemput terlebih dahulu, lalu pilih jamnya.</span></label>
                        </div>
                    </div>
                </div>

                <div class="pt-6 mt-4 border-t border-ink/5">
                    <button type="submit" class="btn w-full bg-forest text-white border-none rounded-xl font-extrabold text-base normal-case shadow-lg shadow-forest/30 hover:bg-forest-dark active:scale-[0.98] transition-all h-14">
                        Kirim Pengajuan Setor Sampah
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

{{-- Flatpickr: kalender pilih tanggal penjemputan --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/id.js"></script>
<style>
    /* Warna aksen kalender agar selaras dengan tema forest */
    .flatpickr-day.selected,
    .flatpickr-day.selected:hover { background: #2f6b3c; border-color: #2f6b3c; }
    .flatpickr-day.today { border-color: #2f6b3c; }
    .flatpickr-day.today:hover { background: #2f6b3c; color: #fff; }
    .flatpickr-months .flatpickr-month,
    .flatpickr-current-month .flatpickr-monthDropdown-months,
    .flatpickr-weekday { color: #2f6b3c; }
    /* Tanggal yang kuotanya penuh: beri tanda merah samar + kursor "tidak boleh" */
    .flatpickr-day.pickup-date-full {
        color: #c0392b !important;
        text-decoration: line-through;
        cursor: not-allowed !important;
        pointer-events: auto !important; /* tetap bisa di-hover agar tooltip muncul */
    }
    /* Opsi slot waktu penjemputan (dropdown) */
    #pickup-time-menu .pickup-slot.is-selected {
        background: #2f6b3c;
        color: #fff;
    }
    #pickup-time-menu .pickup-slot.is-selected:hover { background: #2f6b3c; }
    #pickup-time-menu .pickup-slot.is-full {
        color: #c0392b;
        text-decoration: line-through;
        background: #f8e6e3;
        cursor: not-allowed;
        opacity: .9;
    }
    #pickup-time-menu .pickup-slot.is-full:hover { background: #f8e6e3; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var pickupDateInput = document.querySelector('input[name="pickup_date"]');
        if (pickupDateInput && window.flatpickr) {
            if (window.flatpickr.l10ns && window.flatpickr.l10ns.id) {
                flatpickr.localize(flatpickr.l10ns.id);
            }
            var minDate = new Date().fp_incr(1); // paling cepat besok (berdasar tanggal browser real-time)
            var fullDates = @json($fullPickupDates ?? []); // tanggal yang kuotanya sudah penuh (10 pengangkutan)
            var fullDatesSet = fullDates.reduce(function (acc, d) { acc[d] = true; return acc; }, {});

            var fp = flatpickr(pickupDateInput, {
                dateFormat: 'Y-m-d',       // nilai yang dikirim ke server (cocok utk kolom date)
                altInput: true,            // input tampilan yang mudah dibaca user
                altFormat: 'd F Y',
                minDate: minDate,          // blokir hari ini & sebelumnya
                disable: fullDates,        // blokir tanggal yang sudah penuh
                disableMobile: true,       // paksa pakai kalender flatpickr, bukan native
                onDayCreate: function (dObj, dStr, fpInstance, dayElem) {
                    // Beri keterangan saat kursor diarahkan ke tanggal yang penuh.
                    var ymd = fpInstance.formatDate(dayElem.dateObj, 'Y-m-d');
                    if (fullDatesSet[ymd]) {
                        dayElem.classList.add('pickup-date-full');
                        dayElem.setAttribute('title', 'Pengantaran penuh, tolong pilih hari lain.');
                    }
                },
            });

            // Tampilkan placeholder samar pada input tampilan flatpickr.
            if (fp.altInput) {
                fp.altInput.setAttribute('placeholder', 'dd/mm/yyyy');
            }

            // Sinkronkan teks bantuan di bawah dengan tanggal minimal kalender (hindari selisih zona waktu server).
            var hint = document.getElementById('pickup-date-hint');
            if (hint) {
                var fmt = fp.formatDate(minDate, 'd F Y');
                hint.textContent = 'Penjemputan paling cepat besok (' + fmt + ').';
            }
        }

        // ===== Kuota slot waktu penjemputan (maksimal 2 user per slot per hari) =====
        var fullTimeSlots = @json($fullTimeSlots ?? []); // { 'Y-m-d': ['08:00', '13:00', ...] }
        var pickupDateEl  = document.querySelector('input[name="pickup_date"]');
        var timeInput     = document.getElementById('pickup_time_input');
        var timeTrigger   = document.getElementById('pickup-time-trigger');
        var timeMenu      = document.getElementById('pickup-time-menu');
        var timeLabel     = document.getElementById('pickup-time-label');
        var timeHint      = document.getElementById('pickup-time-hint');
        var slotItems     = Array.prototype.slice.call(document.querySelectorAll('#pickup-time-menu .pickup-slot'));

        function currentPickupDate() {
            return pickupDateEl ? pickupDateEl.value : '';
        }

        function isSlotFull(item) {
            return item.classList.contains('is-full');
        }

        function setTriggerLabel() {
            var selected = slotItems.filter(function (i) { return i.classList.contains('is-selected'); })[0];
            if (selected) {
                timeLabel.textContent = selected.textContent.trim();
                timeLabel.classList.remove('text-ink-soft');
                timeLabel.classList.add('text-ink');
            } else {
                timeLabel.textContent = 'Pilih jam penjemputan';
                timeLabel.classList.add('text-ink-soft');
                timeLabel.classList.remove('text-ink');
            }
        }

        function openMenu() {
            timeMenu.classList.remove('hidden');
            timeTrigger.setAttribute('aria-expanded', 'true');
        }
        function closeMenu() {
            timeMenu.classList.add('hidden');
            timeTrigger.setAttribute('aria-expanded', 'false');
        }
        function toggleMenu() {
            timeMenu.classList.contains('hidden') ? openMenu() : closeMenu();
        }

        function refreshTimeSlots() {
            var date = currentPickupDate();
            var fullList = (date && fullTimeSlots[date]) ? fullTimeSlots[date] : [];

            slotItems.forEach(function (item) {
                var slot = item.getAttribute('data-slot');
                var isFull = fullList.indexOf(slot) !== -1;

                if (isFull) {
                    item.classList.add('is-full');
                    item.setAttribute('aria-disabled', 'true');
                    item.setAttribute('title', 'Silahkan pilih waktu penjemputan lain');
                    // Batalkan pilihan bila slot yang sebelumnya dipilih ternyata sudah penuh.
                    if (item.classList.contains('is-selected')) {
                        item.classList.remove('is-selected');
                        if (timeInput) timeInput.value = '';
                    }
                } else {
                    item.classList.remove('is-full');
                    item.removeAttribute('aria-disabled');
                    item.removeAttribute('title');
                }
            });

            setTriggerLabel();

            if (timeHint) {
                timeHint.textContent = date
                    ? 'Klik untuk memilih jam. Slot yang penuh tidak bisa dipilih.'
                    : 'Pilih tanggal jemput terlebih dahulu, lalu pilih jamnya.';
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
                if (isSlotFull(item)) return; // slot penuh: tidak bisa dipilih
                slotItems.forEach(function (i) { i.classList.remove('is-selected'); });
                item.classList.add('is-selected');
                if (timeInput) timeInput.value = item.getAttribute('data-slot');
                setTriggerLabel();
                closeMenu();
            });
        });

        // Tutup dropdown saat klik di luar.
        document.addEventListener('click', function (e) {
            if (timeMenu && !timeMenu.classList.contains('hidden')) {
                var wrap = document.getElementById('pickup-time-dropdown');
                if (wrap && !wrap.contains(e.target)) closeMenu();
            }
        });

        // Perbarui slot setiap kali tanggal berubah (flatpickr & native sama-sama memicu 'change').
        if (pickupDateEl) {
            pickupDateEl.addEventListener('change', refreshTimeSlots);
        }
        refreshTimeSlots(); // status awal (termasuk setelah validasi gagal / old input)

        // ===== Sub-kategori dinamis & estimasi poin =====
        var subCatData = @json($subCategories ?? []);
        var categorySelect = document.querySelector('select[name="category"]');
        var subCategorySelect = document.getElementById('sub_category_select');
        var weightInput = document.getElementById('estimated_weight_input');
        var pointEstBox = document.getElementById('point-estimation');
        var pointsDisplay = document.getElementById('estimated-points');
        var rateInfo = document.getElementById('point-rate-info');
        var oldSubCategory = @json(old('sub_category', ''));

        function populateSubCategories() {
            var cat = categorySelect ? categorySelect.value : '';
            if (!subCategorySelect) return;
            subCategorySelect.innerHTML = '<option value="" disabled selected>-- Pilih jenis barang --</option>';
            if (cat && subCatData[cat]) {
                Object.keys(subCatData[cat]).forEach(function(name) {
                    var opt = document.createElement('option');
                    opt.value = name;
                    opt.textContent = name;
                    if (name === oldSubCategory) opt.selected = true;
                    subCategorySelect.appendChild(opt);
                });
            }
            updatePointEstimation();
        }

        function updatePointEstimation() {
            var cat = categorySelect ? categorySelect.value : '';
            var sub = subCategorySelect ? subCategorySelect.value : '';
            var weight = weightInput ? parseFloat(weightInput.value) || 0 : 0;
            var rate = 0;

            if (cat && sub && subCatData[cat] && subCatData[cat][sub]) {
                rate = subCatData[cat][sub];
            }

            if (rate > 0 && weight > 0) {
                pointEstBox.classList.remove('hidden');
                pointsDisplay.textContent = new Intl.NumberFormat('id-ID').format(Math.round(weight * rate));
                rateInfo.textContent = sub + ': ' + new Intl.NumberFormat('id-ID').format(rate) + ' poin/kg';
            } else if (rate > 0) {
                pointEstBox.classList.remove('hidden');
                pointsDisplay.textContent = '0';
                rateInfo.textContent = sub + ': ' + new Intl.NumberFormat('id-ID').format(rate) + ' poin/kg';
            } else {
                pointEstBox.classList.add('hidden');
            }
        }

        if (categorySelect) {
            categorySelect.addEventListener('change', populateSubCategories);
            if (categorySelect.value) populateSubCategories();
        }
        if (subCategorySelect) subCategorySelect.addEventListener('change', updatePointEstimation);
        if (weightInput) weightInput.addEventListener('input', updatePointEstimation);
    });
</script>
@endsection