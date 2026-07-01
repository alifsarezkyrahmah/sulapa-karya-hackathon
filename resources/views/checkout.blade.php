@extends('layouts.app', ['title' => 'Selesaikan Pembayaran — SulapaKarya'])

@section('content')

<!-- DIKUNCI PERMANEN KE LINK SANDBOX -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

<div class="max-w-xl mx-auto py-16 px-6 text-center space-y-6 animate-fadeIn min-h-[60vh] flex flex-col items-center justify-center">
    
    <div class="w-16 h-16 bg-forest/10 text-forest rounded-full flex items-center justify-center mx-auto mb-2 shadow-inner">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
    </div>

    <h2 class="font-display font-extrabold text-2xl text-ink">Selesaikan Pembayaran</h2>
    <p class="text-sm text-ink-soft">Silakan selesaikan pembayaran untuk <b>{{ $product->name }}</b> senilai <b class="text-forest">Rp {{ number_format($transaction->final_price, 0, ',', '.') }}</b>.</p>
    
    <button id="pay-button" class="btn w-full bg-forest border-none text-white hover:bg-forest-dark rounded-xl font-extrabold normal-case shadow-lg shadow-forest/30 h-14 transition-all">
        Buka Kasir Pembayaran
    </button>

    <span class="text-[10px] bg-terracotta/10 text-terracotta px-3 py-1.5 rounded-lg font-bold mt-2 inline-block">
        Mode Sandbox (Uji Coba) Terkunci Aktif
    </span>
</div>

<script>
    document.getElementById('pay-button').onclick = function () {
        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result){
                window.location.href = "{{ url('/checkout/success') }}/" + '{{ $transaction->order_id }}';
            },
            onPending: function(result){
                alert("Mohon selesaikan pembayaran sesuai instruksi bank/merchant pilihan Anda.");
                window.location.href = "{{ route('user.katalog') }}";
            },
            onError: function(result){
                alert("Pembayaran gagal atau dibatalkan!");
                window.location.href = "{{ route('user.katalog') }}";
            },
            onClose: function(){
                alert('Anda menutup layar kasir sebelum menyelesaikan pembayaran.');
            }
        });
    };
    
    // Otomatis terbuka dalam 0.5 detik
    setTimeout(() => { document.getElementById('pay-button').click(); }, 500);
</script>
@endsection