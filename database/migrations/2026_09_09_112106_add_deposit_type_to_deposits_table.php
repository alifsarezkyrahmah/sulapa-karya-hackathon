<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            // Nilai: 'personal' (warga reguler) atau 'business' (mitra pro terjadwal)
            $table->string('deposit_type')->default('personal')->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn('deposit_type');
        });
    }
};