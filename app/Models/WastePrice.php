<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WastePrice extends Model
{
    use HasFactory;

    protected $table = 'waste_prices';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'category',
        'price_per_kg',
        'point_per_kg',
        'unit',
        'description',
    ];

    protected $casts = [
        'id'           => 'string',
        'price_per_kg' => 'integer',
        'point_per_kg' => 'integer',
    ];
}