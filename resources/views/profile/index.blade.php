@extends('layouts.dashboard', ['title' => 'Profil Saya — SulapaKarya'])

@section('dashboard-content')
@php
    $user = auth()->user() ?? \App\Models\User::find(session('user_id'));
@endphp

<div class="max-w-4xl mx-auto space-y-6 text-left">
    
    <!-- Header Profil -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-ink/5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">Pengaturan Profil & Titik Penjemputan</h1>
            <p class="text-xs text-ink-soft mt-0.5">Kelola identitas pribadi dan tetapkan alamat penjemputan sampah terpilah Anda di Kota Makassar.</p>
        </div>
        <span class="badge bg-forest/10 text-forest border border-forest/20 text-[10px] font-semibold px-2.5 py-1 rounded-xl">
            Area Layanan: Kota Makassar
        </span>
    </div>

    <!-- Alert Notifikasi -->
    @if($errors->any())
        <div class="alert alert-error bg-terracotta/10 border-terracotta/20 text-terracotta rounded-2xl text-xs font-semibold p-4 shadow-none">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success bg-forest/10 border-forest/20 text-forest rounded-2xl text-xs font-semibold p-4 shadow-none">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        {{-- @method('PUT') --}}

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- KOLOM 1: KARTU FOTO PROFIL & RINGKASAN SALDO -->
            <div class="space-y-5">
                <div class="bg-white rounded-3xl p-6 border border-ink/5 shadow-none text-center space-y-4">
                    
                    <!-- Avatar Preview -->
                    <div class="relative w-24 h-24 mx-auto">
                        <div id="avatar-preview" class="w-full h-full rounded-full bg-forest text-white font-black text-2xl flex items-center justify-center overflow-hidden ring-4 ring-forest/10 shadow-sm">
                            @if($user && $user->foto_profil)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($user->foto_profil) }}?v={{ time() }}" alt="Profil" class="w-full h-full object-cover" />
                            @else
                                {{ strtoupper(substr($user->name ?? session('name', 'U'), 0, 1)) }}
                            @endif
                        </div>
                        <label for="foto_profil" class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-white border border-ink/10 shadow-md flex items-center justify-center cursor-pointer hover:bg-cream transition-colors text-ink">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        </label>
                        <input type="file" id="foto_profil" name="foto_profil" accept="image/*" class="hidden" onchange="previewImage(event)">
                    </div>

                    <div>
                        <h3 class="font-bold text-base text-ink">{{ $user->name ?? 'Pengguna' }}</h3>
                        <p class="text-xs text-ink-soft font-mono mt-0.5">{{ $user->email ?? '-' }}</p>
                        <span class="badge bg-cream border border-ink/10 text-ink-soft text-[10px] font-bold uppercase mt-2 px-2.5 py-1 rounded-md">
                            Peran: {{ $user->role ?? 'Warga' }}
                        </span>
                    </div>

                    <!-- Saldo Poin Live -->
                    <div class="p-3.5 bg-cream/30 border border-ink/5 rounded-2xl text-left flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-ink-soft block">Tabungan Poin</span>
                            <span class="text-xs text-ink-soft font-medium">Bisa ditarik tunai</span>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-black font-mono text-forest">{{ number_format($user->points_balance ?? $user->points ?? 0) }}</span>
                            <span class="text-[10px] font-bold text-forest block">Poin</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- KOLOM 2 & 3: FORM IDENTITAS & ALAMAT PENJEMPUTAN MAKASSAR -->
            <div class="lg:col-span-2 space-y-5">
                
                <!-- Section 1: Informasi Dasar -->
                <div class="bg-white rounded-3xl p-6 border border-ink/5 shadow-none space-y-4">
                    <div class="pb-3 border-b border-ink/5">
                        <h2 class="font-bold text-sm text-ink">Informasi Pribadi</h2>
                        <p class="text-[11px] text-ink-soft">Data dasar akun Anda untuk konfirmasi transaksi.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1.5">Nama Lengkap <span class="text-terracotta">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="input input-bordered w-full rounded-xl text-xs bg-cream/20 focus:outline-none focus:border-forest font-semibold text-ink">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1.5">Nomor Telepon / WhatsApp <span class="text-terracotta">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required placeholder="08xxxxxxxxxx"
                                class="input input-bordered w-full rounded-xl text-xs bg-cream/20 focus:outline-none focus:border-forest font-mono text-ink">
                            <span class="text-[10px] text-ink-soft/70 block mt-1">Digunakan kurir untuk konfirmasi kedatangan.</span>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-ink mb-1.5">Alamat Email</label>
                            <input type="email" value="{{ $user->email }}" disabled
                                class="input input-bordered w-full rounded-xl text-xs bg-sand/30 font-mono text-ink-soft cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Alamat Penjemputan Berjenjang (Makassar Area) -->
                <div class="bg-white rounded-3xl p-6 border border-ink/5 shadow-none space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-ink/5">
                        <div>
                            <h2 class="font-bold text-sm text-ink">Titik Alamat Penjemputan Utama</h2>
                            <p class="text-[11px] text-ink-soft">Alamat ini akan otomatis menjadi lokasi default saat Anda mengajukan penjemputan sampah.</p>
                        </div>
                        <span class="text-[10px] font-mono text-forest bg-forest/10 px-2 py-0.5 rounded font-bold">Makassar</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- 1. Dropdown Kecamatan -->
                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1.5">Kecamatan <span class="text-terracotta">*</span></label>
                            <select name="kecamatan" id="select_kecamatan" required onchange="onKecamatanChange()"
                                class="select select-bordered select-sm w-full rounded-xl text-xs bg-cream/20 font-semibold focus:outline-none focus:border-forest text-ink">
                                <option value="" disabled {{ empty($user->kecamatan) ? 'selected' : '' }}>-- Pilih Kecamatan --</option>
                            </select>
                        </div>

                        <!-- 2. Dropdown Kelurahan (Otomatis menyesuaikan) -->
                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1.5">Kelurahan <span class="text-terracotta">*</span></label>
                            <select name="kelurahan" id="select_kelurahan" required
                                class="select select-bordered select-sm w-full rounded-xl text-xs bg-cream/20 font-semibold focus:outline-none focus:border-forest text-ink">
                                <option value="" disabled selected>-- Pilih Kecamatan Terlebih Dahulu --</option>
                            </select>
                        </div>

                        <!-- 3. Alamat Lengkap (Jalan, No Rumah, RT/RW, Patokan) -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-ink mb-1.5">Alamat Lengkap & Patokan Lokasi <span class="text-terracotta">*</span></label>
                            <textarea name="address" rows="3" required placeholder="Contoh: Jl. Perintis Kemerdekaan KM 10 No. 45, RT 02 / RW 01 (Pagar hitam samping warkop)..."
                                class="textarea textarea-bordered w-full rounded-xl text-xs bg-cream/20 focus:outline-none focus:border-forest leading-relaxed text-ink">{{ old('address', $user->address) }}</textarea>
                            <span class="text-[10px] text-ink-soft/70 block mt-1">Tuliskan nomor rumah dan patokan khusus agar kurir cepat menemukan titik lokasi penjemputan.</span>
                        </div>

                        <!-- 4. Kode Pos (Opsional) -->
                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1.5">Kode Pos (Opsional)</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" placeholder="90xxx" maxlength="6"
                                class="input input-bordered w-full rounded-xl text-xs bg-cream/20 focus:outline-none focus:border-forest font-mono text-ink">
                        </div>

                    </div>
                </div>

                <!-- Tombol Simpan -->
                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn btn-sm bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold px-6 h-11 shadow-sm transition-all">
                        Simpan Perubahan Profil
                    </button>
                </div>

            </div>

        </div>
    </form>

