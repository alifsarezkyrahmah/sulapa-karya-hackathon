<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BusinessSchedule;
use App\Models\Deposit;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GenerateDailyBusinessPickups extends Command
{
    protected $signature = 'pickups:generate-daily-business';
    protected $description = 'Otomatis membuat antrean penjemputan rutin untuk mitra SulapaKarya PRO';

    public function handle()
    {
        $dayMap = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
        ];

        $todayIndo = $dayMap[Carbon::now()->format('l')] ?? 'Senin';
        $todayDate = Carbon::today()->toDateString();

        $activeSchedules = BusinessSchedule::with('user')
            ->where('is_active', true)
            ->whereHas('user', function ($q) {
                $q->where('business_status', 'approved');
            })
            ->get();

        $generatedCount = 0;

        foreach ($activeSchedules as $schedule) {
            if (is_array($schedule->pickup_days) && in_array($todayIndo, $schedule->pickup_days)) {
                // Cegah duplikasi tiket jika command dijalankan ulang di hari yang sama
                $exists = Deposit::where('user_id', $schedule->user_id)
                    ->where('deposit_type', 'business')
                    ->whereDate('pickup_date', $todayDate)
                    ->exists();

                if (!$exists) {
                    Deposit::create([
                        'user_id'          => $schedule->user_id,
                        'deposit_type'     => 'business',
                        'deposit_code'     => 'PRO-' . strtoupper(Str::random(6)),
                        'category'         => $schedule->category_focus ?? 'Limbah Bisnis Campur',
                        'estimated_weight' => $schedule->user->waste_estimate_kg ?? 10,
                        'reward_type'      => 'points',
                        'status'           => 'menunggu_penjemput', // Langsung masuk radar kurir tanpa perlu verifikasi manual
                        'kecamatan'        => $schedule->user->kecamatan,
                        'kelurahan'        => $schedule->user->kelurahan,
                        'pickup_address'   => $schedule->user->address,
                        'pickup_date'      => $todayDate,
                        'pickup_time'      => $schedule->pickup_time,
                        'qc_notes'         => $schedule->notes,
                    ]);
                    $generatedCount++;
                }
            }
        }

        $this->info("Berhasil membuat {$generatedCount} tiket penjemputan rutin PRO untuk hari {$todayIndo}.");
    }
}