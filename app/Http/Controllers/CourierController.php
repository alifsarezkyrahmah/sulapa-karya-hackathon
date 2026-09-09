<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\User;
use App\Models\WastePrice;
use App\Models\Notification;
use Carbon\Carbon;

class CourierController extends Controller
{
    /**
     * Halaman 1: Misi Penjemputan Berfokus (Dashboard Kurir)
     */
   /**
     * Halaman 1: Misi Penjemputan Berfokus (Dashboard Kurir)
     */
    // public function index()
    // {
    //     $courierId = session('user_id');

    //     // Ambil semua tugas yang ditugaskan ke kurir ini atau yang belum ada kurir (kolam tugas)
    //     $activeTasks = Deposit::with('user')
    //         ->where(function ($query) use ($courierId) {
    //             $query->where('penjemput_id', $courierId)
    //                   ->orWhereNull('penjemput_id');
    //         })
    //         ->whereIn('status', ['menunggu_penjemput', 'penjemput_menuju_lokasi', 'penjemput_tiba'])
    //         ->orderByRaw("CASE WHEN deposit_type = 'business' THEN 0 ELSE 1 END") // Prioritas B2B PRO
    //         ->orderBy('pickup_time', 'asc')
    //         ->get();

    //     // Riwayat singkat hari ini
    //     $completedLogs = Deposit::with('user')
    //         ->where('penjemput_id', $courierId)
    //         ->whereIn('status', ['sedang_diproses', 'berhasil_dikirim', 'completed', 'selesai', 'ditolak', 'ditolak_qc', 'rejected'])
    //         ->whereDate('updated_at', Carbon::today())
    //         ->orderBy('updated_at', 'desc')
    //         ->take(5)
    //         ->get();

    //     return view('dashboard.penjemput.index', compact('activeTasks', 'completedLogs'));
    // }

    public function index()
    {
        $courierId = session('user_id');

        // Urutkan: 1. Tugas PRO B2B dulu, 2. Jam penjemputan lebih awal
        $activeTasks = Deposit::with('user')
            ->where(function ($query) use ($courierId) {
                $query->where('penjemput_id', $courierId)
                      ->orWhereNull('penjemput_id');
            })
            ->whereIn('status', ['menunggu_penjemput', 'penjemput_menuju_lokasi', 'penjemput_tiba'])
            ->orderByRaw("CASE WHEN deposit_type = 'business' THEN 0 ELSE 1 END") // Prioritas 1: PRO
            ->orderBy('pickup_time', 'asc') // Prioritas 2: Jam
            ->get();

        $completedLogs = Deposit::with('user')
            ->where('penjemput_id', $courierId)
            ->whereIn('status', ['sedang_diproses', 'berhasil_dikirim', 'completed', 'selesai', 'ditolak', 'ditolak_qc', 'rejected'])
            ->whereDate('updated_at', Carbon::today())
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.penjemput.index', compact('activeTasks', 'completedLogs'));
    }


    /**
     * Kurir mengklaim tiket tugas penjemputan (Personal maupun PRO)
     */
    // public function claimTask($id)
    // {
    //     $courierId = session('user_id');

    //     $deposit = Deposit::where('id', $id)
    //         ->where('status', 'menunggu_penjemput')
    //         ->whereNull('penjemput_id')
    //         ->first();

    //     if (!$deposit) {
    //         return back()->withErrors(['error' => 'Misi ini sudah diambil oleh kurir lain atau tidak tersedia.']);
    //     }

    //     $deposit->update([
    //         'penjemput_id' => $courierId,
    //         'status'       => 'penjemput_menuju_lokasi'
    //     ]);

    //     Notification::notifyDeposit($deposit);

    //     return back()->with('success', 'Misi penjemputan berhasil diambil! Silakan menuju lokasi.');
    // }

    public const MAX_ACTIVE_TASKS = 3;

