@extends('layouts.dashboard', ['title' => 'Dashboard Pengangkut — SulapaKarya'])

@section('dashboard-content')
@php
    $currentUser = \App\Models\User::find(session('user_id'));
@endphp

<div class="space-y-6 animate-fadeIn text-left">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gradient-to-r from-white to-cream p-6 rounded-[1.5rem] border border-ink/5 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="avatar {{ $currentUser && $currentUser->foto_profil ? '' : 'placeholder' }}">
                <div class="bg-gradient-to-tr from-forest to-forest-dark text-white rounded-full w-16 h-16 shadow-lg shadow-forest/20 ring-4 ring-white overflow-hidden flex items-center justify-center">
                    @if($currentUser && $currentUser->foto_profil)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($currentUser->foto_profil) }}?v={{ time() }}" alt="Profil" class="w-full h-full object-cover" />
                    @else
                        <span class="text-xl font-bold font-display">{{ strtoupper(substr(session('name', 'P'), 0, 1)) }}</span>
                    @endif
                </div>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="font-display font-extrabold text-2xl text-ink leading-tight">Selamat Datang, {{ session('name', 'Staf Kurir') }}</h1>
                    <span class="badge bg-forest/10 text-forest border-none text-[10px] font-bold px-2 py-0.5 rounded-full">Kolektor Aktif</span>
                </div>
                <p class="text-xs text-ink-soft font-semibold mt-1 uppercase tracking-wider text-forest">Sektor Penjemputan Kota Makassar</p>
            </div>
        </div>

        <div class="flex items-center gap-3 bg-white border border-forest/10 p-3 rounded-2xl w-full sm:w-auto shadow-sm">
            <div class="bg-amber-50 text-amber-500 p-2 rounded-xl border border-amber-100 flex items-center justify-center">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-ink-soft/60 block">RATING OPERASIONAL</span>
                <span class="text-sm font-bold text-ink">★ 4.9 <span class="text-xs text-ink-soft font-medium">Sempurna</span></span>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-error bg-terracotta/10 border-terracotta/20 text-terracotta rounded-2xl text-xs font-bold p-4 shadow-sm">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success bg-forest/10 border-forest/20 text-forest rounded-2xl text-xs font-bold p-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-ink/5 rounded-2xl p-6 shadow-sm">
        <div class="mb-4">
            <h2 class="font-display font-extrabold text-xl text-ink">Daftar Rute & Jadwal Penjemputan Aktif</h2>
            <p class="text-xs text-ink-soft font-medium">Daftar lokasi setoran sampah warga yang didelegasikan oleh Admin kepada Anda.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-sm">
                <thead>
                    <tr class="bg-sand/30 text-ink border-b border-ink/5">
                        <th class="font-extrabold">Warga & Kode</th>
                        <th class="font-extrabold">Kategori</th>
                        <th class="font-extrabold">Estimasi Berat</th>
                        <th class="font-extrabold">Alamat Penjemputan</th>
                        <th class="font-extrabold text-center">Tindakan Lapangan</th>
                    </tr>
                </thead>
                <tbody class="font-medium">
                    @forelse($activeTasks as $task)
                        @php $warga = \App\Models\User::find($task->user_id); @endphp
                        <tr class="border-b border-ink/5 hover:bg-sand/10 transition-colors">
                            <td class="py-4">
                                <span class="font-bold text-ink block">{{ $warga->name ?? 'Anonim' }}</span>
                                <span class="text-[10px] text-ink-soft font-mono">{{ $task->deposit_code }}</span>
                            </td>
                            <td><span class="badge bg-cream border border-ink/10 text-ink-soft font-bold text-[10px] px-2 py-1 rounded-md">{{ strtoupper($task->category) }}</span></td>
                            <td class="font-mono font-bold text-ink">{{ number_format($task->estimated_weight, 2) }} Kg</td>
                            <td class="text-ink-soft max-w-xs truncate" title="{{ $task->pickup_address }}">{{ $task->pickup_address }}</td>
                            <td class="py-4 text-center">
                                @if($task->status === 'menunggu_penjemput')
                                    <form action="{{ route('penjemput.updateStatus', $task->id) }}" method="POST" class="m-0 p-0">
                                        @csrf
                                        <input type="hidden" name="status" value="penjemput_menuju_lokasi">
                                        <button type="submit" class="btn btn-xs bg-forest border-none text-white font-bold rounded-md px-3">🚀 Mulai Perjalanan</button>
                                    </form>
                                @elseif($task->status === 'penjemput_menuju_lokasi')
                                    <form action="{{ route('penjemput.updateStatus', $task->id) }}" method="POST" class="m-0 p-0">
                                        @csrf
                                        <input type="hidden" name="status" value="penjemput_tiba">
                                        <button type="submit" class="btn btn-xs bg-amber-500 border-none text-white font-bold rounded-md px-3">📍 Saya Sudah Tiba</button>
                                    </form>
                                @elseif($task->status === 'penjemput_tiba')
                                    <button onclick="openWeightModal('{{ $task->id }}')" class="btn btn-xs bg-maritime border-none text-white font-bold rounded-md px-3">⚖️ Timbang & Scan QR</button>
                                @endif
                            </td>
                        </tr>

                        <dialog id="timbang_modal_{{ $task->id }}" class="modal modal-bottom sm:modal-middle">
                            <div class="modal-box bg-white max-w-md rounded-[2rem] border border-ink/5 p-6 text-left relative overflow-y-auto max-h-[90vh]">
                                <button onclick="closeWeightModal('{{ $task->id }}')" class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-ink-soft">✕</button>
                                
                                <h3 class="font-display font-extrabold text-xl text-ink">Validasi Timbangan & QR</h3>
                                <p class="text-xs text-ink-soft mt-1 mb-4">Input data timbangan riil dan verifikasi QR Code warga untuk mencairkan saldo poin.</p>
                                
                                <form action="{{ route('penjemput.completeTransaction', $task->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    
                                    <div class="form-control">
                                        <label class="label py-1"><span class="label-text font-bold text-xs text-forest">Berat Aktual Hasil Timbangan Lapangan (Kg) <span class="text-terracotta">*</span></span></label>
                                        <input type="number" step="0.01" name="actual_weight" id="actual_weight_{{ $task->id }}" 
                                               value="{{ old('actual_weight', $task->estimated_weight) }}" required 
                                               oninput="updateLivePoints('{{ $task->id }}')"
                                               class="input input-bordered w-full rounded-xl font-bold font-mono text-xl focus:outline-none focus:border-forest bg-cream/20">
                                    </div>

                                    <div class="bg-forest/[0.03] border border-forest/10 p-3.5 rounded-xl flex items-center justify-between">
                                        <div>
                                            <span class="text-[10px] text-ink-soft font-extrabold uppercase tracking-wider block">Insentif Tabungan Poin</span>
                                            <span class="text-xs text-ink-soft font-medium">Nilai konversi: 1 Kg = 1.000 Poin</span>
                                        </div>
                                        <div class="text-right">
                                            <span id="live_points_{{ $task->id }}" class="text-2xl font-black font-mono text-forest">0</span>
                                            <span class="text-xs font-bold text-forest block">Poin</span>
                                        </div>
                                    </div>

                                    <div class="form-control space-y-2">
                                        <label class="label py-1 pb-0"><span class="label-text font-bold text-xs">Metode Verifikasi ID Digital Warga <span class="text-terracotta">*</span></span></label>
                                        
                                        <div class="join w-full border border-ink/10 rounded-xl overflow-hidden text-xs font-bold bg-white mb-2">
                                            <button type="button" id="btn_mode_paste_{{ $task->id }}" onclick="toggleVerificationMode('{{ $task->id }}', 'paste')" class="join-item btn btn-xs flex-1 bg-forest text-white border-none normal-case">Salin Kode Token</button>
                                            <button type="button" id="btn_mode_scan_{{ $task->id }}" onclick="toggleVerificationMode('{{ $task->id }}', 'scan')" class="join-item btn btn-xs flex-1 bg-white text-ink-soft hover:bg-cream border-none normal-case">📸 Kamera Scan QR</button>
                                        </div>

                                        <div id="container_paste_{{ $task->id }}" class="relative">
                                            <input type="text" name="qr_code_warga" id="qr_input_{{ $task->id }}" placeholder="Tempel / ketik token UUID QR Code warga..." required class="input input-bordered w-full rounded-xl text-xs focus:outline-none focus:border-forest bg-cream/20 font-mono">
                                        </div>

                                        <div id="container_scan_{{ $task->id }}" class="hidden space-y-2">
                                            <div class="w-full bg-black aspect-square rounded-2xl overflow-hidden border border-ink/10 relative shadow-inner">
                                                <div id="reader_{{ $task->id }}" class="w-full h-full"></div>
                                            </div>
                                            <p class="text-[10px] text-ink-soft text-center font-medium italic">Arahkan kamera ke QR Code aplikasi warga untuk memindai otomatis.</p>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn w-full bg-forest text-white border-none rounded-xl font-extrabold normal-case mt-4 shadow-md shadow-forest/10">
                                        Selesaikan & Kirim Poin Reward 🌟
                                    </button>
                                </form>
                            </div>
                        </dialog>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-ink-soft/60 font-medium text-xs">
                                🎉 Bagus! Tidak ada antrean rute penjemputan tersisa untuk Anda hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white border border-ink/5 rounded-2xl p-6 shadow-sm">
        <div class="mb-4">
            <h2 class="font-display font-extrabold text-xl text-ink">Log Validasi Setoran Sukses</h2>
            <p class="text-xs text-ink-soft font-medium">Rekam data pekerjaan setoran sampah warga yang telah berhasil Anda tuntaskan.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra w-full text-sm">
                <thead>
                    <tr class="bg-sand/30 text-ink border-b border-ink/5">
                        <th class="font-extrabold">PENYETOR</th>
                        <th class="font-extrabold">JENIS SAMPAH</th>
                        <th class="font-extrabold">BERAT AKTUAL</th>
                        <th class="font-extrabold">POIN DIKIRIM</th>
                        <th class="font-extrabold">STATUS VALIDASI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedLogs as $log)
                        @php $pemohon = \App\Models\User::find($log->user_id); @endphp
                        <tr class="border-b border-ink/5">
                            <td class="font-semibold text-ink">{{ $pemohon->name ?? 'Anonim' }}</td>
                            <td>{{ ucfirst($log->category) }}</td>
                            <td class="font-bold text-ink-soft font-mono">{{ number_format($log->actual_weight, 2) }} Kg</td>
                            <td class="text-forest font-extrabold font-mono">+ {{ number_format($log->points_earned) }}</td>
                            <td>
                                <span class="badge bg-emerald-50 text-emerald-600 border border-emerald-100 text-[10px] font-bold px-2 py-1 rounded-md">✓ Terkirim | {{ $log->updated_at->format('H:i') }} WITA</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-ink-soft/50 text-xs font-medium">
                                📭 Anda belum menyelesaikan penjemputan transaksi apa pun hari ini.
                            </td>
                        </tr>
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
        document.getElementById('timbang_modal_' + taskId).showModal();
        updateLivePoints(taskId);
    }

    function closeWeightModal(taskId) {
        stopScanner(taskId);
        document.getElementById('timbang_modal_' + taskId).close();
    }

    // 🌟 Hitung Poin Berbasis Input Kg Lapangan Riil (Simulasi 1 Kg = 1.000 Poin)
    function updateLivePoints(taskId) {
        const weightInput = document.getElementById('actual_weight_' + taskId);
        const pointsDisplay = document.getElementById('live_points_' + taskId);
        
        let weight = parseFloat(weightInput.value) || 0;
        let finalPoints = Math.round(weight * 1000);
        
        pointsDisplay.innerText = new Intl.NumberFormat('id-ID').format(finalPoints);
    }

    // Swicth antara Mode Copy-Paste atau Kamera Aktif
    function toggleVerificationMode(taskId, mode) {
        const pasteBtn = document.getElementById('btn_mode_paste_' + taskId);
        const scanBtn = document.getElementById('btn_mode_scan_' + taskId);
        const pasteContainer = document.getElementById('container_paste_' + taskId);
        const scanContainer = document.getElementById('container_scan_' + taskId);
        const qrInput = document.getElementById('qr_input_' + taskId);

        if (mode === 'paste') {
            pasteBtn.className = "join-item btn btn-xs flex-1 bg-forest text-white border-none normal-case";
            scanBtn.className = "join-item btn btn-xs flex-1 bg-white text-ink-soft hover:bg-cream border-none normal-case";
            pasteContainer.classList.remove('hidden');
            scanContainer.classList.add('hidden');
            qrInput.setAttribute('required', 'required');
            stopScanner(taskId);
        } else {
            scanBtn.className = "join-item btn btn-xs flex-1 bg-forest text-white border-none normal-case";
            pasteBtn.className = "join-item btn btn-xs flex-1 bg-white text-ink-soft hover:bg-cream border-none normal-case";
            pasteContainer.classList.add('hidden');
            scanContainer.classList.remove('hidden');
            qrInput.removeAttribute('required'); // Input teks disembunyikan, nilai diambil dari scanner camera
            startScanner(taskId);
        }
    }

    // Jalankan Engine Pustaka Kamera HTML5-QR Code
    function startScanner(taskId) {
        if (html5QrScannerMap[taskId]) return; // Cegah duplikasi inisialisasi kamera

        const html5QrCode = new Html5Qrcode("reader_" + taskId);
        html5QrScannerMap[taskId] = html5QrCode;

        const config = { fps: 10, qrbox: { width: 220, height: 220 } };

        html5QrCode.start(
            { facingMode: "environment" }, // Paksa kamera belakang smartphone kurir
            config,
            (decodedText) => {
                // Sukses Membaca Token UUID QR Code Warga
                document.getElementById('qr_input_' + taskId).value = decodedText;
                
                // Berikan umpan balik getaran instan jika didukung perangkat kurir
                if (navigator.vibrate) navigator.vibrate(100);
                
                // Kembalikan ke panel teks otomatis agar input siap dikirim
                toggleVerificationMode(taskId, 'paste');
                alert("✓ QR Code Warga Berhasil Dipindai!");
            },
            (errorMessage) => {
                // Abaikan kesalahan pembacaan frame per detik untuk kestabilan log
            }
        ).catch(err => {
            console.error("Gagal mendeteksi izin perangkat kamera: ", err);
        });
    }

    function stopScanner(taskId) {
        if (html5QrScannerMap[taskId]) {
            html5QrScannerMap[taskId].stop().then(() => {
                delete html5QrScannerMap[taskId];
            }).catch(err => {
                console.error("Gagal menghentikan transmisi kamera: ", err);
            });
        }
    }
</script>
@endsection