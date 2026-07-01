<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteDeposit extends Model
{
    use HasFactory;

    // PERBAIKAN UTAMA: Mengarahkan model ke tabel 'deposits' sesuai migrasimu
    protected $table = 'deposits';

    protected $fillable = [
        'user_id',
        'deposit_code',
        'category',
        'sub_category',
        'estimated_weight',
        'actual_weight',
        'photo_path',
        'reward_type',
        'status',
        'points_earned',
        'cash_earned',
        'pickup_address',
        'pickup_date',
        'pickup_time',
        'admin_notes',
        'verified_by',
        'verified_at',
    ];

    /**
     * Relasi ke Warga (Pemilik Setoran)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Admin yang memverifikasi
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}