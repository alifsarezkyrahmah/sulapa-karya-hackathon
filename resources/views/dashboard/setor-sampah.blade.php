@extends('layouts.dashboard', ['title' => 'Setor Sampah — SulapaKarya Macca'])

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
                            <label class="label py-1"><span class="label-text font-bold text-xs text-ink-soft">Rincian Barang (Opsional)</span></label>
                            <input type="text" name="sub_category" value="{{ old('sub_category') }}" placeholder="Contoh: Botol Aqua Kosong, Kardus Mie" class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5">
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-bold text-xs text-ink-soft">Perkiraan Berat <span class="text-terracotta">*</span></span></label>
                            <div class="relative">
                                <input type="number" step="0.1" name="estimated_weight" value="{{ old('estimated_weight') }}" placeholder="0.0" required class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20 pr-12 @error('estimated_weight') border-terracotta @enderror">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-ink-soft">Kg</span>
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
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-bold text-xs text-ink-soft">Rencana Tanggal Jemput</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-soft">📅</span>
                                <input type="date" name="pickup_date" min="{{ date('Y-m-d') }}" value="{{ old('pickup_date') }}" class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20 pl-11">
                            </div>
                        </div>

                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-bold text-xs text-ink-soft">Waktu Standby (Opsional)</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-soft">⏰</span>
                                <input type="time" name="pickup_time" value="{{ old('pickup_time') }}" class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20 pl-11">
                            </div>
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
@endsection