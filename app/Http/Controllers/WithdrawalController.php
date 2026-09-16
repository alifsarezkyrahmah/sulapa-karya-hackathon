<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WithdrawalController extends Controller
{
    /**
     * Menampilkan Halaman Penarikan Dana & Riwayat Mutasi
     */
    public function index()
    {
        $userId = session('user_id') ?? (auth()->check() ? auth()->id() : null);

        // Validasi login tanpa melempar 404
        if (!$userId) {
            return redirect('/login')->withErrors(['error' => 'Silakan masuk ke akun Anda terlebih dahulu.']);
        }

        $currentUser = User::find($userId);

        if (!$currentUser) {
            session()->flush();
            return redirect('/login')->withErrors(['error' => 'Sesi tidak valid, silakan login kembali.']);
        }

        $safePointsBalance = ($currentUser->points_balance > 0) ? $currentUser->points_balance : 0;

        $withdrawals = Withdrawal::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.user.withdrawal', compact('currentUser', 'safePointsBalance', 'withdrawals'));
    }

    /**
     * Memproses Pengajuan Pencairan Dana (Simulasi Kurs 1 Poin = Rp 1)
     */
    public function store(Request $request)
    {
        $userId = session('user_id') ?? (auth()->check() ? auth()->id() : null);

        if (!$userId) {
            return redirect('/login')->withErrors(['error' => 'Sesi login telah berakhir.']);
        }

        $user = User::findOrFail($userId);

        $request->validate([
            'points'              => 'required|integer|min:50000',
            'bank_name'           => 'required|string',
            'account_number'      => 'required|numeric',
            'account_holder_name' => 'required|string',
        ], [
            'points.required'        => 'Jumlah poin yang ingin dicairkan wajib diisi.',
            'points.min'             => 'Batas minimal pencairan adalah 1.000 poin.',
            'account_number.numeric' => 'Nomor rekening wajib berupa angka.',
            'bank_name.required'     => 'Silakan pilih bank tujuan penarikan.',
        ]);

        // 1. Verifikasi kecocokan nama pemilik rekening dengan nama akun
        if (strcasecmp(trim($request->account_holder_name), trim($user->name)) !== 0) {
            return back()->withErrors([
                'account_holder_name' => "Nama pemilik rekening harus sama persis dengan nama akun terdaftar Anda ({$user->name})."
            ])->withInput();
        }

        // 2. Verifikasi kecukupan saldo poin
        if ($user->points_balance < $request->points) {
            return back()->withErrors([
                'points' => 'Saldo poin Anda tidak mencukupi untuk melakukan penarikan nominal ini.'
            ])->withInput();
        }

        try {
            $withdrawalData = DB::transaction(function () use ($user, $request) {
                $cashAmount = (int) $request->points;
                $withdrawalCode = 'WDR-' . date('Ymd') . '-' . strtoupper(Str::random(5));
                $refId = 'TRX-SIM-' . rand(100000, 999999);

                // Potong saldo di database
                $user->decrement('points_balance', $request->points);
                $user->increment('cash_received_total', $cashAmount);

                // Rekam transaksi penarikan
                $withdrawal = Withdrawal::create([
                    'user_id'             => $user->id,
                    'withdrawal_code'     => $withdrawalCode,
                    'points_redeemed'     => $request->points,
                    'cash_amount'         => $cashAmount,
                    'bank_name'           => strtoupper($request->bank_name),
                    'account_number'      => $request->account_number,
                    'account_holder_name' => $request->account_holder_name,
                    'status'              => 'success',
                    'reference_id'        => $refId,
                ]);

                // Sinkronkan ke session
                $user->refresh();
                session(['points_balance' => $user->points_balance]);

                // Picu notifikasi instan
                Notification::notifyPayout(
                    $user->id,
                    $cashAmount,
                    'uang',
                    'berhasil',
                    $withdrawalCode
                );

                return $withdrawal;
            });

            return back()->with('receipt_data', [
                'code'           => $withdrawalData->withdrawal_code,
                'ref_id'         => $withdrawalData->reference_id,
                'bank'           => $withdrawalData->bank_name,
                'account_number' => $withdrawalData->account_number,
                'account_name'   => $withdrawalData->account_holder_name,
                'amount'         => $withdrawalData->cash_amount,
                'points'         => $withdrawalData->points_redeemed,
                'date'           => $withdrawalData->created_at->translatedFormat('d F Y, H:i') . ' WITA',
            ])->with('success', 'Pencairan dana berhasil diproses ke rekening Anda!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memproses pencairan: ' . $e->getMessage()]);
        }
    }
}