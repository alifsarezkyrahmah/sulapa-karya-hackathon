<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSchedule extends Model
{
    use HasFactory;

    protected $table = 'business_schedules';

    protected $fillable = [
        'user_id',
        'pickup_days',
        'pickup_time',
        'category_focus',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'pickup_days' => 'array',
        'is_active'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}