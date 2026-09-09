<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Deposit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoReleaseDepositPoints extends Command
{
    protected $signature = 'deposits:auto-release-points';
    protected $description = 'Otomatis mencairkan poin setoran yang berstatus sedang_diproses jika lebih dari 24 jam tanpa finalisasi admin';

    public function handle()
    {
        // Cari setoran yang berstatus 'sedang_diproses' dan sudah lewat 24 jam dari waktu update kurir
        $thresholdTime = Carbon::now()->subHours(24);

        $pendingDeposits = Deposit::where('status', 'sedang_diproses')
            ->where('updated_at', '<=', $thresholdTime)
            ->get();

        $count = 0;

        foreach ($pendingDeposits as $deposit) {
            DB::transaction(function () use ($deposit, &$count) {
                // 1. Tambahkan poin ke dompet user
                $warga = User::find($deposit->user_id);
                if ($warga && $deposit->points_earned > 0) {
                    $warga->increment('points', $deposit->points_earned);
                }

                // 2. Ubah status menjadi berhasil_dikirim dengan catatan sistem
                $deposit->update([
                    'status'   => 'berhasil_dikirim',
                    'qc_notes' => trim(($deposit->qc_notes ?? '') . ' [Auto-Released oleh Sistem (Lewat 1x24 Jam)]'),
                ]);

                $count++;
            });
        }

        $this->info("Berhasil mencairkan {$count} setoran sampah secara otomatis.");
        Log::info("AutoReleaseDepositPoints: {$count} setoran otomatis dicairkan ke saldo warga.");
    }
}