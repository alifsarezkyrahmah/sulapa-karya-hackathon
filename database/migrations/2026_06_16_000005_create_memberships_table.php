<?php

/*
|--------------------------------------------------------------------------
| TABEL 5: MEMBERSHIPS (Langganan Pengrajin)
|--------------------------------------------------------------------------
|
| Fungsi: Mencatat riwayat subscription pengrajin.
|         Ini SUMBER REVENUE UTAMA SulapaKarya.
|
|         3 tier membership:
|         ┌──────────────┬───────────┬──────────────────────────────────────┐
|         │ Tier         │ Harga/bln │ Benefit                              │
|         ├──────────────┼───────────┼──────────────────────────────────────┤
|         │ pemula       │ Gratis    │ Max 3-5 produk ditampilkan           │
|         │ aktif        │ Rp 35.000 │ Unlimited produk + badge Verified    │
|         │ premium      │ Rp100.000 │ Semua fitur aktif + featured di home │
|         └──────────────┴───────────┴──────────────────────────────────────┘
|
| Relasi:
|   memberships.artisan_id  -->  artisans.id (satu pengrajin bisa punya banyak riwayat membership)
|
| Catatan:
|   - Satu pengrajin bisa punya BANYAK baris membership (riwayat bulan ke bulan)
|   - Yang aktif cuma 1 (WHERE status = 'active')
|   - Pembayaran di MVP dilakukan manual (transfer, lalu admin update status)
|   - Kolom price menyimpan harga yang DIBAYAR saat itu (biar kalau harga berubah, riwayat tetap akurat)
|
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artisan_id')                             // FK ke artisans.id — pengrajin siapa
                  ->constrained()
                  ->onDelete('cascade');
            $table->enum('tier', ['pemula', 'aktif', 'premium']);       // Tier langganan
            $table->integer('price')->default(0);                       // Harga yang dibayar dalam Rp (0 untuk pemula)
            $table->date('started_at');                                  // Tanggal mulai berlaku
            $table->date('expires_at');                                  // Tanggal kedaluwarsa (started_at + 30 hari)
            $table->enum('status', ['active', 'expired'])               // Status saat ini
                  ->default('active');
            $table->timestamps();

            $table->index(['artisan_id', 'status']);                     // Biar query "cari membership aktif pengrajin X" cepat
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
