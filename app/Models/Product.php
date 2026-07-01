<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'artisan_id',
        'name',
        'description',
        'price',
        'material_source',
        'product_category',
        'photo_path',
        'stock',
        'is_featured',
        'status',
    ];

    /**
     * Relasi ke Pengrajin (User) yang membuat produk ini
     */
    public function artisan()
    {
        return $this->belongsTo(User::class, 'artisan_id');
    }
}