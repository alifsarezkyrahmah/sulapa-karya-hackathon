<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    // Kolom id di database adalah bigint (auto-increment)
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

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

    protected $casts = [
        'is_featured' => 'boolean',
        'price'       => 'integer',
        'stock'       => 'integer',
    ];

    /**
     * Relasi ke Pengrajin (User)
     */
    public function artisan()
    {
        return $this->belongsTo(User::class, 'artisan_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'product_id'); 
    }
}