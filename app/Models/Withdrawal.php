<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $table = 'withdrawals';

    protected $fillable = [
        'user_id',
        'withdrawal_code',
        'points_redeemed',
        'cash_amount',
        'bank_name',
        'account_number',
        'account_holder_name',
        'status',
        'reference_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}