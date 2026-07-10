<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Pastikan nama model produk kriya kamu sesuai

class CartController extends Controller
{
    /**
     * Menampilkan Halaman Keranjang Belanja
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $totalHarga = 0;

        foreach ($cart as $item) {
            $totalHarga += $item['price'] * $item['quantity'];
        }

        // Ambil data user untuk kroscek poin nanti di halaman checkout
        $currentUser = \App\Models\User::find(session('user_id'));

        return view('dashboard.keranjang', compact('cart', 'totalHarga', 'currentUser'));
    }

    /**
     * Menambahkan Produk Kriya ke Dalam Keranjang
     */
/**
     * Menambahkan Produk Kriya ke Dalam Keranjang
     */
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                // SINKRONISASI: Menggunakan 'photo_path' sesuai nama kolom asli di DB kamu!
                "image" => $product->photo_path 
            ];
        }

        session()->put('cart', $cart);
        return back()->with('success', 'Produk berhasil dimasukkan ke keranjang belanja!');
    }

    /**
     * Memperbarui Kuantitas Produk (+ / -) di Halaman Keranjang
     */
    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
            return back()->with('success', 'Jumlah belanjaan berhasil diperbarui!');
        }

        return back()->withErrors(['error' => 'Produk tidak ditemukan di keranjang.']);
    }

    /**
     * Menghapus Satu Item dari Keranjang
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }
}