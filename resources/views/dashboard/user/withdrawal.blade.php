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

    <!-- Kotak Form & Informasi Kurs -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Formulir Pencairan -->
        <div class="lg:col-span-2 bg-white border border-ink/5 rounded-2xl p-6 shadow-none">
            <h2 class="font-bold text-sm text-ink mb-4 pb-2 border-b border-ink/5 flex items-center gap-2">
                <span>Formulir Transfer Rekening</span>
            </h2>

            <form action="{{ route('pencairan.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-ink mb-1.5">Jumlah Poin yang Dicairkan</label>
                    <div class="relative">
                        <input type="number" name="points" id="input_points" min="1000" max="{{ $safePointsBalance }}" 
                               placeholder="Minimal 1000 poin" required
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-ink mb-1.5">Bank Tujuan</label>
                        <select name="bank_name" required class="select select-sm select-bordered w-full rounded-xl text-xs bg-cream/20 font-semibold text-ink h-10 focus:border-forest focus:outline-none">
                            <option value="BCA">BCA (Bank Central Asia)</option>
                            <option value="BRI">BRI (Bank Rakyat Indonesia)</option>
                            <option value="MANDIRI">Bank Mandiri</option>
                            <option value="BNI">BNI (Bank Negara Indonesia)</option>
                            <option value="BSI">BSI (Bank Syariah Indonesia)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-ink mb-1.5">Nomor Rekening</label>
                        <input type="text" name="account_number" placeholder="Contoh: 1234567890" required 
                               class="input input-sm input-bordered w-full rounded-xl text-xs bg-cream/20 font-mono text-ink h-10 focus:border-forest focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-ink mb-1.5">Nama Pemilik Rekening</label>
                    <input type="text" name="account_holder_name" value="{{ $currentUser->name ?? '' }}" required 
                           class="input input-sm input-bordered w-full rounded-xl text-xs bg-cream/40 font-semibold text-ink h-10 focus:border-forest focus:outline-none">
                    <p class="text-[10px] text-amber-700 mt-1 font-medium">
                        *Nama rekening harus cocok persis dengan nama profil pendaftar (<strong>{{ $currentUser->name ?? 'User' }}</strong>) guna memenuhi regulasi anti-fraud.
                    </p>
                </div>

                <button type="submit" class="btn btn-sm w-full bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-bold h-11 shadow-xs mt-2">
                    Kirim Permohonan Penarikan Dana &rarr;
                </button>
            </form>
        </div>

        <!-- Panel Edukasi & Ketentuan -->
        <div class="bg-white border border-ink/5 rounded-2xl p-6 space-y-4 shadow-none text-xs">
            <h3 class="font-bold text-ink text-sm pb-2 border-b border-ink/5">Ketentuan Penarikan</h3>
            
            <div class="space-y-3 text-ink-soft leading-relaxed">
                <div class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-md bg-forest/10 text-forest flex items-center justify-center shrink-0 font-bold font-mono text-[10px]">1</span>
                    <p><strong>Nilai Tukar Tetap:</strong> 1 Poin SulapaKarya bernilai setara <strong>Rp 1</strong> tanpa potongan biaya admin platform.</p>
                </div>

                <div class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-md bg-forest/10 text-forest flex items-center justify-center shrink-0 font-bold font-mono text-[10px]">2</span>
                    <p><strong>Batas Minimum:</strong> Penarikan saldo dapat diproses mulai dari akumulasi <strong>1.000 Poin</strong>.</p>
                </div>

                <div class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-md bg-forest/10 text-forest flex items-center justify-center shrink-0 font-bold font-mono text-[10px]">3</span>
                    <p><strong>Proses Transfer:</strong> Pada simulasi demo hackathon ini, dana dikirimkan secara instan melalui sistem mock disbursement gateway.</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Riwayat Pencairan Dana -->
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
                        <tr class="hover:bg-cream/30 transition-colors font-mono">
                            <td class="font-bold text-ink">{{ $w->withdrawal_code }}</td>
                            <td class="font-sans font-bold text-ink">{{ $w->bank_name }}</td>
                            <td>{{ $w->account_number }}</td>
                            <td class="text-amber-700 font-bold">-{{ number_format($w->points_redeemed) }}</td>
                            <td class="text-forest font-bold font-sans">Rp {{ number_format($w->cash_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-xs bg-forest/15 text-forest border-none font-bold text-[9px] px-2 py-0.5">
                                    {{ strtoupper($w->status) }}
                                </span>
                            </td>
                            <td class="text-ink-soft text-[11px] font-sans">{{ $w->created_at->translatedFormat('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-ink-soft/60 text-xs font-sans">
                                Belum ada riwayat transaksi penarikan dana.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($withdrawals->hasPages())
            <div class="pt-3 border-t border-ink/5">
                {{ $withdrawals->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Struk Resi Digital (Muncul Otomatis Saat Berhasil) -->
@if(session('receipt_data'))
    <dialog id="modal_struk_sukses" class="modal modal-middle" open>
        <div class="modal-box bg-white rounded-3xl p-6 border border-ink/10 text-left max-w-md shadow-2xl">
            <div class="flex items-center justify-center w-12 h-12 bg-forest/10 text-forest rounded-2xl mx-auto mb-3">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>

            <h3 class="font-bold text-lg text-center text-ink">Pencairan Dana Berhasil!</h3>
            <p class="text-xs text-center text-ink-soft mt-0.5">Saldo telah berhasil dipotong dan ditransfer ke rekening bank.</p>

            <div class="bg-cream/40 border border-ink/5 rounded-2xl p-4 my-4 space-y-2 text-xs font-mono">
                <div class="flex justify-between">
                    <span class="text-ink-soft">ID Transaksi:</span>
                    <span class="font-bold text-ink">{{ session('receipt_data')['code'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-soft">Ref Payment:</span>
                    <span class="font-bold text-forest">{{ session('receipt_data')['ref_id'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-soft">Bank Penerima:</span>
                    <span class="font-bold text-ink">{{ session('receipt_data')['bank'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-soft">No. Rekening:</span>
                    <span class="font-bold text-ink">{{ session('receipt_data')['account_number'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-soft">Nama Pemilik:</span>
                    <span class="font-bold text-ink">{{ session('receipt_data')['account_name'] }}</span>
                </div>
                <div class="border-t border-ink/10 pt-2 flex justify-between text-sm">
                    <span class="font-bold text-ink">Total Dana:</span>
                    <span class="font-bold text-forest">Rp {{ number_format(session('receipt_data')['amount'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-[11px] text-ink-soft">
                    <span>Poin Terpotong:</span>
                    <span>-{{ number_format(session('receipt_data')['points'], 0, ',', '.') }} Poin</span>
                </div>
            </div>

            <form method="dialog">
                <button class="btn btn-sm w-full bg-forest text-white hover:bg-forest-dark border-none rounded-xl text-xs font-bold h-10">
                    Selesai
                </button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-ink/40"><button>close</button></form>
    </dialog>
@endif
@endsection