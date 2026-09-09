<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteDeposit extends Model
{
    use HasFactory;

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
        'kecamatan',
        'kelurahan',
        'pickup_date',
        'pickup_time',
        'admin_notes',
        'verified_by',
        'verified_at',
        'penjemput_id',
        'is_clean',
        'is_dry',
        'is_compact',
        'qc_notes',
    ];

    protected $casts = [
        'is_clean'   => 'boolean',
        'is_dry'     => 'boolean',
        'is_compact' => 'boolean',
        'pickup_date' => 'date',
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

    /**
     * Relasi ke Kurir / Armada Penjemput
     */
    public function penjemput()
    {
        return $this->belongsTo(User::class, 'penjemput_id');
    }

    /**
     * Relasi ke Transfer Poin (Opsional)
     */
    public function pointTransfer()
    {
        return $this->hasOne(PointTransfer::class, 'deposit_id');
    }
}