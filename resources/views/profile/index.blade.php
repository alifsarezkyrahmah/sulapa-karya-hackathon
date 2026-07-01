@extends('layouts.dashboard', ['title' => 'Profil Saya — SulapaKarya Macca'])

@section('dashboard-content')
<!-- State Global Alpine.js untuk Mengontrol Aktivasi Tombol Berdasarkan Perubahan Input -->
<div x-data="{ 
        hasChanges: false, 
        isAvatarDeleted: false,
        hasPwdChanges: false
     }" 
     class="space-y-8 animate-fadeIn max-w-6xl mx-auto">

    <!-- ============ HEADER ATAS ============ -->
    <div class="text-left">
        <h1 class="font-display font-extrabold text-2xl text-ink">Manajemen Profil Akun</h1>
        <p class="text-xs text-ink-soft font-medium mt-1">Kelola data logistik penjemputan sampah Anda dan pantau performa poin digital Anda di Makassar.</p>
    </div>

    <!-- TAMPILAN GRID SPLIT UTAMA -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <!-- ================= SISI KIRI: AVATAR INTERAKTIF & STATS KARTU ================= -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- 1. KARTU AVATAR INTERAKTIF (MANAJEMEN FOTO LANGSUNG DI SINI) -->
            <div class="bg-white border border-ink/5 rounded-[1.5rem] p-6 text-center shadow-sm relative overflow-hidden">
                <div class="absolute inset-0 dot-grid text-ink/[0.01] pointer-events-none"></div>
                
                <!-- Lingkaran Avatar dengan Efek Hover Klik Uploader -->
                <div class="relative w-28 h-28 mx-auto mb-3 group cursor-pointer" 
                     title="Klik untuk ganti foto profil"
                     @click="document.getElementById('foto_profil').click()">
                    
                    <div class="w-full h-full rounded-full overflow-hidden ring-4 ring-forest/10 shadow-md bg-white relative">
                        
                        
                        <!-- Kondisi A: Tampilkan Gambar jika Ada di Database dan Tidak Memilih Hapus -->
                        <template x-if="!isAvatarDeleted && '{{ $user->foto_profil }}'">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($user->foto_profil) }}?v={{ time() }}" 
                                 alt="Foto {{ $user->name }}" 
                                 class="w-full h-full object-cover">
                        </template>

                        <!-- Kondisi B: Fallback ke Inisial Nama jika Foto Kosong atau Diklik Hapus -->
                        <template x-if="isAvatarDeleted || !'{{ $user->foto_profil }}'">
                            <div class="w-full h-full bg-gradient-to-tr from-forest to-forest-dark text-white flex items-center justify-center font-display font-bold text-3xl">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </template>

                        <!-- Lapisan Overlay Kamera Saat Hover (UX Modern) -->
                        <div class="absolute inset-0 bg-ink/50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col items-center justify-center text-white text-[10px] font-bold gap-1">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            Ganti Foto
                        </div>
                    </div>
                </div>

                <!-- Input File Tersembunyi (Tautkan ke Form Informasi Personal di Kanan via Atribut form) -->
                <input id="foto_profil" name="foto_profil" type="file" accept="image/*" class="hidden" 
                       form="profile-form"
                       @change="hasChanges = true; isAvatarDeleted = false" />

                <!-- Penanda Hidden Token untuk Hapus Foto ke Controller -->
                <input type="hidden" name="delete_avatar" :value="isAvatarDeleted ? '1' : '0'" form="profile-form">

                <!-- Opsi Tombol Hapus Foto: Hanya muncul jika di DB user memang punya foto profil -->
                @if($user->foto_profil)
                    <button type="button" x-show="!isAvatarDeleted" @click="isAvatarDeleted = true; hasChanges = true"
                            class="text-[11px] text-terracotta hover:text-terracotta-dark font-bold transition-colors focus:outline-none">
                        Hapus Foto Profil
                    </button>
                @endif

                <h3 class="font-display font-bold text-ink text-base mt-3">{{ $user->name }}</h3>
                <span class="badge bg-forest/10 border-none text-forest font-extrabold text-[10px] tracking-wider uppercase px-3 py-2.5 mt-1 rounded-lg">
                    Peran: {{ strtoupper($user->role) }}
                </span>
                
                @error('foto_profil')
                    <span class="text-xs text-terracotta font-semibold mt-2 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- 2. KARTU DOMPET POIN & PROFIT (SINKRON DATABASE) -->
            <div class="bg-gradient-to-br from-white via-white to-cream/40 border border-ink/5 rounded-[1.5rem] p-5 shadow-sm space-y-4">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-ink-soft/60 block border-b border-ink/5 pb-2 text-left">Akumulasi Dompet</span>
                
                <div class="flex items-center justify-between text-left">
                    <div>
                        <span class="text-[11px] text-ink-soft font-medium">Total Tabungan Poin</span>
                        <p class="text-xl font-extrabold text-forest-dark mt-0.5">{{ number_format($user->points_balance) }} <span class="text-xs font-bold text-ink-soft">Poin</span></p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-forest-light text-forest flex items-center justify-center font-bold text-sm">P</div>
                </div>

                <div class="flex items-center justify-between text-left pt-2 border-t border-ink/5">
                    <div>
                        <span class="text-[11px] text-ink-soft font-medium">Uang Tunai Diterima</span>
                        <p class="text-xl font-extrabold text-terracotta-dark mt-0.5"><span class="text-xs font-bold text-ink-soft">Rp</span> {{ number_format($user->cash_received_total) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-terracotta-light text-terracotta flex items-center justify-center font-bold text-xs">Rp</div>
                </div>
            </div>


        </div>

        <!-- ================= SISI KANAN: FORM EDIT PROFIL & PASSWORD ================= -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- KARTU 1: FORM INFORMASI PERSONAL -->
            <div class="bg-white border border-ink/5 rounded-[1.5rem] p-6 sm:p-8 shadow-sm text-left">
                <div class="mb-6 border-b border-ink/5 pb-3">
                    <h2 class="font-display font-bold text-lg text-ink">Informasi Personal</h2>
                    <p class="text-xs text-ink-soft font-medium">Ubah data biodata Anda di bawah ini secara langsung untuk mengaktifkan tombol simpan.</p>
                </div>

                <form id="profile-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- Input: Nama Lengkap -->
                    <div class="form-control w-full">
                        <label for="name" class="label py-1"><span class="label-text font-bold text-xs text-ink/70">Nama Lengkap</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                            @input="hasChanges = true"
                            class="input input-bordered w-full rounded-xl text-sm bg-white border-ink/10 text-ink focus:border-forest focus:outline-none h-11 shadow-sm @error('name') border-terracotta @enderror" />
                        @error('name')
                            <span class="text-xs text-terracotta font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Input: Email -->
                        <div class="form-control w-full">
                            <label for="email" class="label py-1"><span class="label-text font-bold text-xs text-ink/70">Alamat Email</span></label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                                @input="hasChanges = true"
                                class="input input-bordered w-full rounded-xl text-sm bg-white border-ink/10 text-ink focus:border-forest focus:outline-none h-11 shadow-sm @error('email') border-terracotta @enderror" />
                            @error('email')
                                <span class="text-xs text-terracotta font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Input: Nomor Telepon/WhatsApp -->
                        <div class="form-control w-full">
                            <label for="phone" class="label py-1"><span class="label-text font-bold text-xs text-ink/70">Nomor WhatsApp Aktif</span></label>
                            <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" required
                                @input="hasChanges = true"
                                class="input input-bordered w-full rounded-xl text-sm bg-white border-ink/10 text-ink focus:border-forest focus:outline-none h-11 shadow-sm font-mono @error('phone') border-terracotta @enderror" />
                            @error('phone')
                                <span class="text-xs text-terracotta font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Input: Alamat Lengkap -->
                    <div class="form-control w-full">
                        <label for="address" class="label py-1"><span class="label-text font-bold text-xs text-ink/70">Alamat Lengkap Rumah (Sesuai KK/Domisili Makassar)</span></label>
                        <textarea id="address" name="address" rows="4" required 
                            @input="hasChanges = true"
                            placeholder="Tuliskan alamat lengkap Anda, RT/RW, nama jalan, nomor rumah, kelurahan, kecamatan, dan patokan spesifik..."
                            class="textarea textarea-bordered w-full rounded-xl text-sm bg-white border-ink/10 text-ink focus:border-forest focus:outline-none leading-relaxed shadow-sm @error('address') border-terracotta @enderror">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <span class="text-xs text-terracotta font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Spanduk Informasi Sukses Simpan Biodata -->
                    @if(session('success') || session('status') === 'profile-updated')
                        <div class="alert bg-forest/10 border-none text-forest font-bold rounded-xl text-xs py-3 shadow-inner">
                            ✓ Perubahan data personal Anda berhasil disimpan!
                        </div>
                    @endif

                    <!-- PANEL TOMBOL FORM BIODATA -->
                    <div class="pt-3 border-t border-ink/5 flex justify-end gap-3">
                        <button type="button" onclick="window.location.reload()" :disabled="!hasChanges"
                                class="btn bg-ink/5 hover:bg-ink/10 text-ink-soft border-none rounded-xl font-bold text-xs normal-case px-5 h-11 min-h-0 transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                            Batal
                        </button>
                        
                        <button type="submit" :disabled="!hasChanges"
                                class="btn bg-gradient-to-r from-forest to-forest-dark hover:from-forest-dark hover:to-forest text-white border-none rounded-xl font-bold text-xs normal-case px-6 shadow-md shadow-forest/10 h-11 min-h-0 transition-all active:scale-95 disabled:from-slate-200 disabled:to-slate-300 disabled:text-slate-400 disabled:opacity-100 disabled:shadow-none disabled:cursor-not-allowed">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- KARTU 2: FORM GANTI KATA SANDI MANDIRI -->
            <div class="bg-white border border-ink/5 rounded-[1.5rem] p-6 sm:p-8 shadow-sm text-left">
                <div class="mb-5 border-b border-ink/5 pb-3">
                    <h2 class="font-display font-bold text-lg text-ink">Perbarui Kata Sandi</h2>
                    <p class="text-xs text-ink-soft font-medium">Pastikan akun Anda menggunakan kata sandi yang panjang dan aman demi keselamatan data.</p>
                </div>

                <form id="password-form" action="{{ route('password.update') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Input: Password Saat Ini -->
                    <div class="form-control w-full">
                        <label for="current_password" class="label py-1"><span class="label-text font-bold text-xs text-ink/70">Kata Sandi Saat Ini</span></label>
                        <input id="current_password" name="current_password" type="password" required
                            @input="hasPwdChanges = true"
                            class="input input-bordered w-full rounded-xl text-sm bg-white border-ink/10 text-ink focus:border-forest focus:outline-none h-11 shadow-sm @error('current_password') border-terracotta @enderror" />
                        @error('current_password')
                            <span class="text-xs text-terracotta font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Input: Password Baru -->
                        <div class="form-control w-full">
                            <label for="password" class="label py-1"><span class="label-text font-bold text-xs text-ink/70">Kata Sandi Baru</span></label>
                            <input id="password" name="password" type="password" required
                                @input="hasPwdChanges = true"
                                class="input input-bordered w-full rounded-xl text-sm bg-white border-ink/10 text-ink focus:border-forest focus:outline-none h-11 shadow-sm @error('password') border-terracotta @enderror" />
                            @error('password')
                                <span class="text-xs text-terracotta font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Input: Konfirmasi Password Baru -->
                        <div class="form-control w-full">
                            <label for="password_confirmation" class="label py-1"><span class="label-text font-bold text-xs text-ink/70">Konfirmasi Kata Sandi Baru</span></label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                @input="hasPwdChanges = true"
                                class="input input-bordered w-full rounded-xl text-sm bg-white border-ink/10 text-ink focus:border-forest focus:outline-none h-11 shadow-sm" />
                        </div>
                    </div>

                    <!-- Spanduk Informasi Sukses Ganti Password -->
                    @if(session('success_password'))
                        <div class="alert bg-forest/10 border-none text-forest font-bold rounded-xl text-xs py-3 shadow-inner">
                            ✓ {{ session('success_password') }}
                        </div>
                    @endif

                    <!-- PANEL TOMBOL FORM KATA SANDI -->
                    <div class="pt-3 border-t border-ink/5 flex justify-end">
                        <button type="submit" :disabled="!hasPwdChanges"
                                class="btn bg-gradient-to-r from-forest to-forest-dark hover:from-forest-dark hover:to-forest text-white border-none rounded-xl font-bold text-xs normal-case px-6 shadow-md shadow-forest/10 h-11 min-h-0 transition-all active:scale-95 disabled:from-slate-200 disabled:to-slate-300 disabled:text-slate-400 disabled:opacity-100 disabled:shadow-none disabled:cursor-not-allowed">
                            Perbarui Sandi
                        </button>
                    </div>
                </form>
            </div>

            <!-- KARTU 3: ZONA BAHAYA (HAPUS AKUN PERMANEN) -->
            <div class="bg-white border border-ink/5 border-t-4 border-t-terracotta rounded-[1.5rem] p-6 shadow-sm text-left">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="font-display font-bold text-sm text-terracotta">Zona Bahaya: Hapus Akun Permanen</h3>
                        <p class="text-[11px] text-ink-soft font-medium mt-0.5">Setelah akun dihapus, semua akumulasi poin, riwayat kriya, dan data logistik Anda akan dimusnahkan permanen.</p>
                    </div>
                    <button type="button" @click="$refs.deleteModal.showModal()"
                            class="btn btn-sm bg-terracotta hover:bg-terracotta-dark text-white border-none rounded-xl font-bold normal-case text-xs px-4 h-9 min-h-0 shrink-0 shadow-sm transition-all active:scale-95">
                        Hapus Akun
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= MODAL DIALOG POP-UP HAPUS AKUN ================= -->
    <dialog x-ref="deleteModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white rounded-2xl border border-ink/5 p-6 text-left">
            <h3 class="font-display font-bold text-lg text-ink">Apakah Anda yakin ingin menghapus akun?</h3>
            <p class="py-3 text-xs text-ink-soft leading-relaxed">
                Tindakan ini bersifat permanen. Silakan ketik kata sandi Anda untuk memverifikasi penghapusan keselamatan data dari sistem SulapaKarya Macca.
            </p>

            <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4 mt-2">
                @csrf
                @method('delete')

                <div class="form-control w-full">
                    <label class="label py-1">
                        <span class="label-text font-bold text-xs text-ink/70">Masukkan Kata Sandi Konfirmasi</span>
                    </label>
                    <input name="password" type="password" placeholder="Ketik kata sandi akun Anda..." required
                        class="input input-bordered w-full rounded-xl text-sm bg-cream/[0.15] border-ink/10 focus:border-terracotta focus:outline-none h-11 shadow-sm" />
                </div>

                <div class="modal-action gap-3 pt-3 border-t border-ink/5">
                    <button type="button" @click="$refs.deleteModal.close()" class="btn bg-ink/5 hover:bg-ink/10 text-ink-soft border-none rounded-xl font-bold text-xs normal-case px-5 h-11 min-h-0">
                        Batal
                    </button>
                    <button type="submit" class="btn bg-terracotta hover:bg-terracotta-dark text-white border-none rounded-xl font-bold text-xs normal-case px-5 h-11 min-h-0 shadow-md shadow-terracotta/10">
                        Ya, Hapus Permanen
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-ink/40 backdrop-blur-sm">
            <button>close</button>
        </form>
    </dialog>

</div>
@endsection