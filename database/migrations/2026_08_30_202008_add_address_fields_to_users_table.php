<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'kecamatan')) {
                $table->string('kecamatan')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'kelurahan')) {
                $table->string('kelurahan')->nullable()->after('kecamatan');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('kelurahan');
            }
            if (!Schema::hasColumn('users', 'postal_code')) {
                $table->string('postal_code', 10)->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kecamatan', 'kelurahan', 'address', 'postal_code']);
        });
    }
};