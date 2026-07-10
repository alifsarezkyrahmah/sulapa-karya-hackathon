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