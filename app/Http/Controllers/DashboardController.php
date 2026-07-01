<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // <-- Wajib di-import untuk mengambil data riil dari database

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
            return redirect('/scan-qr');
        }

        // Jika rolenya 'user', arahkan ke dashboard user / warga biasa
        return redirect()->route('user.dashboard');
    }

    /**
     * Menampilkan halaman Dashboard khusus Warga / User biasa
     */
    public function userIndex() 
    {
        return view('dashboard.user.index');
    }

    /**
     * Menampilkan halaman Dashboard khusus Pengrajin Kriya
     */
    public function pengrajinIndex() 
    {
        return view('dashboard.pengrajin.index');
    }

    /**
     * Menampilkan halaman Dashboard khusus Admin Utama
     */
    public function adminIndex() 
    {
        return view('dashboard.admin.index');
    }
}