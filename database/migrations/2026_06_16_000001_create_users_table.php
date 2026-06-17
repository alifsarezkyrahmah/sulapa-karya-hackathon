<?php

/*
|--------------------------------------------------------------------------
| TABEL 1: USERS
|--------------------------------------------------------------------------
|
| Fungsi: Menyimpan SEMUA akun yang bisa login ke sistem.
|         Ada 3 jenis user (role):
|         - 'user'    = masyarakat yang setor sampah
|         - 'admin'   = pengelola SulapaKarya
|         - 'artisan' = pengrajin yang ambil bahan & punya produk di marketplace
|
| Relasi:
|   users.id  <--  deposits.user_id       (user menyetor sampah)
|   users.id  <--  deposits.verified_by   (admin memverifikasi setoran)
|   users.id  <--  artisans.user_id       (user bertipe artisan punya data tambahan di tabel artisans)
|
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                              // bigint PK — ID unik, auto-increment (1, 2, 3, ...)
            $table->string('name');                                    // Nama lengkap (misal: "Budi Santoso")
            $table->string('email')->unique();                         // Email unik — tidak boleh ada 2 akun pakai email yang sama
            $table->string('password');                                // Password ter-hash (Laravel otomatis hash, jadi yang tersimpan bukan text asli)
            $table->string('phone');                                   // Nomor WhatsApp (wajib, dipakai untuk CTA dan penjadwalan)
            $table->text('address')->nullable();                       // Alamat default penjemputan (boleh kosong saat register, diisi nanti)
            $table->enum('role', ['user', 'admin', 'artisan'])         // Jenis akun — menentukan halaman apa yang bisa diakses
                  ->default('user');                                    // Default: semua pendaftar baru otomatis jadi 'user'
            $table->integer('points_balance')->default(0);             // Saldo poin aktif saat ini (bertambah tiap setor sampah)
            $table->integer('cash_received_total')->default(0);        // Total uang tunai yang pernah diterima (Rp, historis, tidak berkurang)
            $table->string('qr_code')->unique()->nullable();           // Kode QR unik per akun (di-generate saat register, 1 user = 1 QR selamanya)
            $table->timestamp('email_verified_at')->nullable();        // Kapan email diverifikasi (nullable = boleh kosong kalau belum verif)
            $table->rememberToken();                                   // Token "Remember Me" — fitur bawaan Laravel untuk session
            $table->timestamps();                                      // Otomatis bikin 2 kolom: created_at & updated_at
        });

        // Tabel bawaan Laravel untuk fitur "Lupa Password"
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Tabel bawaan Laravel untuk menyimpan session login
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
