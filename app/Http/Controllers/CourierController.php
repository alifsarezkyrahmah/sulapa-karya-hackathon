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

            if ($deposit->deposit_code !== $request->qr_code_warga) {
                return back()->withErrors(['error' => 'Kode QR tidak cocok! Pastikan Anda memindai QR Code setoran yang benar dari aplikasi warga.']);
            }

            $kategori = strtolower($deposit->category);
            $subKategori = $deposit->sub_category;
            $konversi = config('sulapakarya.point_conversion');

            $poinPerKg = 400;
            if (isset($konversi[$kategori][$subKategori])) {
                $poinPerKg = $konversi[$kategori][$subKategori];
            } elseif (isset($konversi[$kategori])) {
                $poinPerKg = (int) round(array_sum($konversi[$kategori]) / count($konversi[$kategori]));
            }

            $totalPoinDihasilkan = (int) round($request->actual_weight * $poinPerKg);

            $deposit->update([
                'status'        => 'selesai',
                'actual_weight' => $request->actual_weight,
                'points_earned' => $totalPoinDihasilkan,
            ]);

            PointTransfer::create([
                'sender_id'        => session('user_id'),
                'receiver_id'      => $warga->id,
                'amount'           => $totalPoinDihasilkan,
                'note'             => "Poin dari Setoran Sampah " . ucfirst($deposit->category) . " (" . $request->actual_weight . " Kg)",
                'reference_number' => 'TRF-' . strtoupper(Str::random(10)),
                'status'           => 'pending',
                'deposit_id'       => $deposit->id,
            ]);

            return back()->with('success', 'Sampah berhasil diambil! ' . number_format($totalPoinDihasilkan) . ' poin menunggu persetujuan admin sebelum masuk ke dompet warga.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memproses transaksi: ' . $e->getMessage()]);
        }
    }
}