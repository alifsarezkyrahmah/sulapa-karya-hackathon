@extends('layouts.dashboard', ['title' => 'Keranjang Belanja — SulapaKarya Macca'])

@section('dashboard-content')
<div class="space-y-6 animate-fadeIn text-left">
    
    <div>
        <h1 class="font-display font-extrabold text-2xl text-ink tracking-tight">Keranjang Kriya Anda</h1>
        <p class="text-xs text-ink-soft font-medium mt-1">Kumpulkan produk kerajinan pilihan Anda, pilih produk yang ingin dibeli, lalu selesaikan pembayaran.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-forest/10 border-forest/20 text-forest rounded-2xl text-xs font-bold p-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-error bg-terracotta/10 border-terracotta/20 text-terracotta rounded-2xl text-xs font-bold p-4 shadow-sm">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST" id="main_checkout_form" class="m-0">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            <!-- KOLOM KIRI: DAFTAR BELANJAAN -->
            <div class="lg:col-span-2 bg-white border border-ink/5 rounded-2xl p-6 shadow-sm space-y-4">
                @if(count($cart) > 0)
                    <div class="flex items-center gap-3 pb-3 border-b border-ink/5">
                        <input type="checkbox" id="select_all_products" checked class="checkbox checkbox-sm checkbox-primary rounded" onclick="toggleSelectAll(this)">
                        <label for="select_all_products" class="text-xs font-bold text-ink cursor-pointer">Pilih Semua Produk (Kriya)</label>
                    </div>

                    <div class="divide-y divide-ink/5">
                        @foreach($cart as $id => $details)
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 first:pt-0 last:pb-0 gap-4">
                                
                                <div class="flex items-center gap-4 w-full sm:w-auto">
                                    <input type="checkbox" name="selected_items[]" value="{{ $id }}" checked 
                                           data-price="{{ $details['price'] }}" id="checkbox_{{ $id }}"
                                           class="item-checkbox checkbox checkbox-sm checkbox-primary rounded" 
                                           onclick="calculateTotal()">

                                    <div class="w-16 h-16 bg-sand/30 rounded-xl overflow-hidden border border-ink/5 shrink-0 flex items-center justify-center text-ink-soft/30">
                                        @if(!empty($details['image']))
                                            @if(str_starts_with($details['image'], 'http') || str_starts_with($details['image'], 'https'))
                                                <img src="{{ $details['image'] }}" class="w-full h-full object-cover">
                                            @else
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($details['image']) }}" class="w-full h-full object-cover">
                                            @endif
                                        @else
                                            <span class="text-xs font-black font-mono text-ink-soft/50 bg-sand/50 w-full h-full flex items-center justify-center uppercase">{{ substr($details['name'], 0, 2) }}</span>
                                        @endif
                                    </div>

                                    <div>
                                        <h4 class="font-bold text-ink text-sm sm:text-base leading-snug">{{ $details['name'] }}</h4>
                                        <p class="text-xs font-extrabold text-forest font-mono mt-0.5">Rp {{ number_format($details['price'], 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between w-full sm:w-auto gap-4 pl-8 sm:pl-0">
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs text-ink-soft font-semibold mr-1">Jumlah:</span>
                                        <input type="number" name="quantities[{{ $id }}]" value="{{ $details['quantity'] }}" min="1" 
                                               id="qty_{{ $id }}" data-id="{{ $id }}"
                                               class="cart-qty-input input input-bordered input-sm w-16 text-center font-bold font-mono rounded-lg bg-cream/10 focus:outline-none focus:border-forest"
                                               onchange="updateQuantityInline(this)">
                                    </div>
                                    
                                    <button type="submit" form="remove_form_{{ $id }}" class="btn btn-sm btn-circle btn-ghost text-terracotta hover:bg-terracotta/10" title="Hapus dari keranjang">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center text-ink-soft/50 text-sm font-medium">
                        🛒 Keranjang belanja Anda masih kosong. Yuk, intip kerajinan cantik di <a href="/katalog" class="text-forest font-bold underline">Katalog Kriya</a>!
                    </div>
                @endif
            </div>

            <!-- KOLOM KANAN: RINGKASAN PESANAN -->
            <div class="bg-white border border-ink/5 rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="font-display font-bold text-lg text-ink">Ringkasan Pesanan</h3>
                
                <div class="space-y-2 border-b border-ink/5 pb-4 text-xs font-semibold">
                    <div class="flex justify-between text-ink-soft"><span>Subtotal Produk</span> <span id="display_subtotal" class="text-ink font-bold font-mono">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between text-ink-soft"><span>Simpanan Poin Anda</span> <span class="text-maritime font-bold font-mono"><span id="user_points_balance">{{ ($currentUser->points_balance ?? 0) > 0 ? $currentUser->points_balance : 0 }}</span> Poin</span></div>
                    <div id="row_discount" class="flex justify-between text-terracotta hidden"><span>Potongan Poin Kriya</span> <span id="display_discount" class="font-bold font-mono">- Rp 0</span></div>
                </div>
                
                <!-- 🛡️ PERBAIKAN: Checkbox diskon hanya muncul dan bisa digunakan jika poin di atas 0 -->
                @if(($currentUser->points_balance ?? 0) > 0)
                    <div id="points_container" class="form-control bg-maritime/5 p-3 rounded-xl border border-maritime/10 {{ $totalHarga > 0 ? '' : 'hidden' }}">
                        <label class="label cursor-pointer justify-start gap-3 py-0">
                            <input type="checkbox" name="use_points" value="1" id="use_points_checkbox" class="checkbox checkbox-xs checkbox-primary rounded" onclick="calculateTotal()">
                            <span class="label-text text-xs font-bold text-maritime-dark">Gunakan Poin Kriya untuk Diskon</span>
                        </label>
                        <span class="text-[10px] text-ink-soft/70 mt-1 block pl-7">1 poin memotong harga sebesar Rp 1.</span>
                    </div>
                @endif

                <div class="flex justify-between items-center py-2">
                    <span class="text-sm font-bold text-ink">Total Pembayaran:</span>
                    <span id="display_total" class="text-xl font-black text-forest font-mono">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                </div>

                @if(count($cart) > 0)
                    @php $firstProductId = array_key_first($cart); @endphp
                    <input type="hidden" name="product_id" value="{{ $firstProductId }}">
                @endif

                <button type="submit" id="checkout_submit_btn" {{ count($cart) == 0 ? 'disabled' : '' }} class="btn w-full bg-ink text-white border-none rounded-xl font-extrabold normal-case shadow-md hover:bg-ink-soft transition-colors disabled:bg-gray-200 disabled:text-gray-400">
                    Lanjut Bayar via Midtrans 💳
                </button>
            </div>
        </div>
    </form>

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
    function calculateTotal() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const submitBtn = document.getElementById('checkout_submit_btn');
        const pointsContainer = document.getElementById('points_container');
        const usePointsCheckbox = document.getElementById('use_points_checkbox');
        const rowDiscount = document.getElementById('row_discount');
        const displayDiscount = document.getElementById('display_discount');
        
        let userPoints = parseInt(document.getElementById('user_points_balance').innerText) || 0;
        let currentSubtotal = 0;
        let selectedCount = 0;

        // 1. Hitung Subtotal berdasarkan item yang dicentang warga
        checkboxes.forEach(box => {
            if (box.checked) {
                const id = box.value;
                const price = parseFloat(box.getAttribute('data-price'));
                const qty = parseInt(document.getElementById('qty_' + id).value) || 1;
                currentSubtotal += (price * qty);
                selectedCount++;
            }
        });

        let finalTotal = currentSubtotal;
        let pointDiskonDiterapkan = 0;

        // 2. Jika poin > 0 dan checkbox aktif, lakukan kalkulasi pemotongan
        if (userPoints > 0 && usePointsCheckbox && usePointsCheckbox.checked && currentSubtotal > 0) {
            pointDiskonDiterapkan = Math.min(userPoints, currentSubtotal);
            finalTotal = currentSubtotal - pointDiskonDiterapkan;

            rowDiscount.classList.remove('hidden');
            displayDiscount.innerText = "- " + formatRupiah(pointDiskonDiterapkan);
        } else {
            if(rowDiscount) rowDiscount.classList.add('hidden');
            if(usePointsCheckbox) usePointsCheckbox.checked = false; // Reset status centang jika tidak valid
        }

        // 3. Render Pembaruan Angka ke Layar Panel
        document.getElementById('display_subtotal').innerText = formatRupiah(currentSubtotal);
        document.getElementById('display_total').innerText = formatRupiah(finalTotal);

        // 4. Manajemen Status Tombol Checkout & Container Poin
        if (selectedCount === 0) {
            submitBtn.setAttribute('disabled', 'disabled');
            if(pointsContainer) pointsContainer.classList.add('hidden');
        } else {
            submitBtn.removeAttribute('disabled');
            // Hanya tampilkan wadah poin jika user memang memiliki saldo poin > 0
            if(pointsContainer && userPoints > 0) {
                pointsContainer.classList.remove('hidden');
            } else if (pointsContainer) {
                pointsContainer.classList.add('hidden');
            }
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