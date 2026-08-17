<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Deposit;
use App\Models\PointTransfer;
use App\Models\Transaction;
use App\Models\Product;
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
    // BLOK 1: KELOLA PENGGUNA (CRUD USERS)
    // ========================================================================

    /**
     * READ: Menampilkan daftar semua pengguna aplikasi (Kecuali Admin yang sedang login)
     */
    public function manageUsers()
    {
        $users = User::where('id', '!=', session('user_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.admin.users', compact('users'));
    }

    /**
     * CREATE: Mendaftarkan pengguna baru dengan standar keamanan
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'phone'    => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:user,penjemput,pengrajin,admin'
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format alamat email tidak valid.',
            'email.unique'      => 'Alamat email ini sudah terdaftar di sistem SulapaKarya.',
            'phone.required'    => 'Nomor telepon/HP wajib diisi untuk menghindari eror database.',
            'password.required' => 'Kata sandi pendaftaran wajib diisi.',
            'password.min'      => 'Kata sandi pendaftaran minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok. Pastikan ulang input Anda sama.'
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

    /**
     * UPDATE: Memperbarui nama, email, nomor HP, dan role pengguna
     */
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

    /**
     * DELETE: Menghapus akun pengguna secara permanen
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $namaLama = $user->name;
        $user->delete();

        return back()->with('success', 'Akun ' . $namaLama . ' telah dihapus secara permanen dari ekosistem.');
    }


    // ========================================================================
    // BLOK 2: KELOLA SETORAN SAMPAH (DEPOSITS & PENUGASAN)
    // ========================================================================

    /**
     * TAMPILAN ADMIN: Halaman Verifikasi Setoran Sampah
     */
    public function manageDeposits()
    {
        // Tarik semua data setoran sampah
        $deposits = Deposit::orderBy('created_at', 'desc')->get();

        // Tarik daftar pengguna yang berstatus 'penjemput' untuk dropdown penugasan
        $penjemputs = User::where('role', 'penjemput')->get();

        return view('dashboard.admin.verifikasi-setoran', compact('deposits', 'penjemputs'));
    }

    /**
     * FUNGSI ADMIN: Memproses (Setujui & Tugaskan / Tolak) Setoran Warga
     */
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

            // PERBAIKAN: Deteksi status 'pending' atau 'menunggu_admin'
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
                $pesanSukses = 'Berhasil! Setoran disetujui dan tugas telah dilempar ke Penjemput.';
            } else {
                $deposit->update([
                    'status'      => 'ditolak',
                    'verified_by' => session('user_id'),
                    'verified_at' => Carbon::now(),
                    'admin_notes' => $request->admin_notes,
                ]);
                $pesanSukses = 'Setoran warga telah resmi ditolak.';
            }

            return back()->with('success', $pesanSukses);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }


    // ========================================================================
    // BLOK 3: STATISTIK AKUMULASI PLATFORM
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

        $totalWeightCollected = Deposit::where('status', 'selesai')->sum('actual_weight');
        $totalEstimatedWeight = Deposit::sum('estimated_weight');

        $depositsByCategory = Deposit::select(
                'sub_category',
                'category',
                DB::raw('COUNT(*) as total'),
                DB::raw('COALESCE(SUM(actual_weight), 0) as total_weight')
            )
            ->groupBy('sub_category', 'category')
            ->orderBy('category')
            ->get();

        $totalPointsDistributed = PointTransfer::sum('amount');
        $totalPointTransfers = PointTransfer::count();

        $totalPointsCirculating = User::where('role', 'user')->sum('points_balance');

        $totalTransactions = Transaction::count();
        $successTransactions = Transaction::where('status', 'success')->count();
        $totalRevenue = Transaction::where('status', 'success')->sum('final_price');
        $totalPointsRedeemed = Transaction::where('status', 'success')->sum('points_used');

        $totalProducts = Product::count();

        $monthlyDeposits = Deposit::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
                DB::raw('COUNT(*) as total'),
                DB::raw('COALESCE(SUM(CASE WHEN status = \'selesai\' THEN actual_weight ELSE 0 END), 0) as berat')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6)->startOfMonth())
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $topWarga = User::where('role', 'user')
            ->withCount(['deposits as selesai_count' => function ($q) {
                $q->where('status', 'selesai');
            }])
            ->withSum(['deposits as total_berat' => function ($q) {
                $q->where('status', 'selesai');
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
                DB::raw('COUNT(*) as total'),
                DB::raw('COALESCE(SUM(actual_weight), 0) as total_weight')
            )
            ->groupBy('sub_category', 'category')
            ->orderBy('category')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Akumulasi Kategori');

        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'Akumulasi per Kategori Sampah — SulapaKarya');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Diekspor: ' . Carbon::now()->translatedFormat('d F Y, H:i') . ' WITA');
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A2')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headerRow = 4;
        $headers = ['Nama Barang', 'Kategori Sampah', 'Jumlah Setoran', 'Total Berat (Kg)'];
        foreach ($headers as $col => $text) {
            $cell = chr(65 + $col) . $headerRow;
            $sheet->setCellValue($cell, $text);
        }

        $headerRange = 'A' . $headerRow . ':D' . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2F6B3C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1A4423']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        $dataRow = $headerRow + 1;
        foreach ($rows as $row) {
            $sheet->setCellValue('A' . $dataRow, $row->sub_category ?? '-');
            $sheet->setCellValue('B' . $dataRow, ucfirst($row->category));
            $sheet->setCellValue('C' . $dataRow, (int) $row->total);
            $sheet->setCellValue('D' . $dataRow, round($row->total_weight, 1));

            $sheet->getStyle('C' . $dataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $dataRow)->getNumberFormat()->setFormatCode('#,##0.0');
            $sheet->getStyle('D' . $dataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            if ($dataRow % 2 === 0) {
                $sheet->getStyle('A' . $dataRow . ':D' . $dataRow)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F2F7F3');
            }

            $dataRow++;
        }

        $lastRow = $dataRow - 1;
        if ($lastRow >= $headerRow + 1) {
            $dataRange = 'A' . ($headerRow + 1) . ':D' . $lastRow;
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D0D5D1']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->setCellValue('B' . $dataRow, 'TOTAL');
            $sheet->setCellValue('C' . $dataRow, '=SUM(C' . ($headerRow + 1) . ':C' . $lastRow . ')');
            $sheet->setCellValue('D' . $dataRow, '=SUM(D' . ($headerRow + 1) . ':D' . $lastRow . ')');
            $sheet->getStyle('A' . $dataRow . ':D' . $dataRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F0E9']],
                'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2F6B3C']]],
            ]);
            $sheet->getStyle('C' . $dataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $dataRow)->getNumberFormat()->setFormatCode('#,##0.0');
            $sheet->getStyle('D' . $dataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        $sheet->getColumnDimension('A')->setWidth(36);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(20);

        $filename = 'akumulasi_kategori_sampah_' . date('Y-m-d') . '.xlsx';
        $tempFile = storage_path('app/' . $filename);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
