<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\User;
use App\Models\PointTransfer;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CourierController extends Controller
{
    /**
     * Dashboard Utama Penjemput
     */
    public function index()
    {
        $courierId = session('user_id');

        // 1. Ambil daftar tugas jemput yang didelegasikan Admin ke dia (selain yang berstatus selesai/ditolak)
        $activeTasks = Deposit::where('penjemput_id', $courierId)
            ->whereIn('status', ['menunggu_penjemput', 'penjemput_menuju_lokasi', 'penjemput_tiba'])
            ->orderBy('created_at', 'asc')
            ->get();

        // 2. Ambil riwayat penyelesaian setoran hari ini
        $completedLogs = Deposit::where('penjemput_id', $courierId)
            ->where('status', 'selesai')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('dashboard.penjemput.index', compact('activeTasks', 'completedLogs'));
    }

    /**
     * Mengubah Status Perjalanan di Lapangan
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:penjemput_menuju_lokasi,penjemput_tiba']);
        
        try {
            $deposit = Deposit::findOrFail($id);
            $deposit->update(['status' => $request->status]);

            $pesan = $request->status === 'penjemput_menuju_lokasi' 
                ? 'Status: Anda sedang menuju ke lokasi warga!' 
                : 'Status: Anda telah tiba di lokasi! Silakan lakukan penimbangan.';

            return back()->with('success', $pesan);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengubah status: ' . $e->getMessage()]);
        }
    }

    /**
     * Finalisasi Timbangan Fisik, Validasi Akun Warga, & Distribusi Poin Otomatis
     */
    public function completeTransaction(Request $request, $id)
    {
        $request->validate([
            'actual_weight' => 'required|numeric|min:0.1',
            'qr_code_warga' => 'required|string',
        ]);

        try {
            $deposit = Deposit::findOrFail($id);
            $warga = User::findOrFail($deposit->user_id);

            // Validasi Keamanan: Pastikan QR Code yang disecan kurir cocok dengan pemilik setoran
            if ($warga->qr_code !== $request->qr_code_warga) {
                return back()->withErrors(['error' => 'Kode QR tidak cocok! Pastikan Anda memindai QR Code dari aplikasi warga yang bersangkutan.']);
            }

            // --- STRATEGI LOGIKA KALKULASI POIN KRIYA ---
            $kategori = strtolower($deposit->category);
            $opsiHarga = [
                'plastik' => 2500,
                'kertas'  => 1500,
                'kardus'  => 2000,
                'kain'    => 4000,
                'logam'   => 8000,
                'kaca'    => 1000
            ];
            $pengaliPoin = $opsiHarga[$kategori] ?? 1000;
            $totalPoinDihasilkan = round($request->actual_weight * $pengaliPoin);

            // 1. Update Tabel `deposits` menjadi Selesai
            $deposit->update([
                'status'        => 'selesai',
                'actual_weight' => $request->actual_weight,
                'points_earned' => $totalPoinDihasilkan,
            ]);

            // 2. Mutasi Saldo: Tambahkan Poin ke akun Dompet Digital Warga
            $warga->increment('points_balance', $totalPoinDihasilkan);

            // 3. Catat Riwayat Mutasi ke Buku Kas Tabel `point_transfers` Supabase
            PointTransfer::create([
                'sender_id'        => session('user_id'), // Kurir
                'receiver_id'      => $warga->id, // Warga
                'amount'           => $totalPoinDihasilkan,
                'note'             => "Poin Cair dari Setoran Sampah " . ucfirst($deposit->category) . " (" . $request->actual_weight . " Kg)",
                'reference_number' => 'TRF-' . strtoupper(Str::random(10)),
            ]);

            return back()->with('success', 'Transaksi Sukses! Berat divalidasi dan 🌟' . number_format($totalPoinDihasilkan) . ' poin telah ditransfer ke dompet warga.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memproses transaksi: ' . $e->getMessage()]);
        }
    }
}