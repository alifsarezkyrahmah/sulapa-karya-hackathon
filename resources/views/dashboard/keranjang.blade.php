@extends('layouts.dashboard', ['title' => 'Keranjang Belanja — SulapaKarya'])

@section('dashboard-content')
@php
    $currentUser = auth()->user() ?? \App\Models\User::find(session('user_id'));
    $isProPartner = (($currentUser->business_status ?? '') === 'approved');
    $proDiscountPercent = 10; // Diskon eksklusif 10% untuk Mitra PRO B2B
@endphp

<div class="space-y-6 text-left px-1 sm:px-0">
    
    <!-- Header Ringkas & Terpadu -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-ink/5">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-bold text-ink tracking-tight">Keranjang Kriya</h1>
                @if($isProPartner)
                    <span class="inline-flex items-center bg-amber-400 text-amber-950 font-black text-[9px] font-mono px-2 py-0.5 rounded uppercase tracking-wider shadow-2xs">
                        ★ PRO MEMBER (Diskon {{ $proDiscountPercent }}%)
                    </span>
                @endif
            </div>
            <p class="text-xs text-ink-soft mt-0.5">Tinjau karya daur ulang pilihan Anda, terapkan diskon poin reward, dan selesaikan transaksi.</p>
        </div>

        <a href="/katalog" class="btn btn-xs sm:btn-sm bg-white hover:bg-cream border border-ink/15 text-ink rounded-xl font-bold px-3.5 shadow-2xs w-full sm:w-auto text-center">
            ← Tambah Produk Lain
        </a>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="p-3.5 sm:p-4 rounded-2xl bg-forest/10 border border-forest/20 text-forest text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="p-3.5 sm:p-4 rounded-2xl bg-terracotta/10 border border-terracotta/20 text-terracotta text-xs font-semibold">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST" id="main_checkout_form" class="m-0">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6 items-start">
            
            <!-- ========================================================================= -->
            <!-- KOLOM KIRI: DAFTAR PRODUK DALAM KERANJANG -->
            <!-- ========================================================================= -->
            <div class="lg:col-span-2 bg-white border border-ink/10 rounded-3xl p-5 sm:p-6 shadow-none space-y-4">
                @if(count($cart) > 0)
                    <div class="flex items-center justify-between pb-3 border-b border-ink/5">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" id="select_all_products" checked class="checkbox checkbox-xs checkbox-success rounded" onclick="toggleSelectAll(this)">
                            <span class="text-xs font-bold text-ink">Pilih Semua Item</span>
                        </label>
                        <span class="text-[11px] text-ink-soft font-mono">{{ count($cart) }} Produk Terdaftar</span>
                    </div>

                    <div class="divide-y divide-ink/5">
                        @foreach($cart as $id => $details)
                            <div class="py-4 first:pt-1 last:pb-1 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3.5">
                                
                                <!-- Info Produk & Foto -->
                                <div class="flex items-center gap-3.5 w-full sm:w-auto">
                                    <input type="checkbox" name="selected_items[]" value="{{ $id }}" checked 
                                           data-price="{{ $details['price'] }}" id="checkbox_{{ $id }}"
                                           class="item-checkbox checkbox checkbox-xs checkbox-success rounded shrink-0" 
                                           onclick="calculateTotal()">

                                    <div class="w-14 h-14 sm:w-16 sm:h-16 bg-cream/40 rounded-2xl overflow-hidden border border-ink/10 shrink-0 flex items-center justify-center">
                                        @if(!empty($details['image']))
                                            @if(str_starts_with($details['image'], 'http') || str_starts_with($details['image'], 'https'))
                                                <img src="{{ $details['image'] }}" class="w-full h-full object-cover">
                                            @else
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($details['image']) }}" class="w-full h-full object-cover">
                                            @endif
                                        @else
                                            <span class="text-xs font-black font-mono text-ink-soft/40 uppercase">{{ substr($details['name'], 0, 2) }}</span>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-bold text-ink text-xs sm:text-sm leading-snug truncate max-w-[220px] sm:max-w-xs">{{ $details['name'] }}</h4>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-xs font-black text-forest font-mono">Rp {{ number_format($details['price'], 0, ',', '.') }}</span>
                                            @if($isProPartner)
                                                <span class="text-[9px] font-mono text-amber-900 bg-amber-100 font-bold px-1.5 py-0.2 rounded">-{{ $proDiscountPercent }}%</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Pengaturan Kuantitas & Tombol Hapus -->
                                <div class="flex items-center justify-between w-full sm:w-auto gap-4 pl-7 sm:pl-0 pt-1 sm:pt-0 border-t sm:border-t-0 border-ink/5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[11px] text-ink-soft font-medium">Qty:</span>
                                        <input type="number" name="quantities[{{ $id }}]" value="{{ $details['quantity'] }}" min="1" 
                                               id="qty_{{ $id }}" data-id="{{ $id }}"
                                               class="cart-qty-input input input-bordered input-xs w-14 text-center font-bold font-mono rounded-lg bg-cream/20 focus:outline-none focus:border-forest h-8"
                                               onchange="updateQuantityInline(this)">
                                    </div>
                                    
                                    <button type="submit" form="remove_form_{{ $id }}" class="btn btn-xs btn-circle btn-ghost text-terracotta hover:bg-terracotta/10" title="Hapus produk">
                                        ✕
                                    </button>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center text-ink-soft/60 text-xs space-y-2">
                        <div class="text-2xl">🛒</div>
                        <p class="font-medium">Keranjang belanja Anda masih kosong.</p>
                        <a href="/katalog" class="btn btn-xs bg-forest text-white rounded-xl font-bold px-4 h-8 border-none mt-1 inline-flex">
                            Jelajahi Katalog Kriya
                        </a>
                    </div>
                @endif
            </div>

            <!-- ========================================================================= -->
            <!-- KOLOM KANAN: RINGKASAN PEMBAYARAN -->
            <!-- ========================================================================= -->
            <div class="bg-white border border-ink/10 rounded-3xl p-5 sm:p-6 shadow-none space-y-4">
                <h3 class="font-bold text-sm text-ink pb-2 border-b border-ink/5">Rincian Pembayaran</h3>
                
                <div class="space-y-2 border-b border-ink/5 pb-3 text-xs font-medium">
                    <div class="flex justify-between text-ink-soft">
                        <span>Subtotal Produk</span> 
                        <span id="display_subtotal" class="text-ink font-bold font-mono">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                    </div>

                    <!-- Baris Diskon Khusus Mitra PRO (Jika Berlaku) -->
                    @if($isProPartner)
                        <div id="row_pro_discount" class="flex justify-between text-amber-900 font-semibold">
                            <span>Diskon Mitra PRO ({{ $proDiscountPercent }}%)</span>
                            <span id="display_pro_discount" class="font-bold font-mono">- Rp 0</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-ink-soft">
                        <span>Saldo Poin Anda</span> 
                        <span class="text-maritime font-bold font-mono">
                            <span id="user_points_balance">{{ ($currentUser->points_balance ?? 0) > 0 ? $currentUser->points_balance : 0 }}</span> Poin
                        </span>
                    </div>

                    <div id="row_discount" class="flex justify-between text-forest font-semibold hidden">
                        <span>Potongan Poin Kriya</span> 
                        <span id="display_discount" class="font-bold font-mono">- Rp 0</span>
                    </div>
                </div>
                
                <!-- Checkbox Pemanfaatan Poin -->
                @if(($currentUser->points_balance ?? 0) > 0)
                    <div id="points_container" class="p-3 bg-forest/[0.04] rounded-2xl border border-forest/15 {{ $totalHarga > 0 ? '' : 'hidden' }}">
                        <label class="label cursor-pointer justify-start gap-2.5 p-0">
                            <input type="checkbox" name="use_points" value="1" id="use_points_checkbox" class="checkbox checkbox-xs checkbox-success rounded" onclick="calculateTotal()">
                            <span class="label-text text-xs font-bold text-ink">Gunakan Poin untuk Diskon</span>
                        </label>
                        <span class="text-[10px] text-ink-soft block mt-1 pl-6 leading-tight">Tukarkan 1 poin kriya senilai potongan harga Rp 1.</span>
                    </div>
                @endif

                <div class="flex justify-between items-center py-1">
                    <span class="text-xs font-bold text-ink">Total Akhir:</span>
                    <span id="display_total" class="text-xl font-black text-forest font-mono">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                </div>

                @if(count($cart) > 0)
                    @php $firstProductId = array_key_first($cart); @endphp
                    <input type="hidden" name="product_id" value="{{ $firstProductId }}">
                @endif

                <button type="submit" id="checkout_submit_btn" {{ count($cart) == 0 ? 'disabled' : '' }} 
                    class="btn btn-sm w-full bg-forest hover:bg-forest-dark text-white border-none rounded-xl text-xs font-bold h-11 shadow-sm transition-all disabled:bg-gray-100 disabled:text-gray-400">
                    Lanjut Pembayaran via Midtrans &rarr;
                </button>
            </div>

        </div>
    </form>

    <!-- Form Pembantu (Update Kuantitas & Hapus) -->
    <form id="inline_update_form" action="" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="quantity" id="inline_update_qty_val">
    </form>

    @foreach($cart as $id => $details)
        <form id="remove_form_{{ $id }}" action="{{ route('cart.remove', $id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

