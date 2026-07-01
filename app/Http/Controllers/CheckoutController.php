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
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'use_points' => 'nullable|boolean'
        ]);

        $user = User::findOrFail(session('user_id'));
        $product = Product::findOrFail($request->product_id);

        if ($product->stock < 1) {
            return back()->withErrors(['error' => 'Maaf, stok produk ini sudah habis.']);
        }

        // LOGIKA POTONGAN HARGA POIN (SHOPEE COINS)
        $originalPrice = $product->price;
        $pointsUsed = 0;
        $finalPrice = $originalPrice;

        if ($request->has('use_points') && $request->use_points == 1) {
            $pointsUsed = min($originalPrice, $user->points_balance);
            $finalPrice = $originalPrice - $pointsUsed;
        }

        $orderId = 'TRX-ORD-' . strtoupper(Str::random(8));

        $transaction = Transaction::create([
            'user_id'        => $user->id,
            'product_id'     => $product->id,
            'order_id'       => $orderId,
            'original_price' => $originalPrice,
            'points_used'    => $pointsUsed,
            'final_price'    => $finalPrice,
            'status'         => 'pending',
        ]);

        // JIKA BAYAR FULL PAKAI POIN (Otomatis sukses tanpa Midtrans)
        if ($finalPrice == 0) {
            DB::transaction(function () use ($user, $product, $transaction, $pointsUsed) {
                $user->decrement('points_balance', $pointsUsed);
                $product->decrement('stock', 1);
                $transaction->update(['status' => 'success']);
            });
            return redirect()->route('user.katalog')->with('success', 'Pembayaran berhasil! Anda menukar produk secara penuh menggunakan Poin Kriya.');
        }

        // JIKA ADA SISA BAYAR -> PANGGIL MIDTRANS (KUNCI MATI DI SANDBOX)
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
                    'price'    => $finalPrice,
                    'quantity' => 1,
                    'name'     => substr($product->name, 0, 50)
                ]
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);
            return view('checkout', compact('snapToken', 'transaction', 'product'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal terhubung ke Midtrans: ' . $e->getMessage()]);
        }
    }

    public function success(Request $request, $order_id)
    {
        $transaction = Transaction::where('order_id', $order_id)->firstOrFail();
        
        if ($transaction->status == 'pending') {
            DB::transaction(function () use ($transaction) {
                $transaction->update(['status' => 'success']);
                
                $user = User::find($transaction->user_id);
                if ($transaction->points_used > 0) {
                    $user->decrement('points_balance', $transaction->points_used);
                }
                
                $product = Product::find($transaction->product_id);
                $product->decrement('stock', 1);
            });
        }

        return redirect()->route('user.katalog')->with('success', 'Pembayaran Rp ' . number_format($transaction->final_price, 0, ',', '.') . ' berhasil diverifikasi! Produk segera diproses.');
    }

    /**
     * READ: Menampilkan semua riwayat transaksi pembelian produk milik user
     */
    public function history()
    {
        // Ambil riwayat belanja beserta relasi produknya
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
