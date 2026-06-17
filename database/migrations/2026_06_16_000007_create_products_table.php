<?php

/*
|--------------------------------------------------------------------------
| TABEL 7: PRODUCTS (Katalog Produk Marketplace)
|--------------------------------------------------------------------------
|
| Fungsi: Menyimpan semua produk yang ditampilkan di marketplace SulapaKarya.
|         ADMIN yang input/kelola semua produk (bukan pengrajin).
|         Pengrajin TIDAK punya "lapak" sendiri.
|
| Relasi dengan Pengrajin:
|   products.artisan_id  -->  artisans.id (nullable)
|   Kolom ini BUKAN untuk "lapak", tapi untuk:
|     → Menampilkan tombol CTA "Hubungi Pengrajin"
|     → Sistem ambil artisans.user_id → users.phone (nomor WA pengrajin)
|     → Pembeli klik → redirect ke wa.me/628xxx
|   Nullable karena ada kemungkinan produk buatan internal SulapaKarya (tanpa pengrajin)
|
| Kolom is_featured:
|   → Untuk benefit membership Premium ("produk disorot di halaman utama")
|   → Admin set true pada produk tertentu
|   → Frontend filter: WHERE is_featured = true untuk section "Produk Unggulan" di landing page
|
| Contoh data:
|   id=1 | artisan_id=1 | "Tote Bag Kain Perca" | Rp 35.000 | stock=5 | available | featured=true
|   id=2 | artisan_id=2 | "Notebook Daur Ulang"  | Rp 15.000 | stock=0 | sold_out  | featured=false
|   id=3 | NULL         | "Pot Plastik SulapaK"  | Rp 25.000 | stock=3 | available | featured=false
|         ↑ produk buatan internal, bukan dari pengrajin — tombol CTA tidak muncul
|
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artisan_id')->nullable()                 // FK ke artisans.id — pengrajin pembuat (untuk CTA kontak WA)
                  ->constrained()->onDelete('set null');                  // Kalau pengrajin dihapus, produk tetap ada tapi CTA hilang
            $table->string('name');                                      // Nama produk (misal: "Tote Bag Daur Ulang Kain")
            $table->text('description')->nullable();                     // Deskripsi panjang
            $table->integer('price');                                    // Harga jual dalam Rp (admin yang tentukan)
            $table->string('material_source')->nullable();               // Label asal bahan (misal: "Daur Ulang Kain Perca 300g")
            $table->string('product_category')->nullable();              // Kategori display: "Tas & Aksesoris", "Alat Tulis", "Dekorasi Rumah"
            $table->string('photo_path')->nullable();                    // Path foto produk di storage
            $table->integer('stock')->default(0);                        // Jumlah stok unit tersedia
            $table->boolean('is_featured')->default(false);              // Apakah ditampilkan di section unggulan landing page (benefit Premium)
            $table->enum('status', ['available', 'sold_out'])
                  ->default('available');
            $table->timestamps();

            $table->index('status');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
