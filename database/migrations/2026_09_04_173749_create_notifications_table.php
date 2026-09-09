<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke ID tabel users (bigint)
            $table->unsignedBigInteger('user_id');
            $table->string('title');                  // Judul (misal: "Poin Berhasil Dicairkan!")
            $table->text('message');                  // Pesan rincian
            $table->string('type')->default('info');  // Kategori: deposit, payout, order, warning, success
            $table->string('link')->nullable();       // Rute halaman tujuan (misal: /riwayat-setoran)
            $table->boolean('is_read')->default(false);// Status sudah dibaca atau belum
            $table->timestamps();

            // Relasi foreign key ke tabel users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};