</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT HIERARKI KECAMATAN & KELURAHAN KOTA MAKASSAR -->
<!-- ========================================================================= -->
<script>
    // Database 15 Kecamatan & 153 Kelurahan Kota Makassar
    const makassarData = {
        "Biringkanaya": [
            "Bakung", "Berua", "Bulurokeng", "Daya", "Katimbang", 
            "Laikang", "Paccerakkang", "Pai", "Sudiang", "Sudiang Raya", "Untia"
        ],
        "Bontoala": [
            "Baraya", "Bontoala", "Bontoala Parang", "Bontoala Tua", "Bunga Ejaya", 
            "Gaddong", "Layang", "Malimongan Baru", "Parang Layang", "Timungan Lompoa", 
            "Tompo Balang", "Wajo Baru"
        ],
        "Kepulauan Sangkarrang": [
            "Barrang Caddi", "Barrang Lompo", "Kodingareng"
        ],
        "Makassar": [
            "Bara-Baraya", "Bara-Baraya Selatan", "Bara-Baraya Timur", "Bara-Baraya Utara", 
            "Barana", "Lariang Bangi", "Maccini", "Maccini Gusung", "Maccini Parang", 
            "Maradekaya", "Maradekaya Selatan", "Maradekaya Utara", "Maricaya", "Maricaya Baru"
        ],
        "Mamajang": [
            "Baji Mappakasunggu", "Bonto Biraeng", "Bonto Lebang", "Karang Anyar", 
            "Labuang Baji", "Mamajang Dalam", "Mamajang Luar", "Mandala", "Maricaya Selatan", 
            "Pa'batang", "Parang", "Sambung Jawa", "Tamparang Keke"
        ],
        "Manggala": [
            "Antang", "Bangkala", "Batua", "Biring Romang", "Bitowa", 
            "Borong", "Manggala", "Tamangapa"
        ],
        "Mariso": [
            "Bontorannu", "Kampung Buyang", "Kunjung Mae", "Lette", "Mario", 
            "Mariso", "Mattoangin", "Panambungan", "Tamarunang"
        ],
        "Panakkukang": [
            "Karampuang", "Karuwisi", "Karuwisi Utara", "Masale", "Pampang", 
            "Panaikang", "Pandang", "Paropo", "Sinrijala", "Tamamaung", "Tello Baru"
        ],
        "Rappocini": [
            "Balla Parang", "Banta-Bantaeng", "Bonto Makkio", "Bua Kana", 
            "Gunung Sari", "Karunrung", "Kassi-Kassi", "Mapala", "Minasa Upa", 
            "Rappocini", "Tidung"
        ],
        "Tallo": [
            "Buloa", "Bunga Eja Beru", "Kaluku Bodoa", "Kalukuang", "La'latang", 
            "Lakkang", "Lembo", "Pannampu", "Rappojawa", "Rappokalling", 
            "Suangga", "Tallo", "Tammua", "Ujung Pandang Baru", "Wala-Walaya"
        ],
        "Tamalanrea": [
            "Bira", "Buntusu", "Kapasa", "Kapasa Raya", "Parang Loe", 
            "Tamalanrea", "Tamalanrea Indah", "Tamalanrea Jaya"
        ],
        "Tamalate": [
            "Balang Baru", "Barombong", "Bongaya", "Bonto Duri", "Jongaya", 
            "Maccini Sombala", "Mangasa", "Mannuruki", "Pa'baeng-Baeng", 
            "Parang Tambung", "Tanjung Merdeka"
        ],
        "Ujung Pandang": [
            "Baru", "Bulogading", "Lae-Lae", "Lajangiru", "Losari", 
            "Maloku", "Mangkura", "Pisang Selatan", "Pisang Utara", "Sawerigading"
        ],
        "Ujung Tanah": [
            "Camba Berua", "Cambaya", "Gusung", "Pattingalloang", "Pattingalloang Baru", 
            "Tabaringan", "Tamalabba", "Totaka", "Ujung Tanah"
        ],
        "Wajo": [
            "Butung", "Ende", "Malimongan", "Malimongan Tua", "Mampu", 
            "Melayu", "Melayu Baru", "Pattunuang"
        ]
    };

    const currentKecamatan = "{{ old('kecamatan', $user->kecamatan ?? '') }}";
    const currentKelurahan = "{{ old('kelurahan', $user->kelurahan ?? '') }}";

    document.addEventListener("DOMContentLoaded", function() {
        const kecSelect = document.getElementById('select_kecamatan');

        // Isi opsi Kecamatan
        Object.keys(makassarData).forEach(function(kec) {
            const option = document.createElement('option');
            option.value = kec;
            option.textContent = "Kec. " + kec;
            if (kec.toLowerCase() === currentKecamatan.toLowerCase()) {
                option.selected = true;
            }
            kecSelect.appendChild(option);
        });

        // Trigger pengisian kelurahan jika kecamatan sudah ada nilai awal
        if (currentKecamatan) {
            populateKelurahan(currentKecamatan, currentKelurahan);
        }
    });

    function onKecamatanChange() {
        const kecSelect = document.getElementById('select_kecamatan');
        const selectedKec = kecSelect.value;
        populateKelurahan(selectedKec, '');
    }

    function populateKelurahan(kecamatan, selectedKelurahan) {
        const kelSelect = document.getElementById('select_kelurahan');
        kelSelect.innerHTML = '<option value="" disabled selected>-- Pilih Kelurahan --</option>';

        if (makassarData[kecamatan]) {
            makassarData[kecamatan].forEach(function(kel) {
                const option = document.createElement('option');
                option.value = kel;
                option.textContent = "Kel. " + kel;
                if (kel.toLowerCase() === selectedKelurahan.toLowerCase()) {
                    option.selected = true;
                }
                kelSelect.appendChild(option);
            });
        }
    }

    function previewImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatar-preview');
                preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection