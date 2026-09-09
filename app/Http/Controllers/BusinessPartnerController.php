<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BusinessSchedule;
use App\Models\Deposit;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class BusinessPartnerController extends Controller
{
    /**
     * Inisialisasi konfigurasi Midtrans
     */
        private function initMidtrans(): void
        {
            Config::$serverKey = config('services.midtrans.server_key') ?? env('MIDTRANS_SERVER_KEY');
            Config::$clientKey = config('services.midtrans.client_key') ?? env('MIDTRANS_CLIENT_KEY');
            Config::$isProduction = (bool) (config('services.midtrans.is_production') ?? env('MIDTRANS_IS_PRODUCTION', false));
            Config::$isSanitized = true;
            Config::$is3ds = true;
        }

    /**
     * Halaman Utama Mitra Bisnis: Otomatis membaca status tahapan pengguna
     */
    public function index()
    {
        $userId = session('user_id') ?? auth()->id();
        if (!$userId) {
            return redirect('/login')->withErrors(['error' => 'Silakan login terlebih dahulu.']);
        }

        $user = User::findOrFail($userId);
        $schedule = BusinessSchedule::where('user_id', $userId)->first();

        // 1. Riwayat penjemputan KHUSUS MITRA BISNIS (deposit_type = 'business')
        $pickupHistory = Deposit::where('user_id', $userId)
            ->where('deposit_type', 'business')
            ->with('penjemput')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // 2. Rekapitulasi limbah KHUSUS MITRA BISNIS
        $wasteStats = Deposit::where('user_id', $userId)
            ->where('deposit_type', 'business')
            ->whereIn('status', ['berhasil_dikirim', 'completed', 'selesai'])
            ->select('category', DB::raw('SUM(actual_weight) as total_weight'), DB::raw('COUNT(*) as total_pickup'))
            ->groupBy('category')
            ->get();

        $totalWeight = $wasteStats->sum('total_weight');

        // 3. Akumulasi Poin khusus yang dihasilkan dari bisnis PRO
        $businessPointsEarned = Deposit::where('user_id', $userId)
            ->where('deposit_type', 'business')
            ->whereIn('status', ['berhasil_dikirim', 'completed', 'selesai'])
            ->sum('points_earned');

        return view('dashboard.business.register', compact(
            'user', 
            'schedule', 
            'pickupHistory', 
            'wasteStats', 
            'totalWeight',
            'businessPointsEarned'
        ));
    }

    /**
     * Tahap 1: Pendaftaran Unit Usaha (Hanya 1 kali per akun)
     */
    public function register(Request $request)
    {
        $userId = session('user_id') ?? auth()->id();
        $user = User::findOrFail($userId);

        // Kunci jika akun sudah pernah mendaftar
        if ($user->business_status && !in_array($user->business_status, ['none', 'rejected'])) {
            return back()->withErrors(['error' => 'Akun Anda sudah memiliki unit bisnis terdaftar.']);
        }

        $request->validate([
            'business_name'     => 'required|string|max:255',
            'business_type'     => 'required|string|max:100',
            'kecamatan'         => 'required|string|max:100',
            'kelurahan'         => 'required|string|max:100',
            'address'           => 'required|string|max:500',
            'business_photo'    => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
            'waste_estimate_kg' => 'required|numeric|min:1',
            'business_notes'    => 'nullable|string|max:500',
        ]);

        $photoPath = $request->file('business_photo')->store('business_photos', 'public');

        $user->update([
            'business_name'        => $request->business_name,
            'business_type'        => $request->business_type,
            'kecamatan'            => $request->kecamatan,
            'kelurahan'            => $request->kelurahan,
            'address'              => $request->address,
            'business_photo_path'  => $photoPath,
            'waste_estimate_kg'    => (int) $request->waste_estimate_kg,
            'business_notes'       => $request->business_notes,
            'business_status'      => 'pending', // Menunggu Admin
            'business_admin_notes' => null,
        ]);

        Notification::notifyAdminNewBusinessPartner($user);

        return redirect()->route('bisnis.index')->with('success', 'Pendaftaran bisnis berhasil dikirim! Menunggu verifikasi admin.');
    }

    /**
     * Tahap 2: Generate Snap Token Midtrans untuk Aktivasi PRO
     */
    public function getSnapToken(Request $request)
    {
        $userId = session('user_id') ?? auth()->id();
        $user = User::findOrFail($userId);

        if ($user->business_status !== 'verified_unpaid') {
            return response()->json(['error' => 'Status pendaftaran belum memenuhi syarat pembayaran.'], 400);
        }

        $this->initMidtrans();

        $orderId = 'PRO-' . $user->id . '-' . time();
        $grossAmount = 67000; // Biaya langganan bulanan

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone ?? '081234567890',
            ],
            'item_details' => [
                [
                    'id'       => 'PRO-MONTHLY',
                    'price'    => $grossAmount,
                    'quantity' => 1,
                    'name'     => 'Langganan SulapaKarya PRO (1 Bulan)',
                ]
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Konfirmasi Pembayaran Berhasil dari Frontend (Setelah Popup Sukses)
     */
    public function paymentSuccess(Request $request)
    {
        $userId = session('user_id') ?? auth()->id();
        $user = User::findOrFail($userId);

        if ($user->business_status === 'verified_unpaid') {
            $user->update([
                'business_status' => 'approved',
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Webhook Notifikasi Otomatis dari Midtrans (Server-to-Server)
     */
    public function handleWebhook(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $signature = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($signature !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $request->transaction_status;
        $orderId = $request->order_id; // Format: PRO-{user_id}-{timestamp}
        $parts = explode('-', $orderId);
        $userId = $parts[1] ?? null;

        if (!$userId) {
            return response()->json(['message' => 'Invalid Order ID format'], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            $user->update([
                'business_status' => 'approved',
            ]);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $user->update([
                'business_status' => 'verified_unpaid',
            ]);
        }

        return response()->json(['message' => 'Notification handled']);
    }

    /**
     * Tahap 3: Simpan Jadwal Rutin
     */
    public function saveSchedule(Request $request)
    {
        $userId = session('user_id') ?? auth()->id();

        $request->validate([
            'pickup_days'    => 'required|array|min:1',
            'pickup_time'    => 'required|string',
            'category_focus' => 'required|string',
            'notes'          => 'nullable|string|max:500',
        ]);

        BusinessSchedule::updateOrCreate(
            ['user_id' => $userId],
            [
                'pickup_days'    => $request->pickup_days,
                'pickup_time'    => $request->pickup_time,
                'category_focus' => $request->category_focus,
                'notes'          => $request->notes,
                'is_active'      => true,
            ]
        );

        return back()->with('success', 'Jadwal rutin berhasil disimpan.');
    }

    /**
     * Jeda / Aktifkan Kembali Jadwal
     */
    public function toggleSchedule()
    {
        $userId = session('user_id') ?? auth()->id();
        $schedule = BusinessSchedule::where('user_id', $userId)->firstOrFail();

        $schedule->update(['is_active' => !$schedule->is_active]);

        $statusText = $schedule->is_active ? 'diaktifkan kembali' : 'dijeda sementara';
        return back()->with('success', "Jadwal penjemputan telah {$statusText}.");
    }

    /**
     * Verifikasi oleh Admin
     */
    public function verifyByAdmin(Request $request, $id)
    {
        $request->validate([
            'decision'             => 'required|in:approved,rejected',
            'business_admin_notes' => 'nullable|string|max:500'
        ]);

        $user = User::findOrFail($id);

        // Jika disetujui admin, arahkan ke tahap pembayaran (verified_unpaid)
        $newStatus = ($request->decision === 'approved') ? 'verified_unpaid' : 'rejected';

        $user->update([
            'business_status'      => $newStatus,
            'business_admin_notes' => $request->business_admin_notes
        ]);

        Notification::notifyBusinessVerification($user, $request->decision);

        return back()->with('success', "Status pengajuan bisnis {$user->business_name} berhasil diperbarui.");
    }


    public function dashboard()
    {
        $userId = session('user_id');
        $user = User::findOrFail($userId);

        // Ambil semua setoran bisnis yang selesai
        $businessDeposits = Deposit::where('user_id', $userId)
            ->where('deposit_type', 'business')
            ->whereIn('status', ['berhasil_dikirim', 'completed', 'selesai'])
            ->get();

        $totalWeight = $businessDeposits->sum('actual_weight') ?: 0;
        $businessPointsEarned = $businessDeposits->sum('points_earned') ?: 0;
        $totalPickups = $businessDeposits->count();

        // Kalkulasi Dampak ESG
        $co2Saved = round($totalWeight * 1.83, 1); // kg CO2e
        $landfillSavedM3 = round($totalWeight * 0.0035, 2); // m3 ruang TPA Tamangapa

        // Rincian per Komoditas Sampah
        $wasteStats = Deposit::where('user_id', $userId)
            ->where('deposit_type', 'business')
            ->whereIn('status', ['berhasil_dikirim', 'completed', 'selesai'])
            ->selectRaw('category, SUM(actual_weight) as total_weight, COUNT(id) as total_pickup')
            ->groupBy('category')
            ->get();

        $schedule = $user->businessSchedule;
        $pickupHistory = Deposit::where('user_id', $userId)
            ->where('deposit_type', 'business')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('dashboard.business.register', compact(
            'user',
            'totalWeight',
            'businessPointsEarned',
            'totalPickups',
            'co2Saved',
            'landfillSavedM3',
            'wasteStats',
            'schedule',
            'pickupHistory'
        ));
    }

    /**
     * Cetak Laporan Sertifikat / Impact Report ESG (Print-friendly view)
     */
    public function exportReport()
    {
        $userId = session('user_id');
        $user = User::findOrFail($userId);

        $businessDeposits = Deposit::where('user_id', $userId)
            ->where('deposit_type', 'business')
            ->whereIn('status', ['berhasil_dikirim', 'completed', 'selesai'])
            ->get();

        $totalWeight = $businessDeposits->sum('actual_weight') ?: 0;
        $co2Saved = round($totalWeight * 1.83, 1);
        $landfillSavedM3 = round($totalWeight * 0.0035, 2);

        $wasteStats = Deposit::where('user_id', $userId)
            ->where('deposit_type', 'business')
            ->whereIn('status', ['berhasil_dikirim', 'completed', 'selesai'])
            ->selectRaw('category, SUM(actual_weight) as total_weight, COUNT(id) as total_pickup')
            ->groupBy('category')
            ->get();

        return view('dashboard.business.report-esg', compact(
            'user',
            'totalWeight',
            'co2Saved',
            'landfillSavedM3',
            'businessDeposits',
            'wasteStats'
        ));
    }
}