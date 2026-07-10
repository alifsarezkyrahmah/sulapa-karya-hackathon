<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'original_price',
        'points_used',
        'final_price',
        'status',
        'snap_token'
    ];

    /**
     * SINKRONISASI UTAMA: Pastikan fungsi relasi balik ini ada 
     * agar database tahu cara mencocokkan ID Produk
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relasi ke Pembeli (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}