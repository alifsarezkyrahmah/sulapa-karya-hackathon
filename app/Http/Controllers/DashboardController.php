<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Fungsi Pengarah: Mengatur ke mana user harus pergi setelah sukses login
     */
    public function redirect()
    {
        $role = session('role');

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'pengrajin') {
            return redirect()->route('pengrajin.dashboard');
        }

        // Jika rolenya 'user', arahkan ke dashboard user
        return redirect()->route('user.dashboard');
    }

    // View Dashboard User (Ubah dari Warga)
    public function userIndex() {
        return view('dashboard.user.index');
    }

    // View Dashboard Pengrajin
    public function pengrajinIndex() {
        return view('dashboard.pengrajin.index');
    }

    // View Dashboard Admin
    public function adminIndex() {
        return view('dashboard.admin.index');
    }
}