<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WasteDeposit;
use App\Models\User;
use Illuminate\Support\Str;

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
     * Hanya menghitung tanggal mulai hari ini ke depan dan mengabaikan setoran yang ditolak.
     */
    private function getFullPickupDates(): array
    {
        return WasteDeposit::query()
            ->whereNotNull('pickup_date')
            ->where('status', '!=', 'ditolak')
            ->whereDate('pickup_date', '>=', date('Y-m-d'))
            ->groupBy('pickup_date')
            ->havingRaw('COUNT(*) >= ?', [self::DAILY_PICKUP_QUOTA])
            ->pluck('pickup_date')
            ->map(fn ($date) => \Illuminate\Support\Carbon::parse($date)->format('Y-m-d'))
            ->values()
            ->all();
    }

    /**
     * Peta tanggal -> daftar slot jam (H:i) yang sudah penuh (>= SLOT_PICKUP_QUOTA).
     * Dipakai kalender/tombol slot untuk menonaktifkan jam yang tidak tersedia.
     * Contoh hasil: ['2026-07-28' => ['13:00', '15:00'], ...]
     */
    private function getFullTimeSlots(): array
    {
        $rows = WasteDeposit::query()
            ->whereNotNull('pickup_date')
            ->whereNotNull('pickup_time')
            ->where('status', '!=', 'ditolak')
            ->whereDate('pickup_date', '>=', date('Y-m-d'))
            ->selectRaw('pickup_date, pickup_time, COUNT(*) as total')
            ->groupBy('pickup_date', 'pickup_time')
            ->havingRaw('COUNT(*) >= ?', [self::SLOT_PICKUP_QUOTA])
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $date = \Illuminate\Support\Carbon::parse($row->pickup_date)->format('Y-m-d');
            $time = \Illuminate\Support\Carbon::parse($row->pickup_time)->format('H:i');
            $map[$date][] = $time;
        }

        return $map;
    }

    public function create()
    {
        // 1. Ambil data User untuk auto-fill Alamat
        $user = User::find(session('user_id'));

        $categories = [
            (object)['id' => 'plastik',    'nama' => 'Plastik (Botol, Gelas, Kemasan)'],
            (object)['id' => 'kertas',     'nama' => 'Kertas (HVS, Kardus, Koran)'],
            (object)['id' => 'kain',       'nama' => 'Kain (Pakaian Bekas, Perca)'],
            (object)['id' => 'logam',      'nama' => 'Logam (Kaleng, Besi, Tembaga)'],
            (object)['id' => 'kaca',       'nama' => 'Kaca (Botol Kaca)'],
            (object)['id' => 'elektronik', 'nama' => 'Elektronik (E-Waste)'],
        ];

        $subCategories = config('sulapakarya.point_conversion');

        // 3. Tanggal yang sudah penuh (kuota 10) — untuk dinonaktifkan di kalender
        $fullPickupDates = $this->getFullPickupDates();

        // 4. Slot jam yang sudah penuh (kuota 2 per slot per hari) — untuk dinonaktifkan
        $fullTimeSlots = $this->getFullTimeSlots();

        return view('dashboard.setor-sampah', compact('user', 'categories', 'subCategories', 'fullPickupDates', 'fullTimeSlots'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category'         => 'required|in:plastik,kertas,kain,logam,kaca,elektronik',
            'sub_category'     => 'nullable|string|max:255',
            'estimated_weight' => 'required|numeric|min:0.1',
            'reward_type'      => 'required|in:cash,points',
            'kecamatan'        => 'required|string|max:100',
            'kelurahan'        => 'required|string|max:100',
            'pickup_address'   => 'required|string',
            'pickup_date'      => 'nullable|date|after:today',
            'pickup_time'      => 'nullable|in:08:00,09:00,10:00,11:00,12:00,13:00,14:00,15:00,16:00',
            'photo'            => 'required|image|mimes:jpeg,png,jpg,webp|max:3048',
        ], [
            'pickup_date.after'          => 'Penjemputan paling cepat besok hari, tidak bisa memilih hari ini atau hari yang sudah lewat.',
            'pickup_time.in'             => 'Waktu penjemputan tidak valid, silakan pilih slot jam yang tersedia.',
            'category.required'          => 'Kategori sampah wajib dipilih.',
            'estimated_weight.required'  => 'Perkiraan berat wajib diisi.',
            'photo.required'             => 'Foto bukti sampah wajib diunggah.'
        ]);

        // Cek kuota harian: satu hari maksimal DAILY_PICKUP_QUOTA pengangkutan.
        if ($request->filled('pickup_date')) {
            $bookedCount = WasteDeposit::whereDate('pickup_date', $request->pickup_date)
                ->where('status', '!=', 'ditolak')
                ->count();

            if ($bookedCount >= self::DAILY_PICKUP_QUOTA) {
                return back()
                    ->withErrors(['pickup_date' => 'Pengantaran penuh, tolong pilih hari lain.'])
                    ->withInput();
            }
        }

        // Cek kuota slot jam: satu slot maksimal SLOT_PICKUP_QUOTA pengangkutan per hari.
        if ($request->filled('pickup_date') && $request->filled('pickup_time')) {
            $slotCount = WasteDeposit::whereDate('pickup_date', $request->pickup_date)
                ->where('pickup_time', $request->pickup_time)
                ->where('status', '!=', 'ditolak')
                ->count();

            if ($slotCount >= self::SLOT_PICKUP_QUOTA) {
                return back()
                    ->withErrors(['pickup_time' => 'Silahkan pilih waktu penjemputan lain.'])
                    ->withInput();
            }
        }

        try {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('waste_photos', 'public');
            }

            $depositCode = 'TRX-' . strtoupper(substr(uniqid(), -6));

            $fullAddress = $request->pickup_address . ', Kel. ' . $request->kelurahan . ', Kec. ' . $request->kecamatan . ', Makassar';

            WasteDeposit::create([
                'user_id'          => session('user_id'),
                'deposit_code'     => $depositCode,
                'category'         => $request->category,
                'sub_category'     => $request->sub_category,
                'estimated_weight' => $request->estimated_weight,
                'photo_path'       => $photoPath,
                'reward_type'      => $request->reward_type,
                'status'           => 'pending',
                'pickup_address'   => $fullAddress,
                'kecamatan'        => $request->kecamatan,
                'kelurahan'        => $request->kelurahan,
                'pickup_date'      => $request->pickup_date,
                'pickup_time'      => $request->pickup_time,
            ]);

            return back()->with('success', 'Berhasil! Setoran sampah Anda dengan kode ' . $depositCode . ' sedang menunggu penjemputan.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengirim setoran: ' . $e->getMessage()])->withInput();
        }
    }

    public function history()
{
    $currentUser = User::find(session('user_id'));
    $isAdmin = $currentUser && $currentUser->role === 'admin';

    // Admin melihat SEMUA riwayat setoran warga; user biasa hanya miliknya sendiri.
    $query = \App\Models\Deposit::with(['user', 'pointTransfer'])->orderBy('created_at', 'desc');

    if (!$isAdmin) {
        $query->where('user_id', session('user_id'));
    }

    $deposits = $query->get();

    return view('dashboard.riwayat-setoran', compact('deposits', 'isAdmin'));
}
}