    public function claimTask($id)
    {
        $courierId = session('user_id');

        // 1. CEK BATAS BEBAN KERJA AKTIF
        $currentLoad = Deposit::where('penjemput_id', $courierId)
            ->whereIn('status', ['menunggu_penjemput', 'penjemput_menuju_lokasi', 'penjemput_tiba'])
            ->count();

        if ($currentLoad >= self::MAX_ACTIVE_TASKS) {
            return back()->withErrors([
                'error' => 'Batas maksimal ' . self::MAX_ACTIVE_TASKS . ' misi aktif tercapai. Selesaikan atau timbang sampah misi yang ada sebelum mengambil order baru.'
            ]);
        }

        // 2. KUNCI TIKET PENJEMPUTAN
        $deposit = Deposit::where('id', $id)
            ->where('status', 'menunggu_penjemput')
            ->whereNull('penjemput_id')
            ->first();

        if (!$deposit) {
            return back()->withErrors(['error' => 'Misi ini sudah diambil oleh kurir lain atau tidak tersedia.']);
        }

        $deposit->update([
            'penjemput_id' => $courierId,
            'status'       => 'penjemput_menuju_lokasi'
        ]);

        Notification::notifyDeposit($deposit);

        return back()->with('success', 'Misi penjemputan berhasil diambil! Silakan menuju lokasi.');
    }

    /**
     * Update Status Perjalanan Kurir (Mulai Jalan / Tiba di Lokasi)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:penjemput_menuju_lokasi,penjemput_tiba',
        ]);

        $deposit = Deposit::findOrFail($id);
        $deposit->update([
            'status'       => $request->status,
            'penjemput_id' => session('user_id'),
        ]);

        // Notifikasi ke warga mengenai perkembangan perjalanan kurir
        Notification::notifyDeposit($deposit);

        $message = $request->status === 'penjemput_menuju_lokasi'
            ? 'Perjalanan dimulai. Menuju alamat penjemputan.'
            : 'Status diperbarui: Anda telah tiba di lokasi penjemputan.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Selesaikan Transaksi: Lolos QC (Timbang + Poin Pending) ATAU Tolak QC
     */
    public function completeTransaction(Request $request, $id)
    {
        $deposit = Deposit::findOrFail($id);
        $courierId = session('user_id');

        // SKENARIO 1: TOLAK SETORAN KARENA TIDAK LAYAK QC
        if ($request->input('qc_action') === 'reject') {
            $request->validate([
                'reject_reason' => 'required|string',
                'reject_notes'  => 'nullable|string|max:500',
            ]);

            $deposit->update([
                'status'        => 'ditolak',
                'penjemput_id'  => $courierId,
                'points_earned' => 0,
                'qc_notes'      => 'Ditolak: ' . $request->reject_reason . ($request->reject_notes ? ' — ' . $request->reject_notes : ''),
            ]);

            // Kirim notifikasi ke pemilik bahwa setoran ditolak QC
            Notification::notifyDeposit($deposit);

            return redirect()->back()->with('success', 'Setoran berhasil ditolak sesuai SOP QC. Notifikasi edukasi dikirim ke pengguna.');
        }

        // SKENARIO 2: LOLOS QC (VALIDASI TUNGGAL DARI KURIR)
        $request->validate([
            'actual_weight' => 'required|numeric|min:0.01',
            'qr_code_warga' => 'required|string',
            'is_qc_passed'  => 'required|accepted',
        ], [
            'actual_weight.required' => 'Berat timbangan aktual wajib diisi.',
            'qr_code_warga.required' => 'Pindai atau masukkan kode QR pengguna.',
            'is_qc_passed.accepted'  => 'Anda wajib mengonfirmasi bahwa sampah telah lolos uji kelayakan QC.',
        ]);

        // Verifikasi kesesuaian Kode/QR Setoran
        if ($deposit->deposit_code && trim($request->qr_code_warga) !== trim($deposit->deposit_code)) {
            return redirect()->back()->withErrors(['qr_code_warga' => 'Kode QR setoran tidak cocok dengan data pengguna ini.'])->withInput();
        }

        // Hitung estimasi perolehan poin dari master tarif sampah
        $wastePrice = WastePrice::where('name', $deposit->category)
            ->orWhere('name', $deposit->sub_category)
            ->first();
            
        $pointPerKg = $wastePrice ? ($wastePrice->point_per_kg ?? 0) : 400;
        $calculatedPoints = round($request->actual_weight * $pointPerKg);

        // Update status ke 'sedang_diproses' (menunggu audit final admin)
        $deposit->update([
            'actual_weight' => $request->actual_weight,
            'points_earned' => $calculatedPoints,
            'is_clean'      => true,
            'is_dry'        => true,
            'is_compact'    => true,
            'status'        => 'sedang_diproses',
            'penjemput_id'  => $courierId,
        ]);

        // 1. Beritahu pengguna bahwa sampah sudah ditimbang dan masuk tahap audit admin
        Notification::notifyDeposit($deposit);

        // 2. Beritahu SEMUA ADMIN bahwa setoran siap diaudit poinnya
        Notification::notifyAdminPendingPoints($deposit);

        $tipeLabel = ($deposit->deposit_type === 'business') ? ' [Mitra PRO]' : ' [Warga]';
        return redirect()->back()->with('success', 'Verifikasi QC & penimbangan' . $tipeLabel . ' berhasil disimpan! Menunggu audit poin admin.');
    }

