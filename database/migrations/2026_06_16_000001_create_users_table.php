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

            // Primary Key Laravel
            $table->id();

            // UUID dari Supabase Auth
            $table->uuid('supabase_id')
                ->unique();

            $table->string('name');

            $table->string('email')
                ->unique();

            $table->string('phone');

            $table->text('address')
                ->nullable();

            $table->enum('role', [
                'user',
                'admin',
                'artisan'
            ])->default('user');

            $table->integer('points_balance')
                ->default(0);

            $table->integer('cash_received_total')
                ->default(0);

            $table->string('qr_code')
                ->unique()
                ->nullable();

            $table->timestamps();
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
