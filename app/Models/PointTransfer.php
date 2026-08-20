<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointTransfer extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'amount',
        'note',
        'reference_number',
        'status',
        'deposit_id',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(
            User::class,
            'sender_id'
        );
    }

    public function receiver()
    {
        return $this->belongsTo(
            User::class,
            'receiver_id'
        );
    }
}