<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User; // <-- Wajib di-import untuk mengambil data riil dari database
use App\Models\PointTransfer;
class DashboardController extends Controller
{
    /**
     * Fungsi Pengarah: Mengatur ke mana pengguna harus diarahkan setelah sukses login.
     * Menggunakan Real-time DB Check agar sinkron saat role diubah oleh Admin.
     */
    public function redirect()
    {
        // 1. Cari data pengguna segar langsung dari database berdasarkan ID di session
        $user = User::find(session('user_id'));

        // 2. Gunakan role dari database. Jika data bermasalah, gunakan session lama sebagai cadangan
        $role = $user ? $user->role : session('role');

        // 3. Arahkan pengguna ke dashboard masing-masing sesuai dengan role aslinya
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'pengrajin') {
            return redirect()->route('pengrajin.dashboard');
        } elseif ($role === 'penjemput') {
            // Mengarahkan kurir/penjemput ke halaman scan QR kriya warga
            return redirect()->route('penjemput.dashboard');
        }

        // Jika rolenya 'user', arahkan ke dashboard user / warga biasa
        return redirect()->route('user.dashboard');
    }

    /**
     * Menampilkan halaman Dashboard khusus Warga / User biasa
     */
    public function userIndex() 
        {
            $userId = session('user_id');

            // 1. Tarik data profil warga secara real-time dari database
            $currentUser = \App\Models\User::find($userId);

            // 2. Hitung total akumulasi berat sampah (Kg) yang statusnya 'selesai' milik user ini
            $totalWeight = \App\Models\Deposit::where('user_id', $userId)
                                ->where('status', 'selesai')
                                ->sum('actual_weight');

            // 3. Tarik riwayat poin masuk (approved) dan pending
            $pointHistory = \App\Models\PointTransfer::where('receiver_id', $userId)
                                ->whereIn('status', ['approved', 'pending'])
                                ->orderBy('created_at', 'desc')
                                ->take(10)
                                ->get();


            // Tambahkan penarikan data ini di fungsi userIndex() milik DashboardController
            $buyHistory = \App\Models\Transaction::where('user_id', $userId)
                            ->where('status', 'success')
                            ->where('points_used', '>', 0)
                            ->orderBy('created_at', 'desc')
                            ->get();

            return view('dashboard.user.index', compact('pointHistory', 'buyHistory', 'totalWeight', 'currentUser'));
        }

    /**
     * Menampilkan halaman Dashboard khusus Pengrajin Kriya
     */
    public function penjemputIndex() 
    {
        return view('dashboard.penjemput.index');
    }

    /**
     * Menampilkan halaman Dashboard khusus Admin Utama
     */
    public function adminIndex() 
    {
        return view('dashboard.admin.index');
    }
}