<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\PointTransfer;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'supabase_id',
        'name',
        'email',
        'phone',
        'address',
        'role',
        'points_balance',
        'cash_received_total',
        'qr_code',

        'transaction_pin',
        'transaction_pin_set_at',
    ];

    protected $hidden = [
        // kosong dulu
    ];

    protected function casts(): array
    {
        return [
            'points_balance' => 'integer',
            'cash_received_total' => 'integer',
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
}