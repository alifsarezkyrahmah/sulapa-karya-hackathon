<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\PointTransfer;
use App\Models\Deposit;
use App\Models\BusinessSchedule;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'supabase_id',
        'name',
        'email',
        'phone',
        'address',
        'kecamatan',
        'kelurahan',
        'foto_profil',
        'role',
        'points_balance',
        'cash_received_total',
        'qr_code',
        'transaction_pin',
        'transaction_pin_set_at',

        // Atribut Kemitraan Mitra Bisnis (SulapaKarya PRO)
        'business_name',
        'business_type',
        'business_photo_path',
        'waste_estimate_kg',
        'business_notes',
        'business_status',
        'business_admin_notes',
    ];

    protected $hidden = [
        'transaction_pin',
    ];

    protected function casts(): array
    {
        return [
            'points_balance'      => 'integer',
            'cash_received_total' => 'integer',
            'waste_estimate_kg'   => 'integer',
        ];
    }

    public function sentTransfers()
    {
        return $this->hasMany(
            PointTransfer::class,
            'sender_id'
        );
    }

    public function receivedTransfers()
    {
        return $this->hasMany(
            PointTransfer::class,
            'receiver_id'
        );
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'user_id');
    }

    public function businessSchedule()
    {
        return $this->hasOne(BusinessSchedule::class);
    }

    /**
     * Relasi ke setoran yang ditugaskan kepada kurir ini
     */
    public function courierDeposits()
    {
        return $this->hasMany(Deposit::class, 'penjemput_id');
    }
}