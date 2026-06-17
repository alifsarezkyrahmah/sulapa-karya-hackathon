<?php

/*
|--------------------------------------------------------------------------
| TABEL 6: MATERIAL_REQUESTS (Permintaan Bahan Baku oleh Pengrajin)
|--------------------------------------------------------------------------
|
| Fungsi: Mencatat setiap kali pengrajin request/ambil bahan baku dari gudang.
|         Bahan baku GRATIS — tidak ada kolom harga.
|
| Alur:
|   1. Pengrajin login, lihat stok tersedia di dashboard
|   2. Pengrajin submit request: "Saya mau ambil 5 kg Kain Perca"
|   3. Admin review → approve atau reject
|   4. Jika approved: stok di inventory_stocks berkurang otomatis
|
| Status flow:
|   pending   → (baru diajukan, menunggu admin)
|   approved  → (admin setuju, stok sudah dikurangi)
|   rejected  → (admin tolak — misal stok habis atau request tidak wajar)
|   picked_up → (bahan sudah diambil pengrajin secara fisik)
|
| Relasi:
|   material_requests.artisan_id  -->  artisans.id    (pengrajin siapa yang request)
|   material_requests.approved_by -->  users.id       (admin siapa yang approve)
|
| Efek ke tabel lain:
|   Saat status berubah ke 'approved':
|     → inventory_stocks.quantity_kg DIKURANGI sebesar actual_quantity_kg
|
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artisan_id')                             // FK ke artisans.id — pengrajin siapa
                  ->constrained()
                  ->onDelete('cascade');
            $table->enum('category', ['plastik', 'kertas', 'kain']);    // Jenis bahan yang diminta
            $table->string('sub_category')->nullable();                  // Detail jenis (PET/HDPE, HVS, Kain Perca, dll)
            $table->decimal('requested_quantity_kg', 8, 2);             // Jumlah yang diminta pengrajin (kg)
            $table->decimal('actual_quantity_kg', 8, 2)->nullable();    // Jumlah aktual yang diberikan admin (bisa beda dari request)
            $table->enum('status', [
                'pending',                                               // Baru diajukan
                'approved',                                              // Admin setuju → stok dikurangi
                'rejected',                                              // Admin tolak
                'picked_up',                                             // Bahan sudah diambil fisik oleh pengrajin
            ])->default('pending');
            $table->text('notes')->nullable();                           // Catatan (dari pengrajin atau admin)
            $table->foreignId('approved_by')->nullable()                 // FK ke users.id — admin siapa yang approve/reject
                  ->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['artisan_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_requests');
    }
};
