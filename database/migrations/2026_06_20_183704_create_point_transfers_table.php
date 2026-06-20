<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_transfers', function (Blueprint $table) {

            $table->id();

            // pengirim
            $table->foreignId('sender_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // penerima
            $table->foreignId('receiver_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // jumlah koin
            $table->integer('amount');

            // catatan opsional
            $table->string('note')
                ->nullable();

            // nomor referensi transaksi
            $table->string('reference_number')
                ->unique();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transfers');
    }
};