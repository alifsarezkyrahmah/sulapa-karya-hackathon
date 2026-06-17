<?php

/*
|--------------------------------------------------------------------------
| TABEL 4: ARTISANS (Data Tambahan Pengrajin)
|--------------------------------------------------------------------------
|
| Fungsi: Tabel "extension" untuk user yang role-nya 'artisan'.
|         Kenapa tidak langsung di tabel users?
|         Karena tidak semua user adalah pengrajin — jadi data khusus pengrajin
|         dipisah di sini agar tabel users tetap bersih.
|
| Relasi:
|   artisans.user_id  -->  users.id  (relasi 1-to-1)
|   Artinya: 1 artisan = 1 user account
|   Data umum (name, email, phone, address) diambil dari tabel users
|   Tabel ini cuma menyimpan data TAMBAHAN yang khusus pengrajin
|
| Saat ini (MVP): tabel ini sangat minimal — cuma user_id dan status.
|   Kenapa tetap dipisah? Karena nanti fase 2 bisa ditambah kolom
|   seperti specialty, bio, portfolio_url, dll tanpa mengotori tabel users.
|
| Contoh:
|   artisans.id=1 | user_id=5  | status=active
|   → Artinya: user dengan id=5 (misal "Ibu Siti") adalah pengrajin aktif
|   → Nama, phone, address Ibu Siti diambil dari users WHERE id=5
|
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artisans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')                                // FK ke users.id — akun login pengrajin ini
                  ->unique()                                             // unique = 1 user hanya bisa punya 1 record artisan (1-to-1)
                  ->constrained()
                  ->onDelete('cascade');                                  // Kalau user dihapus, data artisan-nya ikut terhapus
            $table->enum('status', ['active', 'inactive'])
                  ->default('active');                                    // Admin bisa nonaktifkan pengrajin tanpa hapus akun
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artisans');
    }
};
