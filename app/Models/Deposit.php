<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    /**
     * Menentukan nama tabel secara eksplisit (opsional tapi disarankan)
     */
    protected $table = 'deposits';

    /**
     * Daftar kolom yang diizinkan untuk diisi secara massal (Mass Assignment)
     */
    protected $fillable = [
        'user_id',
        'penjemput_id', // Kolom penugasan kurir
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
     * Konversi tipe data otomatis (Casting)
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'pickup_date' => 'date',
        'estimated_weight' => 'decimal:2',
        'actual_weight' => 'decimal:2',
    ];

    // ========================================================================
    // RELASI ANTAR TABEL (SANGAT PENTING UNTUK DASHBOARD)
    // ========================================================================

    /**
     * Relasi ke Warga yang melakukan setoran sampah
     * Cara panggil: $deposit->user->name
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Kurir/Penjemput yang ditugaskan oleh Admin
     * Cara panggil: $deposit->penjemput->name
     */
    public function penjemput()
    {
        return $this->belongsTo(User::class, 'penjemput_id');
    }

    /**
     * Relasi ke Admin yang melakukan verifikasi setoran ini
     * Cara panggil: $deposit->verifier->name
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}