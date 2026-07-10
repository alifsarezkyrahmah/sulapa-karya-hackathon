@extends('layouts.dashboard', ['title' => 'Verifikasi Setoran — SulapaKarya Macca'])

@section('dashboard-content')
<div class="space-y-6 animate-fadeIn">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="text-left">
            <h1 class="font-display font-extrabold text-2xl text-ink tracking-tight">Verifikasi Setoran Sampah</h1>
            <p class="text-xs text-ink-soft/80 font-medium mt-1">Kelola dan verifikasi permintaan penjemputan sampah daur ulang dari warga secara teliti.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-error bg-terracotta/10 border-terracotta/20 text-terracotta rounded-2xl text-xs font-bold text-left p-4 shadow-sm">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success bg-forest/10 border-forest/20 text-forest rounded-2xl text-xs font-bold text-left p-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-ink/5 rounded-[1.5rem] p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto rounded-xl border border-ink/5">
            <table class="table w-full text-sm">
                <thead>
                    <tr class="border-b border-ink/5 text-ink/70 font-bold uppercase tracking-wider text-xs bg-cream/60">
                        <th class="py-3.5 pl-5">No. Referensi & Tanggal</th>
                        <th class="py-3.5">Detail Sampah & Est. Berat</th>
                        <th class="py-3.5">Foto Wujud</th>
                        <th class="py-3.5 text-center">Status Lacak</th>
                        <th class="py-3.5 text-center pr-5">Aksi / Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="font-medium text-ink/90">
                    @forelse($deposits as $d)
                        @php 
                            $warga = \App\Models\User::find($d->user_id); 
                        @endphp
                        <tr class="hover:bg-cream/10 border-b border-ink/5 transition-colors">
                            
                            <td class="py-4 pl-5">
                                <span class="text-ink-soft font-mono font-bold block">{{ $d->deposit_code }}</span>
                                <span class="text-[10px] text-ink-soft/60 block tracking-wide mt-0.5">{{ \Carbon\Carbon::parse($d->created_at)->translatedFormat('d M Y - H:i') }}</span>
                                <span class="text-xs font-bold text-maritime block mt-1">Oleh: {{ $warga->name ?? 'Anonim' }}</span>
                            </td>

                            <td class="py-4">
                                <span class="badge bg-cream border border-ink/10 text-ink-soft font-bold text-[10px] px-2 py-1 rounded-md mb-1">{{ $d->category }}</span>
                                <p class="text-xs font-bold text-ink">Est: {{ number_format($d->estimated_weight, 2, ',', '.') }} kg</p>
                                <p class="text-[10px] text-gray-400 mt-1 truncate max-w-[150px]">Reward: {{ strtoupper($d->reward_type) }}</p>
                            </td>

                            <td class="py-4">
                                <div class="avatar">
                                    <div class="w-14 h-14 rounded-lg border border-ink/10 shadow-sm cursor-pointer hover:scale-105 transition-transform" onclick="document.getElementById('photo_modal_{{ $d->id }}').showModal()" title="Klik untuk perbesar">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($d->photo_path) }}" alt="Foto Sampah" class="object-cover" />
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 text-center">
                                @if($d->status === 'pending' || $d->status === 'menunggu_admin')
                                    <span class="badge bg-amber-100 text-amber-700 border-none text-[10px] font-bold px-2 py-1.5 rounded-md">Menunggu Verifikasi</span>
                                @elseif($d->status === 'ditolak')
                                    <span class="badge bg-terracotta/10 text-terracotta border-none text-[10px] font-bold px-2 py-1.5 rounded-md">Ditolak Admin</span>
                                @elseif($d->status === 'menunggu_penjemput')
                                    <span class="badge bg-maritime/10 text-maritime border-none text-[10px] font-bold px-2 py-1.5 rounded-md">Mencari Kurir</span>
                                @elseif($d->status === 'penjemput_menuju_lokasi')
                                    <span class="badge bg-blue-100 text-blue-700 border-none text-[10px] font-bold px-2 py-1.5 rounded-md">Kurir OTW</span>
                                @elseif($d->status === 'penjemput_tiba')
                                    <span class="badge bg-purple-100 text-purple-700 border-none text-[10px] font-bold px-2 py-1.5 rounded-md">Kurir Tiba (Timbang)</span>
                                @elseif($d->status === 'selesai')
                                    <span class="badge bg-forest/20 text-forest-dark border-none text-[10px] font-bold px-2 py-1.5 rounded-md">✓ Selesai & Ditransfer</span>
                                @else
                                    <span class="badge badge-ghost text-[10px] uppercase">{{ $d->status }}</span>
                                @endif
                            </td>

                            <td class="py-4 text-center pr-5">
                                @if($d->status === 'pending' || $d->status === 'menunggu_admin')
                                    <button onclick="document.getElementById('verify_modal_{{ $d->id }}').showModal()" class="btn btn-xs bg-maritime border-none text-white hover:bg-maritime-dark rounded-md font-bold px-3 shadow-sm">
                                        🔍 Cek Detail & Proses
                                    </button>
                                @else
                                    @if($d->penjemput_id)
                                        @php $kurir = \App\Models\User::find($d->penjemput_id); @endphp
                                        <span class="text-[10px] text-ink-soft/60 block font-semibold bg-gray-50 py-1 px-2 rounded border border-ink/5">Penjemput: <br> <b class="text-ink">{{ $kurir->name ?? 'Tidak diketahui' }}</b></span>
                                    @endif
                                @endif
                            </td>
                        </tr>

                        <dialog id="photo_modal_{{ $d->id }}" class="modal modal-bottom sm:modal-middle">
                            <div class="modal-box bg-white rounded-2xl p-2 relative max-w-sm">
                                <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-white drop-shadow-md z-10">✕</button></form>
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($d->photo_path) }}" class="w-full h-auto rounded-xl object-contain">
                            </div>
                            <form method="dialog" class="modal-backdrop bg-ink/70 backdrop-blur-sm"><button>close</button></form>
                        </dialog>

                        @if($d->status === 'pending' || $d->status === 'menunggu_admin')
                        <dialog id="verify_modal_{{ $d->id }}" class="modal modal-bottom sm:modal-middle">
                            <div class="modal-box bg-white max-w-2xl rounded-[2rem] border border-ink/5 p-6 sm:p-8 text-left relative">
                                <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-6 top-6 text-ink-soft bg-gray-100 hover:bg-gray-200">✕</button></form>
                                
                                <h3 class="font-display font-extrabold text-2xl text-ink">Verifikasi Setoran Warga</h3>
                                <p class="text-sm text-ink-soft mt-1 mb-6 border-b border-ink/5 pb-4">Tinjau dengan seksama data pengajuan di bawah ini sebelum mengirimkan unit penjemputan.</p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
                                    
                                    <div class="md:col-span-3 space-y-4">
                                        <div class="bg-cream/30 p-4 rounded-xl border border-ink/5">
                                            <span class="text-[10px] font-bold text-ink-soft uppercase tracking-widest block mb-2">Informasi Pemohon</span>
                                            <div class="flex flex-col gap-1 text-sm">
                                                <div class="flex justify-between"><span class="text-ink-soft">Nama:</span> <span class="font-bold text-ink">{{ $warga->name ?? 'Anonim' }}</span></div>
                                                <div class="flex justify-between"><span class="text-ink-soft">No. HP:</span> <span class="font-bold text-ink">{{ $warga->phone ?? '-' }}</span></div>
                                            </div>
                                        </div>

                                        <div class="bg-forest/5 p-4 rounded-xl border border-forest/10">
                                            <span class="text-[10px] font-bold text-forest-dark uppercase tracking-widest block mb-2">Detail Sampah Daur Ulang</span>
                                            <div class="flex flex-col gap-1 text-sm">
                                                <div class="flex justify-between"><span class="text-forest-dark/70">Kategori Utama:</span> <span class="font-bold text-forest-dark">{{ strtoupper($d->category) }}</span></div>
                                                <div class="flex justify-between"><span class="text-forest-dark/70">Sub-Kategori:</span> <span class="font-semibold text-forest-dark">{{ $d->sub_category ?? 'Tidak spesifik' }}</span></div>
                                                <div class="flex justify-between"><span class="text-forest-dark/70">Estimasi Timbangan:</span> <span class="font-mono font-bold text-xl text-forest">{{ number_format($d->estimated_weight, 2, ',', '.') }} Kg</span></div>
                                                <div class="flex justify-between"><span class="text-forest-dark/70">Pilihan Imbalan:</span> <span class="font-bold text-maritime">{{ strtoupper($d->reward_type) }}</span></div>
                                            </div>
                                        </div>

                                        <div class="bg-cream/30 p-4 rounded-xl border border-ink/5">
                                            <span class="text-[10px] font-bold text-ink-soft uppercase tracking-widest block mb-2">Lokasi Penjemputan</span>
                                            <p class="font-semibold text-sm leading-snug text-ink">{{ $d->pickup_address }}</p>
                                        </div>
                                    </div>

                                    <div class="md:col-span-2 flex flex-col">
                                        <span class="text-[10px] font-bold text-ink-soft uppercase tracking-widest block mb-2">Foto Fisik Kondisi</span>
                                        <div class="flex-1 rounded-xl border border-ink/10 overflow-hidden bg-gray-50 flex items-center justify-center p-1 shadow-inner relative group">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($d->photo_path) }}" alt="Bukti Foto" class="object-contain w-full h-full max-h-[250px] rounded-lg">
                                            <div class="absolute inset-0 bg-ink/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity cursor-pointer" onclick="document.getElementById('photo_modal_{{ $d->id }}').showModal()">
                                                <span class="text-white font-bold text-xs bg-ink px-3 py-1.5 rounded-lg flex items-center gap-2">🔍 Perbesar Foto</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form action="{{ route('admin.deposits.approve', $d->id) }}" method="POST" class="bg-maritime/5 p-5 rounded-2xl border border-maritime/10">
                                    @csrf
                                    <h4 class="font-bold text-sm text-maritime-dark mb-3 flex items-center gap-2">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        Tindakan Admin
                                    </h4>
                                    
                                    <div class="form-control mb-3">
                                        <label class="label py-1"><span class="label-text font-bold text-xs text-ink">Keputusan Akhir <span class="text-terracotta">*</span></span></label>
                                        <select name="keputusan" id="keputusan_select_{{ $d->id }}" required class="select select-bordered w-full rounded-xl text-sm focus:outline-none focus:border-maritime font-bold" onchange="toggleKurir(this.value, 'kurir_box_{{ $d->id }}')">
                                            <option value="" disabled selected>-- Tentukan Sikap... --</option>
                                            <option value="terima">Terima & Tugaskan Penjemput</option>
                                            <option value="tolak">Tolak Setoran (Bukan sampah daur ulang)</option>
                                        </select>
                                    </div>

                                    <div id="kurir_box_{{ $d->id }}" class="form-control hidden mb-3 bg-white p-3 rounded-xl border border-ink/10 shadow-sm">
                                        <label class="label py-1"><span class="label-text font-bold text-xs text-maritime">Tugaskan ke Kurir <span class="text-terracotta">*</span></span></label>
                                        <select name="penjemput_id" class="select select-bordered w-full rounded-xl text-sm focus:outline-none focus:border-maritime font-bold bg-cream/20">
                                            <option value="" disabled selected>Pilih staf kurir yang sedang aktif...</option>
                                            @foreach($penjemputs as $kurir)
                                                <option value="{{ $kurir->id }}">{{ $kurir->name }} - (HP: {{ $kurir->phone ?? '-' }})</option>
                                            @endforeach
                                        </select>
                                        <span class="text-[10px] text-ink-soft/70 mt-1 block">Tugas ini akan otomatis dikirim ke Dashboard Penjemput terpilih.</span>
                                    </div>

                                    <div class="form-control mb-4">
                                        <label class="label py-1"><span class="label-text font-bold text-xs text-ink">Catatan Instruksi (Opsional)</span></label>
                                        <textarea name="admin_notes" rows="2" placeholder="Contoh instruksi ke kurir atau alasan penolakan ke warga..." class="textarea textarea-bordered w-full rounded-xl text-sm focus:outline-none focus:border-maritime bg-white"></textarea>
                                    </div>

                                    <button type="submit" class="btn w-full bg-ink text-white border-none rounded-xl font-extrabold normal-case shadow-md hover:bg-ink-soft">
                                        Eksekusi Keputusan
                                    </button>
                                </form>
                            </div>
                            <form method="dialog" class="modal-backdrop bg-ink/50 backdrop-blur-sm"><button>close</button></form>
                        </dialog>
                        @endif

                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-ink-soft/60 font-medium text-xs">
                                📭 Belum ada data permohonan setoran sampah masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function toggleKurir(keputusan, boxId) {
        const box = document.getElementById(boxId);
        if(keputusan === 'terima') {
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
        }
    }
</script>
@endsection