</div>

<script>
    const isPro = @json($isProPartner);
    const proRate = @json($proDiscountPercent) / 100;

    function calculateTotal() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const submitBtn = document.getElementById('checkout_submit_btn');
        const pointsContainer = document.getElementById('points_container');
        const usePointsCheckbox = document.getElementById('use_points_checkbox');
        const rowDiscount = document.getElementById('row_discount');
        const displayDiscount = document.getElementById('display_discount');
        const rowProDiscount = document.getElementById('row_pro_discount');
        const displayProDiscount = document.getElementById('display_pro_discount');
        
        let userPoints = parseInt(document.getElementById('user_points_balance')?.innerText) || 0;
        let rawSubtotal = 0;
        let selectedCount = 0;

        // 1. Hitung Subtotal barang terpilih
        checkboxes.forEach(box => {
            if (box.checked) {
                const id = box.value;
                const price = parseFloat(box.getAttribute('data-price')) || 0;
                const qty = parseInt(document.getElementById('qty_' + id)?.value) || 1;
                rawSubtotal += (price * qty);
                selectedCount++;
            }
        });

        // 2. Hitung Diskon Khusus Mitra PRO (B2B 10%)
        let proDiscountNominal = 0;
        if (isPro && rawSubtotal > 0) {
            proDiscountNominal = Math.round(rawSubtotal * proRate);
            if (rowProDiscount) {
                rowProDiscount.classList.remove('hidden');
                displayProDiscount.innerText = "- " + formatRupiah(proDiscountNominal);
            }
        } else if (rowProDiscount) {
            rowProDiscount.classList.add('hidden');
        }

        let totalAfterPro = Math.max(0, rawSubtotal - proDiscountNominal);
        let finalTotal = totalAfterPro;
        let pointDiskonDiterapkan = 0;

        // 3. Potong dengan Poin Kriya jika dicentang
        if (userPoints > 0 && usePointsCheckbox && usePointsCheckbox.checked && totalAfterPro > 0) {
            pointDiskonDiterapkan = Math.min(userPoints, totalAfterPro);
            finalTotal = totalAfterPro - pointDiskonDiterapkan;

            if (rowDiscount) rowDiscount.classList.remove('hidden');
            if (displayDiscount) displayDiscount.innerText = "- " + formatRupiah(pointDiskonDiterapkan);
        } else {
            if (rowDiscount) rowDiscount.classList.add('hidden');
            if (usePointsCheckbox && totalAfterPro <= 0) usePointsCheckbox.checked = false;
        }

        // 4. Perbarui Teks Angka di Layar
        document.getElementById('display_subtotal').innerText = formatRupiah(rawSubtotal);
        document.getElementById('display_total').innerText = formatRupiah(finalTotal);

        // 5. Manajemen Status Tombol Submit
        if (selectedCount === 0) {
            submitBtn?.setAttribute('disabled', 'disabled');
            if (pointsContainer) pointsContainer.classList.add('hidden');
        } else {
            submitBtn?.removeAttribute('disabled');
            if (pointsContainer && userPoints > 0) pointsContainer.classList.remove('hidden');
        }
    }

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number).replace("Rp", "Rp ");
    }

    function toggleSelectAll(source) {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(box => {
            box.checked = source.checked;
        });
        calculateTotal();
    }

    function updateQuantityInline(input) {
        const productId = input.getAttribute('data-id');
        const quantityValue = input.value;
        if (quantityValue < 1) return;

        const updateForm = document.getElementById('inline_update_form');
        document.getElementById('inline_update_qty_val').value = quantityValue;
        updateForm.action = "/keranjang/update/" + productId;
        updateForm.submit();
    }

    document.addEventListener("DOMContentLoaded", function() {
        calculateTotal();
    });
</script>
@endsection