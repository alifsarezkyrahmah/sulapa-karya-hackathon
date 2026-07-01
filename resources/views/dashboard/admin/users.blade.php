@extends('layouts.dashboard', ['title' => 'Kelola Pengguna — SulapaKarya Macca'])

@section('dashboard-content')
<div class="space-y-6 animate-fadeIn">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="text-left">
            <h1 class="font-display font-extrabold text-2xl text-ink tracking-tight">Manajemen Akun Pengguna</h1>
            <p class="text-xs text-ink-soft/80 font-medium mt-1">Kelola tingkatan hak akses gerakan sosial SulapaKarya Macca daerah Makassar.</p>
        </div>
        <button onclick="add_user_modal.showModal()" class="btn btn-sm bg-forest border-none text-white hover:bg-forest-dark rounded-xl normal-case shadow-md font-bold px-4 gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Pengguna
        </button>
    </div>

    @if($errors->any())
        <div class="alert alert-error bg-terracotta/10 border-terracotta/20 text-terracotta rounded-2xl text-xs font-bold text-left p-4">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-ink/5 rounded-[1.5rem] p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto rounded-xl border border-ink/5">
            <table class="table w-full text-sm">
                <thead>
                    <tr class="border-b border-ink/5 text-ink/70 font-bold uppercase tracking-wider text-xs bg-cream/60">
                        <th class="py-3.5 pl-5">Nama & Kontak</th>
                        <th class="py-3.5">ID Identitas</th>
                        <th class="py-3.5">Hak Akses (Role)</th>
                        <th class="py-3.5 pr-5 text-center">Aksi Operasional</th>
                    </tr>
                </thead>
                <tbody class="font-medium text-ink/90">
                    @forelse($users as $u)
                        <tr class="hover:bg-cream/10 border-b border-ink/5 transition-colors">
                            <td class="py-4 pl-5">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder shrink-0">
                                        <div class="bg-forest/10 text-forest font-bold rounded-full w-9 h-9 flex items-center justify-center">
                                            <span>{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                        </div>
                                    </div>
                                    <div class="text-left max-w-[200px] truncate">
                                        <p class="font-bold text-ink truncate leading-tight">{{ $u->name }}</p>
                                        <p class="text-[11px] text-ink-soft font-mono mt-0.5 truncate">{{ $u->email }}</p>
                                        <p class="text-[10px] text-gray-400 font-medium mt-0.5 truncate">📞 {{ $u->phone ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 font-mono text-xs text-ink-soft">
                                <span class="badge badge-sm bg-cream border-ink/5 font-bold tracking-wide">
                                    ID-{{ substr($u->supabase_id ?? $u->id, 0, 8) }}...
                                </span>
                            </td>

                            <td class="py-4">
                                @if($u->role === 'admin')
                                    <span class="badge bg-terracotta/10 border-none text-terracotta text-xs font-bold px-2.5 py-2 rounded-lg">ADMIN</span>
                                @elseif($u->role === 'penjemput')
                                    <span class="badge bg-maritime/10 border-none text-maritime text-xs font-bold px-2.5 py-2 rounded-lg">PENJEMPUT</span>
                                @elseif($u->role === 'pengrajin')
                                    <span class="badge bg-purple-100 border-none text-purple-700 text-xs font-bold px-2.5 py-2 rounded-lg">PENGRAJIN</span>
                                @else
                                    <span class="badge bg-forest/10 border-none text-forest text-xs font-bold px-2.5 py-2 rounded-lg">WARGA</span>
                                @endif
                            </td>

                            <td class="py-4 pr-5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="edit_modal_{{ $u->id }}.showModal()" class="btn btn-xs btn-square bg-maritime/10 border-none text-maritime hover:bg-maritime hover:text-white rounded-lg transition-all" title="Ubah Data Profil">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>

                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }} secara permanen dari sistem?')" class="m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-square bg-terracotta/10 border-none text-terracotta hover:bg-terracotta hover:text-white rounded-lg transition-all" title="Hapus Akun">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <dialog id="edit_modal_{{ $u->id }}" class="modal modal-bottom sm:modal-middle">
                            <div class="modal-box bg-white max-w-md rounded-[2rem] border border-ink/5 p-6 text-left relative">
                                <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft">✕</button></form>
                                <h3 class="font-display font-extrabold text-xl text-ink">Ubah Data Pengguna</h3>
                                <p class="text-xs text-ink-soft font-semibold mt-1">Perbarui biodata profil atau hak akses peran akun.</p>
                                
                                <form action="{{ route('admin.users.update', $u->id) }}" method="POST" class="space-y-4 mt-4">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text font-bold text-xs">Nama Lengkap</span></label>
                                        <input type="text" name="name" value="{{ $u->name }}" required class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text font-bold text-xs">Alamat Email</span></label>
                                        <input type="email" name="email" value="{{ $u->email }}" required class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text font-bold text-xs">Nomor Telepon / HP</span></label>
                                        <input type="text" name="phone" value="{{ $u->phone }}" required class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text font-bold text-xs">Peran Tingkat Sistem</span></label>
                                        <select name="role" class="select select-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20 font-bold">
                                            <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>Warga (User)</option>
                                            <option value="penjemput" {{ $u->role === 'penjemput' ? 'selected' : '' }}>Penjemput (Kurir)</option>
                                            <option value="pengrajin" {{ $u->role === 'pengrajin' ? 'selected' : '' }}>Pengrajin Kriya</option>
                                            <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin Utama</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn w-full bg-forest text-white border-none rounded-xl font-bold normal-case mt-2 shadow-md shadow-forest/10">Simpan Perubahan</button>
                                </form>
                            </div>
                            <form method="dialog" class="modal-backdrop bg-ink/30 backdrop-blur-sm"><button>close</button></form>
                        </dialog>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-ink-soft/60 font-medium text-xs">
                                📭 Belum ada data anggota Komunitas Makassar lainnya yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <dialog id="add_user_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white max-w-md rounded-[2rem] border border-ink/5 p-6 text-left relative">
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft">✕</button></form>
            <h3 class="font-display font-extrabold text-xl text-ink">Registrasi Pengguna Baru</h3>
            <p class="text-xs text-ink-soft font-semibold mt-1">Daftarkan anggota komunitas baru dengan syarat validasi akun resmi.</p>
            
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3.5 mt-4">
                @csrf
                
                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-bold text-xs">Nama Lengkap</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Alifsa Rezky" required 
                        class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">
                </div>

                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-bold text-xs">Alamat Email Resmi</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="lifsa@example.com" required 
                        class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">
                </div>

                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-bold text-xs">Nomor Telepon / HP Aktif</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 08123456789" required 
                        class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">
                </div>

                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-bold text-xs">Kata Sandi (Min. 6 Karakter)</span></label>
                    <input type="password" name="password" placeholder="••••••••" required 
                        class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">
                </div>

                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-bold text-xs">Ulangi Kata Sandi</span></label>
                    <input type="password" name="password_confirmation" placeholder="••••••••" required 
                        class="input input-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20">
                </div>

                <div class="form-control">
                    <label class="label py-1"><span class="label-text font-bold text-xs">Hak Akses Sistem</span></label>
                    <select name="role" class="select select-bordered w-full rounded-xl text-sm focus:outline-none focus:border-forest bg-cream/20 font-bold">
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Warga / User Biasa</option>
                        <option value="penjemput" {{ old('role') == 'penjemput' ? 'selected' : '' }}>Penjemput (Kurir Armada)</option>
                        <option value="pengrajin" {{ old('role') == 'pengrajin' ? 'selected' : '' }}>Pengrajin Kriya Mitra</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin Utama</option>
                    </select>
                </div>

                <button type="submit" class="btn w-full bg-forest text-white border-none rounded-xl font-bold normal-case mt-3 shadow-md shadow-forest/10 active:scale-95 transition-all">
                    Daftarkan & Aktifkan Akun
                </button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-ink/30 backdrop-blur-sm"><button>close</button></form>
    </dialog>

</div>
@endsection