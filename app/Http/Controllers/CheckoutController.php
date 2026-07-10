<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// WAJIB DITAMBAHKAN AGAR MIDTRANS TERBACA
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        $userId = session('user_id');

        // =================================================================
        // 🛡️ GERBANG VALIDASI 1: ANTI-SPAM TRANSAKSI MENGGANTUNG (PENDING)
        // =================================================================
        $pendingTransactionExists = Transaction::where('user_id', $userId)
            ->where('status', 'pending')
            ->exists();

        if ($pendingTransactionExists) {
            return redirect()->route('user.pembelian.history')
                ->withErrors(['error' => 'Maaf, Anda tidak dapat melakukan checkout baru. Selesaikan atau batalkan terlebih dahulu pembayaran pesanan Anda yang masih tertunda di bawah ini!']);
        }

        // =================================================================
        // 🛡️ GERBANG VALIDASI 2: VALIDASI INPUT KERANJANG
        // =================================================================
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'use_points' => 'nullable|boolean'
        ]);

        $user = User::findOrFail($userId);
        $cart = session()->get('cart', []);

        // Ambil ID produk yang dikirim dari form keranjang
        $productId = $request->product_id;
        
        // Ambil detail produk dari session cart untuk mendapatkan kuantitas riil
        $cartItem = $cart[$productId] ?? null;
        $quantity = $cartItem ? (int)$cartItem['quantity'] : 1;

        $product = Product::findOrFail($productId);

        if ($product->stock < $quantity) {
            return back()->withErrors(['error' => 'Maaf, stok produk "' . $product->name . '" tidak mencukupi untuk jumlah yang Anda minta.']);
        }

        // =================================================================
        // 🧮 LOGIKA KALKULASI HARGA & POTONGAN DISKON POIN KRIYA
        // =================================================================
        $originalPrice = $product->price * $quantity;
        $pointsUsed = 0;
        $finalPrice = $originalPrice;

        if ($request->has('use_points') && $request->use_points == 1) {
            $pointsUsed = min($originalPrice, $user->points_balance);
            $finalPrice = $originalPrice - $pointsUsed;
        }

        $orderId = 'TRX-ORD-' . strtoupper(Str::random(8));

        // Buat record transaksi dengan menyimpan kuantitas pembelian baru
        $transaction = Transaction::create([
            'user_id'        => $user->id,
            'product_id'     => $product->id,
            'order_id'       => $orderId,
            'original_price' => $originalPrice,
            'points_used'    => $pointsUsed,
            'final_price'    => $finalPrice,
            'status'         => 'pending',
            'quantity'       => $quantity, // Pastikan kolom quantity ini ada di skema tabel transaksi Anda
        ]);

        // JIKA BAYAR FULL PAKAI POIN (Otomatis sukses tanpa panggil API Midtrans)
        if ($finalPrice == 0) {
            DB::transaction(function () use ($user, $product, $transaction, $pointsUsed, $quantity, $productId, $cart) {
                $user->decrement('points_balance', $pointsUsed);
                $product->decrement('stock', $quantity);
                $transaction->update(['status' => 'success']);
                
                // Bersihkan item produk ini dari session keranjang kriya
                unset($cart[$productId]);
                session()->put('cart', $cart);
            });
            return redirect()->route('user.katalog')->with('success', 'Pembayaran berhasil! Anda menukar produk secara penuh menggunakan Poin Kriya.');
        }

        // =================================================================
        // 💳 INTEGRASI TOKEN GERBANG PEMBAYARAN MIDTRANS
        // =================================================================
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false; 
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $finalPrice,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone ?? '0800000000',
            ],
            'item_details' => [
                [
                    'id'       => $product->id,
                    'price'    => $product->price, // Mengirim harga satuan asli ke Midtrans
                    'quantity' => $quantity,       // Jumlah kuantitas riil belanjaan
                    'name'     => substr($product->name, 0, 50)
                ]
            ]
        ];

        // Jika checkbox gunakan poin aktif, masukkan potongan harga sebagai item bernilai minus di invoice Midtrans
        if ($pointsUsed > 0) {
            $params['item_details'][] = [
                'id'       => 'DISC-POIN',
                'price'    => -$pointsUsed,
                'quantity' => 1,
                'name'     => 'Potongan Diskon Poin Kriya'
            ];
        }

        try {
            // Proses mendapatkan Snap Token dari Midtrans
            $snapToken = Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);

            // =================================================================
            // 🔥 PROSES PEMBERSIHAN OTOMATIS (PRODUK LANGSUNG HILANG DARI CART)
            // =================================================================
            unset($cart[$productId]); // Menghapus produk yang baru saja di-checkout
            session()->put('cart', $cart); // Simpan kembali sisa isi keranjang ke session

            return view('checkout', compact('snapToken', 'transaction', 'product'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal terhubung ke Midtrans: ' . $e->getMessage()]);
        }
    }

    public function success(Request $request, $order_id)
    {
        $transaction = Transaction::where('order_id', $order_id)->firstOrFail();
        $quantity = $transaction->quantity ?? 1;
        
        if ($transaction->status == 'pending') {
            DB::transaction(function () use ($transaction, $quantity) {
                $transaction->update(['status' => 'success']);
                
                $user = User::find($transaction->user_id);
                if ($transaction->points_used > 0) {
                    $user->decrement('points_balance', $transaction->points_used);
                }
                
                $product = Product::find($transaction->product_id);
                $product->decrement('stock', $quantity);
            });
        }

        return redirect()->route('user.pembelian.history')->with('success', 'Pembayaran Rp ' . number_format($transaction->final_price, 0, ',', '.') . ' berhasil diverifikasi! Produk segera diproses.');
    }

    /**
     * READ: Menampilkan semua riwayat transaksi pembelian produk milik user
     */
    public function history()
    {
        $transactions = Transaction::with('product')
            ->where('user_id', session('user_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.riwayat-pembelian', compact('transactions'));
    }

    /**
     * RESUME: Melanjutkan pembayaran pending yang belum selesai tanpa buat TRX baru
     */
    public function resume($order_id)
    {
        $transaction = Transaction::where('order_id', $order_id)
            ->where('user_id', session('user_id'))
            ->firstOrFail();

        if ($transaction->status !== 'pending' || !$transaction->snap_token) {
            return redirect()->route('user.pembelian.history')->withErrors(['error' => 'Transaksi ini tidak dapat dilanjutkan.']);
        }

        $product = Product::findOrFail($transaction->product_id);
        $snapToken = $transaction->snap_token;

        return view('checkout', compact('snapToken', 'transaction', 'product'));
    }
}