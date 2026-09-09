<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            // Checkbox Standar 3C
            if (!Schema::hasColumn('deposits', 'is_clean')) {
                $table->boolean('is_clean')->default(false)->after('status');
            }
            if (!Schema::hasColumn('deposits', 'is_dry')) {
                $table->boolean('is_dry')->default(false)->after('is_clean');
            }
            if (!Schema::hasColumn('deposits', 'is_compact')) {
                $table->boolean('is_compact')->default(false)->after('is_dry');
            }
            // Catatan verifikasi kurir (misal jika ada sampah yang ditolak/dipotong)
            if (!Schema::hasColumn('deposits', 'qc_notes')) {
                $table->text('qc_notes')->nullable()->after('is_compact');
            }
            // Berat timbangan aktual yang disetujui kurir di lapangan
            if (!Schema::hasColumn('deposits', 'actual_weight')) {
                $table->decimal('actual_weight', 8, 2)->nullable()->after('qc_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['is_clean', 'is_dry', 'is_compact', 'qc_notes', 'actual_weight']);
        });
    }
};