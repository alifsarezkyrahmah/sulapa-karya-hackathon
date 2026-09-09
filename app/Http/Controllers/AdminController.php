<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Deposit;
use App\Models\PointTransfer;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\WastePrice;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AdminController extends Controller
{
    // ========================================================================
    // BLOK 1: KELOLA PENGGUNA & MITRA BISNIS (TAB VIEW)
    // ========================================================================

    public function manageUsers(Request $request)
    {
        // 1. Data antrean verifikasi mitra bisnis pending
        $pendingBusinesses = User::where('business_status', 'pending')
            ->orderBy('updated_at', 'desc')
            ->get();

        // 2. Data seluruh mitra bisnis terdaftar (PRO Aktif, Menunggu Bayar, Ditolak)
        $allBusinessPartners = User::whereNotNull('business_name')
            ->whereIn('business_status', ['approved', 'verified_unpaid', 'rejected'])
            ->with('businessSchedule')
            ->orderBy('updated_at', 'desc')
            ->get();

        // 3. Data semua user sistem (warga, penjemput, pengrajin, admin)
        $users = User::where('id', '!=', session('user_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.admin.users', compact('users', 'pendingBusinesses', 'allBusinessPartners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'phone'    => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:user,penjemput,pengrajin,admin'
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format alamat email tidak valid.',
            'email.unique'       => 'Alamat email ini sudah terdaftar di sistem SulapaKarya.',
            'phone.required'     => 'Nomor telepon/HP wajib diisi.',
            'password.required'  => 'Kata sandi pendaftaran wajib diisi.',
            'password.min'       => 'Kata sandi pendaftaran minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.'
        ]);

        try {
            User::create([
                'supabase_id'         => (string) Str::uuid(),
                'name'                => $request->name,
                'email'               => $request->email,
                'phone'               => $request->phone,
                'password'            => Hash::make($request->password),
                'role'                => $request->role,
                'qr_code'             => (string) Str::uuid(),
                'points_balance'      => 0,
                'cash_received_total' => 0
            ]);

            return back()->with('success', 'Registrasi berhasil! Akun ' . $request->name . ' dengan peran ' . strtoupper($request->role) . ' telah aktif.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mendaftarkan akun: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'role'  => 'required|in:user,penjemput,pengrajin,admin'
        ], [
            'name.required'  => 'Nama lengkap tidak boleh dikosongkan.',
            'email.required' => 'Alamat email tidak boleh dikosongkan.',
            'email.unique'   => 'Alamat email sudah digunakan oleh pengguna lain.',
            'phone.required' => 'Nomor telepon tidak boleh dikosongkan.'
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role'  => $request->role
        ]);

        return back()->with('success', 'Data profil ' . $user->name . ' berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $namaLama = $user->name;
        $user->delete();

        return back()->with('success', 'Akun ' . $namaLama . ' telah dihapus secara permanen dari platform.');
    }


    // ========================================================================
    // BLOK 2: KELOLA SETORAN SAMPAH (DEPOSITS & PENUGASAN KURIR)
    // ========================================================================

    // public function manageDeposits()
    // {
    //     $deposits = Deposit::with(['user', 'penjemput'])->orderBy('created_at', 'desc')->get();
    //     $penjemputs = User::where('role', 'penjemput')->get();

    //     return view('dashboard.admin.verifikasi-setoran', compact('deposits', 'penjemputs'));
    // }

    public function manageDeposits(Request $request)
    {
        $deposits = Deposit::with(['user', 'penjemput'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung tugas aktif kurir berdasarkan penjemput_id (status aktif di lapangan)
        $penjemputs = User::where('role', 'penjemput')
            ->withCount(['courierDeposits as active_missions_count' => function ($q) {
                $q->whereIn('status', ['menunggu_penjemput', 'penjemput_menuju_lokasi', 'penjemput_tiba']);
            }])
            ->orderBy('kecamatan', 'asc')
            ->get();

        return view('dashboard.admin.verifikasi-setoran', compact('deposits', 'penjemputs'));
    }

    public function approveDeposit(Request $request, $id)
    {
        $request->validate([
            'keputusan'    => 'required|in:terima,tolak',
            'penjemput_id' => 'required_if:keputusan,terima',
            'admin_notes'  => 'nullable|string'
        ], [
            'penjemput_id.required_if' => 'Anda wajib menugaskan satu kurir/penjemput jika setoran disetujui.'
        ]);

        try {
            $deposit = Deposit::findOrFail($id);

            if (!in_array($deposit->status, ['pending', 'menunggu_admin'])) {
                return back()->withErrors(['error' => 'Setoran ini sudah diverifikasi sebelumnya dan tidak dapat diubah lagi.']);
            }

            if ($request->keputusan === 'terima') {
                $deposit->update([
                    'status'       => 'menunggu_penjemput',
                    'penjemput_id' => $request->penjemput_id,
                    'verified_by'  => session('user_id'),
                    'verified_at'  => Carbon::now(),
                    'admin_notes'  => $request->admin_notes,
                ]);

                // 1. Notifikasi ke pengguna bahwa setoran disetujui
                Notification::notifyDeposit($deposit);

                // 2. Notifikasi ke kurir bahwa ada tugas penjemputan baru
                Notification::notifyPenjemputAssignment($deposit);

                $pesanSukses = 'Berhasil! Setoran disetujui dan tugas telah didelegasikan ke Penjemput.';
            } else {
                $deposit->update([
                    'status'      => 'ditolak',
                    'verified_by' => session('user_id'),
                    'verified_at' => Carbon::now(),
                    'admin_notes' => $request->admin_notes,
                ]);

                // Notifikasi ke pengguna bahwa setoran ditolak
                Notification::notifyDeposit($deposit);

                $pesanSukses = 'Setoran telah resmi ditolak.';
            }

            return back()->with('success', $pesanSukses);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }


    // ========================================================================
    // BLOK 3: AUDIT & FINALISASI POIN (AKUMULASI SALDO USER)
    // ========================================================================

    public function pendingPoints(Request $request)
    {
        $this->autoApprovePendingPoints();

        // 1. Data antrean pending verifikasi
        $pendingTransfers = Deposit::where('status', 'sedang_diproses')
            ->with(['user', 'penjemput'])
            ->orderBy('updated_at', 'asc')
            ->get();

        // 2. Query riwayat mutasi
        $historyQuery = Deposit::whereIn('status', ['berhasil_dikirim', 'completed', 'selesai', 'ditolak', 'ditolak_qc', 'rejected'])
            ->with(['user', 'penjemput', 'verifier']);

        if ($request->filled('filter_date')) {
            $historyQuery->whereDate('updated_at', $request->filter_date);
        }

        if ($request->filled('filter_month')) {
            $historyQuery->whereMonth('updated_at', $request->filter_month);
        }

        if ($request->filled('filter_year')) {
            $historyQuery->whereYear('updated_at', $request->filter_year);
        }

        if ($request->filled('filter_status')) {
            if ($request->filter_status === 'approved') {
                $historyQuery->whereIn('status', ['berhasil_dikirim', 'completed', 'selesai']);
            } elseif ($request->filter_status === 'rejected') {
                $historyQuery->whereIn('status', ['ditolak', 'ditolak_qc', 'rejected']);
            }
        }

        // Hitung total poin yang cair
        $totalPointsReleased = (int) ((clone $historyQuery)
            ->whereIn('status', ['berhasil_dikirim', 'completed', 'selesai'])
            ->sum('points_earned') ?? 0);

        $totalApprovedCount = (clone $historyQuery)->whereIn('status', ['berhasil_dikirim', 'completed', 'selesai'])->count();
        $totalRejectedCount = (clone $historyQuery)->whereIn('status', ['ditolak', 'ditolak_qc', 'rejected'])->count();

        $verifiedHistory = $historyQuery->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        $availableYears = Deposit::selectRaw('EXTRACT(YEAR FROM created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('dashboard.admin.verifikasi-poin', compact(
            'pendingTransfers',
            'verifiedHistory',
            'totalPointsReleased',
            'totalApprovedCount',
            'totalRejectedCount',
            'availableYears'
        ));
    }

    public function approvePoints(Request $request, $id)
    {
        $request->validate(['keputusan' => 'required|in:setujui,tolak']);

        try {
            $deposit = Deposit::findOrFail($id);

            if ($deposit->status !== 'sedang_diproses') {
                return back()->withErrors(['error' => 'Setoran ini sudah diverifikasi sebelumnya atau tidak dalam antrean proses poin.']);
            }

            if ($request->keputusan === 'setujui') {
                DB::transaction(function () use ($deposit) {
                    // AKUMULASI POIN: Menambah poin ke akun user yang sama (baik personal maupun bisnis)
                    $user = User::find($deposit->user_id);
                    if ($user && $deposit->points_earned > 0) {
                        $user->increment('points_balance', $deposit->points_earned);
                    }

                    $deposit->update([
                        'status'      => 'berhasil_dikirim',
                        'verified_by' => session('user_id'),
                        'verified_at' => Carbon::now(),
                    ]);

                    // Notifikasi ke pengguna: Poin disetujui admin
                    Notification::notifyUserPointsDecision($deposit, 'setujui', false);
                });

                $tipeLabel = ($deposit->deposit_type === 'business') ? ' [Mitra PRO]' : '';
                return back()->with('success', 'Poin' . $tipeLabel . ' sebesar +' . number_format($deposit->points_earned) . ' berhasil diverifikasi dan masuk ke saldo akun!');
            } else {
                $deposit->update([
                    'status'        => 'ditolak',
                    'points_earned' => 0,
                    'verified_by'   => session('user_id'),
                    'verified_at'   => Carbon::now(),
                    'qc_notes'      => trim(($deposit->qc_notes ?? '') . ' [Poin Ditolak oleh Audit Admin]'),
                ]);

                // Notifikasi ke pengguna: Poin ditolak admin
                Notification::notifyUserPointsDecision($deposit, 'tolak', false);

                return back()->with('success', 'Poin setoran berhasil ditolak. Saldo pengguna tidak bertambah.');
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memproses verifikasi poin: ' . $e->getMessage()]);
        }
    }

    private function autoApprovePendingPoints()
    {
        $expiredDeposits = Deposit::where('status', 'sedang_diproses')
            ->where('updated_at', '<=', Carbon::now()->subHours(24))
            ->get();

        foreach ($expiredDeposits as $deposit) {
            DB::transaction(function () use ($deposit) {
                // AKUMULASI POIN OTOMATIS
                $user = User::find($deposit->user_id);
                if ($user && $deposit->points_earned > 0) {
                    $user->increment('points_balance', $deposit->points_earned);
                }

                $deposit->update([
                    'status'   => 'berhasil_dikirim',
                    'qc_notes' => trim(($deposit->qc_notes ?? '') . ' [Auto-Release Sistem: >24 Jam]'),
                ]);

                // Notifikasi ke pengguna: Poin cair otomatis
                Notification::notifyUserPointsDecision($deposit, 'setujui', true);
            });
        }
    }

    // ========================================================================
    // BLOK 4: STATISTIK AKUMULASI PLATFORM
    // ========================================================================

    public function statistics()
    {
        $usersByRole = User::select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $totalUsers = User::count();
        $totalDeposits = Deposit::count();
        $depositsByStatus = Deposit::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalWeightCollected = Deposit::whereIn('status', ['berhasil_dikirim', 'completed', 'selesai'])->sum('actual_weight');
        $totalEstimatedWeight = Deposit::sum('estimated_weight');

        $depositsByCategory = Deposit::select(
                'sub_category',
                'category',
                'kecamatan',
                'kelurahan',
                DB::raw('COUNT(*) as total'),
                DB::raw('COALESCE(SUM(actual_weight), 0) as total_weight')
            )
            ->groupBy('sub_category', 'category', 'kecamatan', 'kelurahan')
            ->orderBy('kecamatan')
            ->orderBy('kelurahan')
            ->orderBy('category')
            ->get();

        $totalPointsDistributed = Deposit::whereIn('status', ['berhasil_dikirim', 'completed', 'selesai'])->sum('points_earned');
        $totalPointTransfers = Deposit::whereIn('status', ['berhasil_dikirim', 'completed', 'selesai'])->count();

        $totalPointsCirculating = User::where('role', 'user')->sum('points_balance');

        $totalTransactions = Transaction::count();
        $successTransactions = Transaction::where('status', 'success')->count();
        $totalRevenue = Transaction::where('status', 'success')->sum('final_price');
        $totalPointsRedeemed = Transaction::where('status', 'success')->sum('points_used');

        $totalProducts = Product::count();

        $monthlyDeposits = Deposit::select(
                DB::raw("TO_CHAR(created_at, 'YYYY-MM') as bulan"),
                DB::raw('COUNT(*) as total'),
                DB::raw("COALESCE(SUM(CASE WHEN status IN ('berhasil_dikirim', 'completed', 'selesai') THEN actual_weight ELSE 0 END), 0) as berat")
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6)->startOfMonth())
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $topWarga = User::where('role', 'user')
            ->withCount(['deposits as selesai_count' => function ($q) {
                $q->whereIn('status', ['berhasil_dikirim', 'completed', 'selesai']);
            }])
            ->withSum(['deposits as total_berat' => function ($q) {
                $q->whereIn('status', ['berhasil_dikirim', 'completed', 'selesai']);
            }], 'actual_weight')
            ->orderByDesc('total_berat')
            ->take(5)
            ->get();

        return view('dashboard.admin.statistik', compact(
            'usersByRole', 'totalUsers',
            'totalDeposits', 'depositsByStatus', 'totalWeightCollected', 'totalEstimatedWeight',
            'depositsByCategory',
            'totalPointsDistributed', 'totalPointTransfers', 'totalPointsCirculating',
            'totalTransactions', 'successTransactions', 'totalRevenue', 'totalPointsRedeemed',
            'totalProducts',
            'monthlyDeposits', 'topWarga'
        ));
    }

    public function exportCategoryData()
    {
        $rows = Deposit::select(
                'sub_category',
                'category',
                'kecamatan',
                'kelurahan',
                DB::raw('COUNT(*) as total'),
                DB::raw('COALESCE(SUM(actual_weight), 0) as total_weight')
            )
            ->groupBy('sub_category', 'category', 'kecamatan', 'kelurahan')
            ->orderBy('kecamatan')
            ->orderBy('kelurahan')
            ->orderBy('category')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Akumulasi Kategori');

        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'Akumulasi per Kategori Sampah — SulapaKarya');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Diekspor: ' . Carbon::now()->translatedFormat('d F Y, H:i') . ' WITA');
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headerRow = 4;
        $headers = ['Kecamatan', 'Kelurahan', 'Nama Barang', 'Kategori Sampah', 'Jumlah Setoran', 'Total Berat (Kg)'];
        foreach ($headers as $col => $text) {
            $cell = chr(65 + $col) . $headerRow;
            $sheet->setCellValue($cell, $text);
        }

        $headerRange = 'A' . $headerRow . ':F' . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2F6B3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1A4423']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        $dataRow = $headerRow + 1;
        foreach ($rows as $row) {
            $sheet->setCellValue('A' . $dataRow, $row->kecamatan ?? '-');
            $sheet->setCellValue('B' . $dataRow, $row->kelurahan ?? '-');
            $sheet->setCellValue('C' . $dataRow, $row->sub_category ?? '-');
            $sheet->setCellValue('D' . $dataRow, ucfirst($row->category));
            $sheet->setCellValue('E' . $dataRow, (int) $row->total);
            $sheet->setCellValue('F' . $dataRow, round($row->total_weight, 1));

            $sheet->getStyle('E' . $dataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $dataRow)->getNumberFormat()->setFormatCode('#,##0.0');
            $sheet->getStyle('F' . $dataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            if ($dataRow % 2 === 0) {
                $sheet->getStyle('A' . $dataRow . ':F' . $dataRow)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F2F7F3');
            }

            $dataRow++;
        }

        $lastRow = $dataRow - 1;
        if ($lastRow >= $headerRow + 1) {
            $dataRange = 'A' . ($headerRow + 1) . ':F' . $lastRow;
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D0D5D1']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->setCellValue('D' . $dataRow, 'TOTAL');
            $sheet->setCellValue('E' . $dataRow, '=SUM(E' . ($headerRow + 1) . ':E' . $lastRow . ')');
            $sheet->setCellValue('F' . $dataRow, '=SUM(F' . ($headerRow + 1) . ':F' . $lastRow . ')');
            $sheet->getStyle('A' . $dataRow . ':F' . $dataRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F0E9']],
                'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2F6B3C']]],
            ]);
            $sheet->getStyle('E' . $dataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $dataRow)->getNumberFormat()->setFormatCode('#,##0.0');
            $sheet->getStyle('F' . $dataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        $sheet->getColumnDimension('A')->setWidth(22);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(36);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(20);

        $filename = 'akumulasi_kategori_sampah_' . date('Y-m-d') . '.xlsx';
        $tempFile = storage_path('app/' . $filename);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // ========================================================================
    // BLOK 5: KELOLA HARGA SAMPAH & POIN (CRUD WASTE PRICES)
    // ========================================================================

    public function manageWastePrices(Request $request)
    {
        $query = WastePrice::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('name', 'ilike', "%{$search}%");
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price_per_kg', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price_per_kg', 'desc');
                    break;
                case 'points_desc':
                    $query->orderBy('point_per_kg', 'desc');
                    break;
                case 'latest_updated':
                    $query->orderBy('updated_at', 'desc');
                    break;
                case 'name_asc':
                default:
                    $query->orderBy('name', 'asc');
                    break;
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        $wastePrices = $query->get();

        return view('dashboard.admin.waste-prices', compact('wastePrices'));
    }

    public function storeWastePrice(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'price_per_kg' => 'required|integer|min:0',
            'unit'         => 'nullable|string|max:20',
            'description'  => 'nullable|string',
        ]);

        try {
            $price = (int) $request->price_per_kg;
            $point = (int) round($price * 0.40);

            WastePrice::create([
                'id'           => (string) Str::uuid(),
                'name'         => $request->name,
                'price_per_kg' => $price,
                'point_per_kg' => $point,
                'unit'         => $request->unit ?? 'kg',
                'description'  => $request->description,
            ]);

            return redirect()->route('admin.waste-prices.index')->with('success', 'Data harga sampah berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()])->withInput();
        }
    }

    public function updateWastePrice(Request $request, $id)
    {
        $wastePrice = WastePrice::findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:255',
            'price_per_kg' => 'required|integer|min:0',
            'unit'         => 'nullable|string|max:20',
            'description'  => 'nullable|string',
        ]);

        try {
            $price = (int) $request->price_per_kg;
            $point = (int) round($price * 0.40);

            $wastePrice->update([
                'name'         => $request->name,
                'price_per_kg' => $price,
                'point_per_kg' => $point,
                'unit'         => $request->unit ?? 'kg',
                'description'  => $request->description,
            ]);

            return redirect()->route('admin.waste-prices.index')->with('success', 'Harga sampah & poin konversi berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
        }
    }

    public function destroyWastePrice($id)
    {
        try {
            $wastePrice = WastePrice::findOrFail($id);
            $wastePrice->delete();

            return redirect()->route('admin.waste-prices.index')->with('success', 'Kategori harga sampah berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    /**
     * Matriks Jadwal Operasional Penjemputan Mitra PRO
     */
    public function businessSchedules(Request $request)
    {
        $daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $selectedDay = $request->get('hari', Carbon::now()->translatedFormat('l')); // Default hari ini

        // Sesuaikan nama hari Inggris ke Indonesia jika locale default belum id
        $dayMap = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
        ];
        if (isset($dayMap[$selectedDay])) {
            $selectedDay = $dayMap[$selectedDay];
        }

        // Ambil semua mitra PRO yang aktif dan memiliki jadwal
        $activeSchedules = \App\Models\BusinessSchedule::with('user')
            ->where('is_active', true)
            ->whereHas('user', function ($q) {
                $q->where('business_status', 'approved');
            })
            ->get();

        // Hitung kepadatan penjemputan per hari untuk badge di tab
        $dayCounts = [];
        foreach ($daysOfWeek as $day) {
            $dayCounts[$day] = $activeSchedules->filter(function ($item) use ($day) {
                return is_array($item->pickup_days) && in_array($day, $item->pickup_days);
            })->count();
        }

        // Filter jadwal untuk hari yang sedang dipilih
        $schedulesForDay = $activeSchedules->filter(function ($item) use ($selectedDay) {
            return is_array($item->pickup_days) && in_array($selectedDay, $item->pickup_days);
        })->sortBy('pickup_time');

        // Kelompokkan berdasarkan Kecamatan agar admin mudah mengatur rute kurir
        $groupedByKecamatan = $schedulesForDay->groupBy(function ($item) {
            return $item->user->kecamatan ?? 'Lainnya';
        });

        return view('dashboard.admin.jadwal-bisnis', compact(
            'daysOfWeek', 
            'selectedDay', 
            'dayCounts', 
            'schedulesForDay', 
            'groupedByKecamatan'
        ));
    }
}