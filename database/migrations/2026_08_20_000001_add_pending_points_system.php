<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('point_transfers', function (Blueprint $table) {
            $table->string('status', 20)->default('approved')->after('reference_number');
            $table->foreignId('deposit_id')->nullable()->after('status')
                  ->constrained('deposits')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->after('deposit_id')
                  ->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->string('kecamatan', 100)->nullable()->after('pickup_address');
            $table->string('kelurahan', 100)->nullable()->after('kecamatan');
        });
    }

    public function down(): void
    {
        Schema::table('point_transfers', function (Blueprint $table) {
            $table->dropForeign(['deposit_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status', 'deposit_id', 'approved_by', 'approved_at']);
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['kecamatan', 'kelurahan']);
        });
    }
};
