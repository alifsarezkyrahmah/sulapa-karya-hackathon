@extends('layouts.dashboard', ['title' => 'Kelola Pengguna — SulapaKarya'])

@section('dashboard-content')
<div class="space-y-6 text-left">
    
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-ink/5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">Manajemen Pengguna & Mitra Bisnis</h1>
            <p class="text-xs text-ink-soft mt-0.5">Kelola akun pengguna, pantau data unit usaha terdaftar, dan verifikasi kemitraan PRO.</p>
        </div>
        <button type="button" onclick="document.getElementById('add_user_modal').showModal()" 
            class="btn btn-sm bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold px-4 h-10 shadow-sm transition-all w-full sm:w-auto">
            <svg class="w-4 h-4 mr-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Pengguna
        </button>
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

    <!-- ========================================================================= -->
    <!-- NAVIGASI TAB MENU -->
    <!-- ========================================================================= -->
    <div class="flex items-center gap-2 border-b border-ink/10 pb-2 overflow-x-auto">
        <button type="button" onclick="switchAdminTab('users')" id="tab_btn_users" 
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all bg-forest text-white shadow-xs">
            Semua Pengguna ({{ $users->count() }})
        </button>
        <button type="button" onclick="switchAdminTab('partners')" id="tab_btn_partners" 
            class="px-4 py-2 rounded-xl text-xs font-semibold transition-all text-ink-soft hover:bg-cream/60">
            Mitra Bisnis PRO ({{ isset($allBusinessPartners) ? $allBusinessPartners->count() : 0 }})
        </button>
        <button type="button" onclick="switchAdminTab('pending')" id="tab_btn_pending" 
            class="px-4 py-2 rounded-xl text-xs font-semibold transition-all text-ink-soft hover:bg-cream/60 flex items-center gap-2">
            <span>Antrean Ajuan</span>
            @if(isset($pendingBusinesses) && $pendingBusinesses->count() > 0)
                <span class="badge badge-xs bg-amber-400 text-amber-950 font-bold border-none px-1.5 py-0.5 font-mono">
                    {{ $pendingBusinesses->count() }}
                </span>
            @endif
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: SEMUA PENGGUNA (TABEL USER REGULER)                                -->
    <!-- ========================================================================= -->
    <div id="tab_content_users" class="space-y-4">
        <div class="hidden md:block bg-white border border-ink/5 rounded-2xl p-6 shadow-none">
            <div class="overflow-x-auto">
                <table class="table w-full text-xs">
                    <thead>
                        <tr class="bg-cream/40 text-ink-soft border-b border-ink/5 font-semibold text-[10px] uppercase">
                            <th class="py-3 pl-3">Pengguna & Kontak</th>
                            <th class="py-3">Status Kemitraan</th>
                            <th class="py-3">Identitas Sistem</th>
                            <th class="py-3">Saldo Poin</th>
                            <th class="py-3 text-center min-w-[110px]">Hak Akses</th>
                            <th class="py-3 pr-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/5 font-medium">
                        @forelse($users as $u)
                            <tr class="hover:bg-cream/20 transition-colors">
                                <td class="py-3.5 pl-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-forest/10 text-forest font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 max-w-[220px]">
                                            <span class="font-bold text-ink block text-xs truncate">{{ $u->name }}</span>
                                            <span class="text-[10px] text-ink-soft font-mono block truncate">{{ $u->email }}</span>
                                            <span class="text-[10px] text-ink-soft/70 block font-mono">{{ $u->phone ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5">
                                    @if($u->business_status === 'approved')
                                        <span class="inline-flex items-center gap-1 bg-forest/15 text-forest border border-forest/20 text-[10px] font-bold px-2 py-0.5 rounded-md font-mono">
                                            PRO &bull; {{ $u->business_name }}
                                        </span>
                                    @elseif($u->business_status === 'verified_unpaid')
                                        <span class="inline-flex items-center bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-2 py-0.5 rounded-md font-mono">
                                            Menunggu Bayar
                                        </span>
                                    @elseif($u->business_status === 'pending')
                                        <span class="inline-flex items-center bg-amber-100 text-amber-800 border border-amber-300 text-[10px] font-bold px-2 py-0.5 rounded-md font-mono">
                                            Pending
                                        </span>
                                    @else
                                        <span class="text-[11px] text-ink-soft/60">Reguler</span>
                                    @endif
                                </td>
                                <td class="py-3.5 font-mono text-[11px] text-ink-soft">
                                    <span class="badge bg-cream border border-ink/10 text-ink text-[10px] font-mono px-2 py-0.5 rounded">
                                        {{ substr($u->supabase_id ?? (string)$u->id, 0, 8) }}...
                                    </span>
                                </td>
                                <td class="py-3.5 font-mono">
                                    <span class="font-bold text-forest text-xs">{{ number_format($u->points_balance ?? 0) }}</span>
                                    <span class="text-[10px] text-ink-soft font-sans">Poin</span>
                                </td>
                                <td class="py-3.5 text-center min-w-[110px]">
                                    @if($u->role === 'admin')
                                        <span class="inline-flex items-center justify-center whitespace-nowrap bg-terracotta/10 text-terracotta border border-terracotta/20 text-[10px] font-bold px-2.5 py-1 rounded-md">ADMIN</span>
                                    @elseif($u->role === 'penjemput')
                                        <span class="inline-flex items-center justify-center whitespace-nowrap bg-maritime/10 text-maritime border border-maritime/20 text-[10px] font-bold px-2.5 py-1 rounded-md">PENJEMPUT</span>
                                    @elseif($u->role === 'pengrajin' || $u->role === 'artisan')
                                        <span class="inline-flex items-center justify-center whitespace-nowrap bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-bold px-2.5 py-1 rounded-md">PENGRAJIN</span>
                                    @else
                                        <span class="inline-flex items-center justify-center whitespace-nowrap bg-forest/10 text-forest border border-forest/20 text-[10px] font-bold px-2.5 py-1 rounded-md">WARGA</span>
                                    @endif
                                </td>
                                <td class="py-3.5 pr-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" onclick="document.getElementById('edit_modal_{{ $u->id }}').showModal()"
                                            class="btn btn-xs bg-forest hover:bg-forest-dark text-white border-none rounded-lg text-[10px] font-semibold px-2.5 shadow-none whitespace-nowrap">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Hapus akun {{ $u->name }}?')" class="inline m-0 p-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs bg-white hover:bg-terracotta/10 text-terracotta border border-terracotta/30 rounded-lg text-[10px] font-semibold px-2 shadow-none">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-ink-soft/60 text-xs font-medium">Belum ada pengguna lainnya.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: DIREKTORI MITRA BISNIS PRO                                         -->
    <!-- ========================================================================= -->
    <div id="tab_content_partners" class="hidden space-y-4">
        <div class="bg-white border border-ink/10 rounded-2xl p-5 sm:p-6 shadow-none">
            <div class="overflow-x-auto">
                <table class="table w-full text-xs">
                    <thead>
                        <tr class="bg-cream/40 text-ink-soft border-b border-ink/5 font-semibold text-[10px] uppercase">
                            <th class="py-2.5 pl-3">Nama Unit Usaha</th>
                            <th class="py-2.5">Kategori & Estimasi</th>
                            <th class="py-2.5">Akun Pemilik (User)</th>
                            <th class="py-2.5">Wilayah & Titik Jemput</th>
                            <th class="py-2.5">Jadwal Operasional</th>
                            <th class="py-2.5 text-center">Status PRO</th>
                            <th class="py-2.5 pr-3 text-right">Foto Tempat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/5 font-medium">
                        @forelse($allBusinessPartners ?? [] as $biz)
                            <tr class="hover:bg-cream/20 transition-colors">
                                <td class="py-3.5 pl-3">
                                    <span class="font-bold text-ink block text-xs">{{ $biz->business_name }}</span>
                                    <span class="text-[10px] text-ink-soft font-mono">{{ $biz->updated_at ? $biz->updated_at->format('d/m/Y') : '-' }}</span>
                                </td>
                                <td class="py-3.5">
                                    <span class="text-ink font-semibold block">{{ $biz->business_type }}</span>
                                    <span class="text-[10px] text-forest font-mono font-bold">{{ $biz->waste_estimate_kg }} Kg / pekan</span>
                                </td>
                                <td class="py-3.5">
                                    <span class="font-semibold text-ink block">{{ $biz->name }}</span>
                                    <span class="text-[10px] text-ink-soft font-mono block">{{ $biz->phone ?? '-' }}</span>
                                </td>
                                <td class="py-3.5 max-w-[200px]">
                                    <span class="font-semibold text-ink block truncate">Kec. {{ $biz->kecamatan ?? '-' }}, Kel. {{ $biz->kelurahan ?? '-' }}</span>
                                    <span class="text-[11px] text-ink-soft block truncate" title="{{ $biz->address }}">{{ $biz->address ?? '-' }}</span>
                                </td>
                                <td class="py-3.5">
                                    @if($biz->businessSchedule)
                                        <div class="space-y-0.5">
                                            <span class="font-bold text-ink text-[11px] block">
                                                {{ implode(', ', $biz->businessSchedule->pickup_days ?? []) }}
                                            </span>
                                            <span class="text-[10px] text-ink-soft font-mono">
                                                Pukul {{ $biz->businessSchedule->pickup_time }} WITA
                                                @if(!$biz->businessSchedule->is_active)
                                                    <span class="text-terracotta">(Jeda)</span>
                                                @endif
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-ink-soft/60 italic">Belum disetel</span>
                                    @endif
                                </td>
                                <td class="py-3.5 text-center">
                                    @if($biz->business_status === 'approved')
                                        <span class="inline-flex items-center bg-forest/15 text-forest border border-forest/20 text-[10px] font-bold px-2 py-0.5 rounded-md font-mono">
                                            PRO AKTIF
                                        </span>
                                    @elseif($biz->business_status === 'verified_unpaid')
                                        <span class="inline-flex items-center bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-bold px-2 py-0.5 rounded-md font-mono">
                                            MENUNGGU BAYAR
                                        </span>
                                    @elseif($biz->business_status === 'rejected')
                                        <span class="inline-flex items-center bg-red-50 text-terracotta border border-red-200 text-[10px] font-bold px-2 py-0.5 rounded-md font-mono">
                                            DITOLAK
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 pr-3 text-right">
                                    @if($biz->business_photo_path)
                                        <a href="{{ asset('storage/' . $biz->business_photo_path) }}" target="_blank" class="text-forest hover:underline text-xs font-semibold">
                                            Lihat Foto ↗
                                        </a>
                                    @else
                                        <span class="text-ink-soft/40">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-ink-soft/60 text-xs font-medium">Belum ada data mitra bisnis yang aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: ANTREAN VERIFIKASI MITRA BISNIS                                   -->
    <!-- ========================================================================= -->
    <div id="tab_content_pending" class="hidden space-y-4">
        <div class="bg-white border border-ink/10 rounded-2xl p-5 sm:p-6 shadow-none">
            @if(isset($pendingBusinesses) && $pendingBusinesses->count() > 0)
                <div class="overflow-x-auto">
                    <table class="table w-full text-xs">
                        <thead>
                            <tr class="bg-cream/40 text-ink-soft border-b border-ink/5 font-semibold text-[10px] uppercase">
                                <th class="py-2.5 pl-3">Unit Usaha & Pemilik</th>
                                <th class="py-2.5">Kategori</th>
                                <th class="py-2.5">Alamat Titik Jemput</th>
                                <th class="py-2.5">Estimasi Limbah</th>
                                <th class="py-2.5">Foto Tempat</th>
                                <th class="py-2.5 pr-3 text-right">Keputusan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-ink/5 font-medium">
                            @foreach($pendingBusinesses as $b)
                                <tr class="hover:bg-cream/20 transition-colors">
                                    <td class="py-3 pl-3">
                                        <span class="font-bold text-ink block">{{ $b->business_name }}</span>
                                        <span class="text-[10px] text-ink-soft font-mono">{{ $b->name }} &bull; {{ $b->phone ?? '-' }}</span>
                                    </td>
                                    <td class="py-3">{{ $b->business_type }}</td>
                                    <td class="py-3 max-w-[220px]">
                                        <span class="font-semibold text-ink block">Kec. {{ $b->kecamatan ?? '-' }}, Kel. {{ $b->kelurahan ?? '-' }}</span>
                                        <span class="text-[11px] text-ink-soft truncate block" title="{{ $b->address }}">{{ $b->address }}</span>
                                        @if($b->business_notes)
                                            <span class="text-[10px] text-forest block truncate" title="{{ $b->business_notes }}">Catatan: {{ $b->business_notes }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 font-mono font-bold text-forest">{{ $b->waste_estimate_kg }} Kg / pekan</td>
                                    <td class="py-3">
                                        @if($b->business_photo_path)
                                            <a href="{{ asset('storage/' . $b->business_photo_path) }}" target="_blank" class="text-forest hover:underline text-xs font-semibold">
                                                Lihat Foto ↗
                                            </a>
                                        @else
                                            <span class="text-ink-soft/40">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <form action="{{ route('bisnis.admin.verify', $b->id) }}" method="POST" class="m-0">
                                                @csrf
                                                <input type="hidden" name="decision" value="approved">
                                                <button type="submit" onclick="return confirm('Setujui kemitraan {{ $b->business_name }}?')" class="btn btn-xs bg-forest hover:bg-forest-dark text-white border-none rounded-lg px-3 shadow-none">
                                                    Setujui
                                                </button>
                                            </form>
                                            <button type="button" onclick="document.getElementById('reject_modal_{{ $b->id }}').showModal()" class="btn btn-xs bg-white hover:bg-terracotta/10 text-terracotta border border-terracotta/30 rounded-lg px-2.5 shadow-none">
                                                Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center text-ink-soft/60 text-xs font-medium border border-dashed border-ink/10 rounded-xl">
                    Tidak ada antrean ajuan mitra bisnis yang sedang menunggu keputusan.
                </div>
            @endif
        </div>
    </div>

</div>

<!-- Modal Alasan Penolakan -->
@if(isset($pendingBusinesses))
    @foreach($pendingBusinesses as $b)
        <dialog id="reject_modal_{{ $b->id }}" class="modal modal-middle">
            <div class="modal-box w-11/12 max-w-sm bg-white rounded-2xl border border-ink/10 p-5 text-left shadow-2xl">
                <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-3 top-3 text-ink-soft">✕</button></form>
                <h3 class="font-bold text-sm text-ink">Tolak Pengajuan Mitra</h3>
                <p class="text-xs text-ink-soft mt-0.5">{{ $b->business_name }}</p>

                <form action="{{ route('bisnis.admin.verify', $b->id) }}" method="POST" class="space-y-3 mt-3">
                    @csrf
                    <input type="hidden" name="decision" value="rejected">
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1">Alasan Penolakan</label>
                        <textarea name="business_admin_notes" rows="3" required placeholder="Tuliskan catatan evaluasi rute atau berkas..."
                            class="textarea textarea-bordered w-full rounded-xl text-xs bg-cream/20 text-ink focus:outline-none focus:border-terracotta leading-relaxed"></textarea>
                    </div>
                    <button type="submit" class="btn btn-sm w-full bg-terracotta hover:bg-red-700 text-white border-none rounded-xl text-xs font-bold h-9 shadow-none">
                        Kirim Penolakan
                    </button>
                </form>
            </div>
            <form method="dialog" class="modal-backdrop bg-ink/30"><button>close</button></form>
        </dialog>
    @endforeach
@endif

<!-- Modal Tambah Pengguna Baru -->
<dialog id="add_user_modal" class="modal modal-middle">
    <div class="modal-box w-11/12 max-w-lg bg-white rounded-3xl border border-ink/10 p-5 sm:p-7 text-left shadow-2xl overflow-y-auto max-h-[88vh] my-auto">
        <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft hover:bg-cream">✕</button></form>
        <div class="border-b border-ink/5 pb-3 mb-4">
            <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-forest bg-forest/10 px-2 py-0.5 rounded">Akun Baru</span>
            <h3 class="font-bold text-base sm:text-lg text-ink mt-1">Registrasi Pengguna Baru</h3>
            <p class="text-xs text-ink-soft mt-0.5">Daftarkan akun warga, kurir penjemput, atau mitra kriya baru.</p>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-ink mb-1">Nama Lengkap <span class="text-terracotta">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Rahmat Hidayat" required class="input input-bordered input-sm w-full rounded-xl text-xs bg-cream/20 font-semibold text-ink focus:outline-none focus:border-forest">
            </div>
            <div>
                <label class="block text-xs font-semibold text-ink mb-1">Alamat Email <span class="text-terracotta">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="warga@sulapakarya.id" required class="input input-bordered input-sm w-full rounded-xl text-xs bg-cream/20 font-mono text-ink focus:outline-none focus:border-forest">
            </div>
            <div>
                <label class="block text-xs font-semibold text-ink mb-1">Nomor Telepon / HP <span class="text-terracotta">*</span></label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required class="input input-bordered input-sm w-full rounded-xl text-xs bg-cream/20 font-mono text-ink focus:outline-none focus:border-forest">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">Kata Sandi <span class="text-terracotta">*</span></label>
                    <input type="password" name="password" placeholder="••••••••" required class="input input-bordered input-sm w-full rounded-xl text-xs bg-cream/20 text-ink focus:outline-none focus:border-forest">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1">Konfirmasi Sandi <span class="text-terracotta">*</span></label>
                    <input type="password" name="password_confirmation" placeholder="••••••••" required class="input input-bordered input-sm w-full rounded-xl text-xs bg-cream/20 text-ink focus:outline-none focus:border-forest">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-ink mb-1">Peran Akses Sistem <span class="text-terracotta">*</span></label>
                <select name="role" class="select select-bordered select-sm w-full rounded-xl text-xs bg-cream/20 font-semibold text-ink focus:outline-none focus:border-forest">
                    <option value="user">Warga / User Biasa</option>
                    <option value="penjemput">Penjemput (Kurir Armada)</option>
                    <option value="pengrajin">Pengrajin Kriya Mitra</option>
                    <option value="admin">Admin Utama</option>
                </select>
            </div>
            <button type="submit" class="btn btn-sm w-full bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-semibold h-11 shadow-sm transition-all mt-3">
                Daftarkan & Aktifkan Pengguna &rarr;
            </button>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop bg-ink/40 backdrop-blur-xs"><button>close</button></form>
</dialog>

<!-- JavaScript Pengendali Tab -->
<script>
    function switchAdminTab(tabName) {
        const contents = ['users', 'partners', 'pending'];
        
        contents.forEach(item => {
            const elContent = document.getElementById('tab_content_' + item);
            const elBtn = document.getElementById('tab_btn_' + item);
            
            if (item === tabName) {
                elContent.classList.remove('hidden');
                elBtn.className = "px-4 py-2 rounded-xl text-xs font-bold transition-all bg-forest text-white shadow-xs flex items-center gap-2";
            } else {
                elContent.classList.add('hidden');
                elBtn.className = "px-4 py-2 rounded-xl text-xs font-semibold transition-all text-ink-soft hover:bg-cream/60 flex items-center gap-2";
            }
        });
    }
</script>
@endsection