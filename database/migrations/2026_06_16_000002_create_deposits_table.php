<?php

/*
|--------------------------------------------------------------------------
| TABEL 2: DEPOSITS (Setoran Sampah)
|--------------------------------------------------------------------------
|
| Fungsi: Mencatat SETIAP kali user menyetorkan sampah.
|         Satu user bisa punya banyak deposit (one-to-many).
|         Setiap deposit punya siklus hidup (status):
|
|         pending → scheduled → picked_up → verified → completed
|                                                    → rejected (jika ditolak)
|
| Alur Lengkap:
|   1. User isi form setor          → INSERT (status: pending)
|   2. User jadwalkan penjemputan    → UPDATE (status: scheduled, pickup_date diisi)
|   3. Petugas jemput sampah         → UPDATE (status: picked_up)
|   4. Admin scan QR, timbang, cek   → UPDATE (status: verified, actual_weight diisi)
|   5. Admin approve reward          → UPDATE (status: completed, points/cash dihitung)
|      ATAU admin tolak              → UPDATE (status: rejected)
|
| Relasi:
|   deposits.user_id     -->  users.id  (siapa yang setor)
|   deposits.verified_by -->  users.id  (admin siapa yang verifikasi)
|
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();

            // === SIAPA YANG SETOR ===
            $table->foreignId('user_id')                               // FK ke users.id — siapa pemilik setoran ini
                  ->constrained()                                       // constrained() = pastikan user_id HARUS ada di tabel users
                  ->onDelete('cascade');                                 // cascade = kalau user dihapus, semua deposit-nya ikut terhapus

            // === IDENTITAS SETORAN ===
            $table->string('deposit_code')->unique();                   // Kode unik per setoran (misal: "DEP-20260616-00001")
                                                                        // Ini yang ditampilkan di QR code saat user submit form

            // === DATA SAMPAH ===
            $table->enum('category', ['plastik', 'kertas', 'kain']);    // Jenis utama sampah
            $table->string('sub_category')->nullable();                 // Detail jenis (misal: "PET/HDPE", "HVS/Buku", "Kain Perca")
            $table->decimal('estimated_weight', 8, 2);                  // Berat estimasi dari user (kg) — misal: 0.60
                                                                        // 8,2 artinya: max 999999.99 (8 digit total, 2 di belakang koma)
            $table->decimal('actual_weight', 8, 2)->nullable();         // Berat aktual setelah ditimbang admin (nullable karena belum pasti saat submit)
            $table->string('photo_path');                                // Path foto sampah di storage (misal: "deposits/foto_001.jpg")

            // === REWARD ===
            $table->enum('reward_type', ['cash', 'points']);            // Pilihan user: uang tunai atau poin
                                                                        // Aturan bisnis: cash hanya boleh kalau >= 1 kg
            $table->enum('status', [
                'pending',                                               // Baru submit, belum dijadwalkan
                'scheduled',                                             // Sudah ada jadwal penjemputan
                'picked_up',                                             // Sudah dijemput petugas
                'verified',                                              // Admin sudah verifikasi berat & kondisi
                'completed',                                             // Reward sudah diberikan — SELESAI
                'rejected',                                              // Ditolak (sampah tidak layak, salah kategori, dll)
            ])->default('pending');
            $table->integer('points_earned')->nullable();                // Poin yang didapat (NULL kalau pilih cash, atau belum diverifikasi)
            $table->integer('cash_earned')->nullable();                  // Uang tunai Rp yang didapat (NULL kalau pilih poin)

            // === PENJEMPUTAN ===
            $table->text('pickup_address');                              // Alamat jemput (auto-fill dari users.address, bisa diubah per setoran)
            $table->date('pickup_date')->nullable();                     // Tanggal jemput yang dijadwalkan
            $table->time('pickup_time')->nullable();                     // Jam jemput

            // === VERIFIKASI ADMIN ===
            $table->text('admin_notes')->nullable();                     // Catatan admin (misal: "Sampah bersih, layak proses")
            $table->foreignId('verified_by')->nullable()                 // FK ke users.id — admin SIAPA yang verifikasi
                  ->constrained('users')->onDelete('set null');          // set null = kalau admin dihapus, kolom ini jadi NULL (bukan hapus deposit)
            $table->timestamp('verified_at')->nullable();                // KAPAN diverifikasi

            $table->timestamps();                                        // created_at (kapan form disubmit) & updated_at

            // === INDEX untuk mempercepat query ===
            $table->index('status');                                     // Biar query WHERE status = 'pending' cepat
            $table->index(['user_id', 'status']);                        // Biar query "tampilkan semua deposit user X yang pending" cepat
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
