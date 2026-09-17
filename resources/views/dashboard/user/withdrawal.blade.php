@extends('layouts.dashboard', ['title' => 'Pencairan Poin — SulapaKarya'])

@section('dashboard-content')
<div class="space-y-6 text-left max-w-6xl mx-auto">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white border border-ink/5 p-6 rounded-2xl shadow-none">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[10px] uppercase font-bold tracking-widest text-forest bg-forest/10 px-2.5 py-0.5 rounded-full font-mono">Pencairan Dana</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">Tukar Poin ke Rekening Bank</h1>
            <p class="text-xs text-ink-soft mt-0.5">Konversikan poin reward setoran sampah Anda menjadi uang tunai langsung ke rekening pribadi.</p>
        </div>

        <div class="bg-cream/40 border border-ink/5 p-3.5 rounded-2xl text-left min-w-[300px]">
            <span class="text-[10px] text-ink-soft block font-bold uppercase tracking-wider">Saldo Siap Tarik</span>
            <span class="text-2xl font-black font-mono text-forest">Rp{{ number_format($safePointsBalance, 0, ',', '.') }}</span>
            <span class="text-xs font-bold text-amber-700 block font-sans">Poin (Rp {{ number_format($safePointsBalance, 0, ',', '.') }})</span>
        </div>
    </div>

    <!-- Alert Error / Success -->
    @if($errors->any())
        <div class="alert alert-error bg-terracotta/10 border-terracotta/20 text-terracotta rounded-2xl text-xs font-semibold p-4 shadow-none">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Formulir Pencairan -->
        <div class="lg:col-span-2 bg-white border border-ink/5 rounded-2xl p-6 shadow-none">
            <h2 class="font-bold text-sm text-ink mb-4 pb-2 border-b border-ink/5">Formulir Transfer Rekening</h2>

            <form action="{{ route('pencairan.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-ink mb-1.5">Jumlah Poin yang Dicairkan</label>
                    <div class="relative">
                        <input type="number" name="points" id="input_points" min="50000" max="{{ $safePointsBalance }}" 
                               placeholder="Minimal 50000 poin" required
                               oninput="document.getElementById('display_rupiah').innerText = 'Rp ' + (this.value ? new Intl.NumberFormat('id-ID').format(this.value) : '0')"
                               class="input input-sm input-bordered w-full rounded-xl text-xs bg-cream/20 font-mono text-ink pr-16 h-10 focus:border-forest focus:outline-none">
                        <button type="button" 
                                onclick="document.getElementById('input_points').value = {{ $safePointsBalance }}; document.getElementById('display_rupiah').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format({{ $safePointsBalance }});" 
                                class="absolute right-2.5 top-2 text-[10px] font-bold text-forest bg-forest/10 px-2 py-1 rounded-md hover:bg-forest/20">
                            TARIK SEMUA
                        </button>
                    </div>
                    <div class="flex justify-between items-center mt-1.5 text-[11px]">
                        <span class="text-ink-soft">Estimasi dana tunai yang diterima:</span>
                        <span class="font-mono font-bold text-forest text-xs" id="display_rupiah">Rp 0</span>
                    </div>
                </div>

                @php
                    $savedBank = $currentUser->bank_name ?? null;
                    $savedAccount = $currentUser->account_number ?? null;
                    $hasBankDetails = !empty($savedBank) && !empty($savedAccount);
                @endphp

                @if($hasBankDetails)
                    {{-- REKENING TERKUNCI PERMANEN --}}
                    <input type="hidden" name="bank_name" value="{{ $savedBank }}">
                    <input type="hidden" name="account_number" value="{{ $savedAccount }}">

                    <div class="p-4 bg-cream/30 border border-ink/10 rounded-2xl space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-forest bg-forest/10 px-2 py-0.5 rounded font-mono">
                                REKENING TERKUNCI (TERVERIFIKASI)
                            </span>
                            <span class="text-[10px] text-ink-soft">Tidak dapat diubah</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                            <div>
                                <span class="text-[10px] text-ink-soft block font-bold uppercase">Bank Tujuan</span>
                                <span class="font-bold text-ink">{{ $savedBank }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-ink-soft block font-bold uppercase">Nomor Rekening</span>
                                <span class="font-mono font-bold text-ink">{{ $savedAccount }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- INPUT REKENING HANYA 1 KALI --}}
                    <div class="p-3 bg-amber-500/10 border border-amber-400/30 rounded-xl text-[11px] text-amber-900 font-medium">
                        ⚠️ <strong>Perhatian:</strong> Data bank & nomor rekening ini hanya dapat diisi 1 kali. Pastikan data sudah benar sebelum dikirimkan.
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-bold text-ink mb-1.5">Bank Tujuan <span class="text-terracotta">*</span></label>
                            <select name="bank_name" required class="select select-sm select-bordered w-full rounded-xl text-xs bg-cream/20 font-semibold text-ink h-10 focus:border-forest focus:outline-none">
                                <option value="" disabled selected>-- Pilih Bank --</option>
                                <option value="BCA">BCA (Bank Central Asia)</option>
                                <option value="BRI">BRI (Bank Rakyat Indonesia)</option>
                                <option value="MANDIRI">Bank Mandiri</option>
                                <option value="BNI">BNI (Bank Negara Indonesia)</option>
                                <option value="BSI">BSI (Bank Syariah Indonesia)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink mb-1.5">Nomor Rekening <span class="text-terracotta">*</span></label>
                            <input type="text" name="account_number" placeholder="Contoh: 1234567890" required 
                                   class="input input-sm input-bordered w-full rounded-xl text-xs bg-cream/20 font-mono text-ink h-10 focus:border-forest focus:outline-none">
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-ink mb-1.5">Nama Pemilik Rekening</label>
                    <input type="text" name="account_holder_name" value="{{ $currentUser->name ?? '' }}" readonly 
                           class="input input-sm input-bordered w-full rounded-xl text-xs bg-cream/40 font-semibold text-ink h-10 focus:border-forest focus:outline-none cursor-not-allowed">
                </div>

                <button type="submit" class="btn btn-sm w-full bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-bold h-11 shadow-xs mt-2">
                    Kirim Permohonan Penarikan Dana &rarr;
                </button>
            </form>
        </div>

        <!-- Ketentuan -->
        <div class="bg-white border border-ink/5 rounded-2xl p-6 space-y-4 shadow-none text-xs">
            <h3 class="font-bold text-ink text-sm pb-2 border-b border-ink/5">Ketentuan Penarikan</h3>
            <div class="space-y-3 text-ink-soft leading-relaxed">
                <p>1. <strong>Nilai Tukar:</strong> 1 Poin = Rp 1.</p>
                <p>2. <strong>Batas Minimum:</strong> Minimal penarikan 50.000 Poin.</p>
                <p>3. <strong>Proteksi Akun:</strong> Rekening diisi 1 kali dan langsung terkunci.</p>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat Penarikan -->
    <div class="bg-white border border-ink/5 rounded-2xl p-6 shadow-none space-y-4">
        <h2 class="font-bold text-sm text-ink pb-2 border-b border-ink/5">Riwayat Transaksi Penarikan</h2>
        <div class="overflow-x-auto">
            <table class="table table-sm w-full text-left">
                <thead>
                    <tr class="border-b border-ink/10 text-ink-soft text-[10px] uppercase font-bold tracking-wider">
                        <th>Kode Penarikan</th>
                        <th>Bank Penerima</th>
                        <th>No. Rekening</th>
                        <th>Poin Ditukar</th>
                        <th>Dana Ditransfer</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/5 text-xs">
                    @forelse($withdrawals as $w)
                        <tr class="hover:bg-cream/30 font-mono">
                            <td class="font-bold text-ink">{{ $w->withdrawal_code }}</td>
                            <td class="font-sans font-bold text-ink">{{ $w->bank_name }}</td>
                            <td>{{ $w->account_number }}</td>
                            <td class="text-amber-700 font-bold">-{{ number_format($w->points_redeemed) }}</td>
                            <td class="text-forest font-bold font-sans">Rp {{ number_format($w->cash_amount, 0, ',', '.') }}</td>
                            <td><span class="badge badge-xs bg-forest/15 text-forest border-none font-bold text-[9px] px-2 py-0.5">{{ strtoupper($w->status) }}</span></td>
                            <td class="text-ink-soft text-[11px] font-sans">{{ $w->created_at->translatedFormat('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-8 text-ink-soft/60 text-xs">Belum ada riwayat transaksi penarikan dana.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($withdrawals->hasPages())
            <div class="pt-3 border-t border-ink/5">{{ $withdrawals->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal Struk Resi Digital (Muncul Otomatis) -->
@if(session('receipt_data'))
    <dialog id="modal_struk_sukses" class="modal modal-middle" open>
        <div class="modal-box bg-white rounded-3xl p-6 border border-ink/10 text-left max-w-md shadow-2xl">
            <h3 class="font-bold text-lg text-center text-ink">Pencairan Dana Berhasil!</h3>
            <div class="bg-cream/40 border border-ink/5 rounded-2xl p-4 my-4 space-y-2 text-xs font-mono">
                <div class="flex justify-between"><span>ID Transaksi:</span><span class="font-bold text-ink">{{ session('receipt_data')['code'] }}</span></div>
                <div class="flex justify-between"><span>Bank Penerima:</span><span class="font-bold text-ink">{{ session('receipt_data')['bank'] }}</span></div>
                <div class="flex justify-between"><span>No. Rekening:</span><span class="font-bold text-ink">{{ session('receipt_data')['account_number'] }}</span></div>
                <div class="flex justify-between text-sm border-t border-ink/10 pt-2"><span class="font-bold">Total Dana:</span><span class="font-bold text-forest">Rp {{ number_format(session('receipt_data')['amount'], 0, ',', '.') }}</span></div>
            </div>
            <form method="dialog"><button class="btn btn-sm w-full bg-forest text-white hover:bg-forest-dark border-none rounded-xl text-xs font-bold h-10">Selesai</button></form>
        </div>
    </dialog>
@endif
@endsection