    /**
     * Halaman 2: Riwayat & Log Setoran Lengkap Kurir
     */
    public function history(Request $request)
    {
        $courierId = session('user_id');

        $query = Deposit::with('user')
            ->where('penjemput_id', $courierId)
            ->whereIn('status', ['sedang_diproses', 'berhasil_dikirim', 'completed', 'selesai', 'ditolak', 'ditolak_qc', 'rejected']);

        // Filter Status
        if ($request->filled('status')) {
            if ($request->status === 'success') {
                $query->whereIn('status', ['berhasil_dikirim', 'completed', 'selesai']);
            } elseif ($request->status === 'processing') {
                $query->where('status', 'sedang_diproses');
            } elseif ($request->status === 'rejected') {
                $query->whereIn('status', ['ditolak', 'ditolak_qc', 'rejected']);
            }
        }

        // Filter Tipe Setoran (Personal vs Bisnis)
        if ($request->filled('deposit_type')) {
            $query->where('deposit_type', $request->deposit_type);
        }

        // Filter Pencarian Kode / Nama Warga / Nama Bisnis
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('deposit_code', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'ilike', "%{$search}%")
                        ->orWhere('business_name', 'ilike', "%{$search}%");
                  });
            });
        }

        $allLogs = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        // Statistik Keseluruhan Kurir Ini
        $totalWeightCollected = Deposit::where('penjemput_id', $courierId)
            ->whereIn('status', ['sedang_diproses', 'berhasil_dikirim', 'completed', 'selesai'])
            ->sum('actual_weight');

        $totalPointsGiven = Deposit::where('penjemput_id', $courierId)
            ->whereIn('status', ['berhasil_dikirim', 'completed', 'selesai'])
            ->sum('points_earned');

        $totalSuccessCount = Deposit::where('penjemput_id', $courierId)
            ->whereIn('status', ['sedang_diproses', 'berhasil_dikirim', 'completed', 'selesai'])
            ->count();

        $totalRejectedCount = Deposit::where('penjemput_id', $courierId)
            ->whereIn('status', ['ditolak', 'ditolak_qc', 'rejected'])
            ->count();

        return view('dashboard.penjemput.riwayat-kurir', compact(
            'allLogs',
            'totalWeightCollected',
            'totalPointsGiven',
            'totalSuccessCount',
            'totalRejectedCount'
        ));
    }
}