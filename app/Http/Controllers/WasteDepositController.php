<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WasteDeposit;
use App\Models\WastePrice;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WasteDepositController extends Controller
{
    /**
     * Kuota maksimal pengangkutan sampah yang bisa diterima per hari.
     */
    public const DAILY_PICKUP_QUOTA = 10;

    /**
     * Kuota maksimal per slot waktu penjemputan dalam satu hari.
     */
    public const SLOT_PICKUP_QUOTA = 2;

    /**
     * Ambil daftar tanggal (Y-m-d) yang kuotanya sudah penuh (>= DAILY_PICKUP_QUOTA).
     */
    private function getFullPickupDates(): array
    {
        return WasteDeposit::query()
            ->whereNotNull('pickup_date')
            ->whereNotIn('status', ['ditolak', 'rejected'])
            ->whereDate('pickup_date', '>=', date('Y-m-d'))
            ->groupBy('pickup_date')
            ->havingRaw('COUNT(*) >= ?', [self::DAILY_PICKUP_QUOTA])
            ->pluck('pickup_date')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
            ->values()
            ->all();
    }

    /**
     * Peta tanggal -> daftar slot jam (H:i) yang sudah penuh (>= SLOT_PICKUP_QUOTA).
     */
    private function getFullTimeSlots(): array
    {
        $rows = WasteDeposit::query()
            ->whereNotNull('pickup_date')
            ->whereNotNull('pickup_time')
            ->whereNotIn('status', ['ditolak', 'rejected'])
            ->whereDate('pickup_date', '>=', date('Y-m-d'))
            ->selectRaw('pickup_date, pickup_time, COUNT(*) as total')
            ->groupBy('pickup_date', 'pickup_time')
            ->havingRaw('COUNT(*) >= ?', [self::SLOT_PICKUP_QUOTA])
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $date = Carbon::parse($row->pickup_date)->format('Y-m-d');
            $time = Carbon::parse($row->pickup_time)->format('H:i');
            $map[$date][] = $time;
        }

        return $map;
    }

    /**
     * Menampilkan Form Setor Sampah
     */
    public function create()
    {
        $user = auth()->user() ?? User::find(session('user_id'));

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Silakan masuk akun terlebih dahulu.');
        }

        // Ambil kategori sampah langsung dari database tabel waste_prices
        $wastePrices = WastePrice::orderBy('name', 'asc')->get();

        $fullPickupDates = $this->getFullPickupDates();
        $fullTimeSlots = $this->getFullTimeSlots();

        return view('dashboard.setor-sampah', compact(
            'user', 
            'wastePrices', 
            'fullPickupDates', 
            'fullTimeSlots'
        ));
    }

    /**
     * Menyimpan Pengajuan Setor Sampah Baru
     */
/**
     * Menyimpan Pengajuan Setor Sampah Baru
     */
    public function store(Request $request)
    {
        $user = auth()->user() ?? User::findOrFail(session('user_id'));

        $request->validate([
            'category'         => 'required|string|max:100',
            'sub_category'     => 'nullable|string|max:255',
            'estimated_weight' => 'required|numeric|min:0.1',
            'photo'            => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
            'kecamatan'        => 'required|string|max:100',
            'kelurahan'        => 'required|string|max:100',
            'pickup_address'   => 'required|string',
            'pickup_date'      => 'required|date|after:today',
            'pickup_time'      => 'required|string',
        ], [
            'pickup_date.after'         => 'Penjemputan paling cepat H+1 (besok hari).',
            'category.required'         => 'Kategori sampah wajib dipilih.',
            'estimated_weight.required' => 'Perkiraan berat wajib diisi.',
            'photo.required'            => 'Foto fisik sampah wajib diunggah.',
            'photo.max'                 => 'Ukuran foto maksimal 3MB.',
        ]);

        // Cek Kuota Harian
        $bookedCount = WasteDeposit::whereDate('pickup_date', $request->pickup_date)
            ->whereNotIn('status', ['ditolak', 'rejected'])
            ->count();

        if ($bookedCount >= self::DAILY_PICKUP_QUOTA) {
            return back()->withErrors(['pickup_date' => 'Kuota penjemputan pada tanggal tersebut sudah penuh. Silakan pilih hari lain.'])->withInput();
        }

        // Cek Kuota Slot Jam
        $slotCount = WasteDeposit::whereDate('pickup_date', $request->pickup_date)
            ->where('pickup_time', $request->pickup_time)
            ->whereNotIn('status', ['ditolak', 'rejected'])
            ->count();

        if ($slotCount >= self::SLOT_PICKUP_QUOTA) {
            return back()->withErrors(['pickup_time' => 'Slot waktu tersebut sudah penuh. Silakan pilih jam lainnya.'])->withInput();
        }

        // Upload Foto
        $photoPath = $request->file('photo')->store('waste_photos', 'public');

        // Buat Kode Unik Tunggal
        $depositCode = 'TRX-' . strtoupper(Str::random(6));

        // Tentukan jenis setoran (Personal vs Bisnis PRO)
        $depositType = ($user->business_status === 'approved' && $request->input('is_business_pickup') == '1')
            ? 'business'
            : 'personal';

        // Simpan HANYA SATU KALI ke tabel database
        $deposit = WasteDeposit::create([
            'user_id'          => $user->id,
            'deposit_type'     => $depositType,
            'deposit_code'     => $depositCode,
            'category'         => $request->category,
            'sub_category'     => $request->sub_category,
            'estimated_weight' => $request->estimated_weight,
            'photo_path'       => $photoPath,
            'reward_type'      => 'points',
            'status'           => 'pending',
            'kecamatan'        => $request->kecamatan,
            'kelurahan'        => $request->kelurahan,
            'pickup_address'   => $request->pickup_address,
            'pickup_date'      => $request->pickup_date,
            'pickup_time'      => $request->pickup_time,
            'is_clean'         => false,
            'is_dry'           => false,
            'is_compact'       => false,
        ]);

        // Kirim Notifikasi ke admin
        Notification::notifyAdminNewDeposit($deposit);

        return redirect()->route('setor-sampah.history')->with('success', 'Pengajuan setor sampah (' . $depositCode . ') berhasil dikirim! Kurir kami akan segera ditugaskan.');
    }

    /**
     * Riwayat Setoran
     */
    public function history()
    {
        $currentUser = auth()->user() ?? User::find(session('user_id'));
        $isAdmin = $currentUser && $currentUser->role === 'admin';

        $query = WasteDeposit::with(['user', 'pointTransfer'])->orderBy('created_at', 'desc');

        if (!$isAdmin) {
            $query->where('user_id', session('user_id'));
        }

        $deposits = $query->get();

        return view('dashboard.riwayat-setoran', compact('deposits', 'isAdmin'));
    }
}