<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->foreignId('penjemput_id')->nullable()->after('user_id')
                  ->constrained('users')->onDelete('set null');
        });

        DB::statement("ALTER TABLE deposits MODIFY COLUMN category VARCHAR(50) NOT NULL");
        DB::statement("ALTER TABLE deposits MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending'");

        DB::table('deposits')->where('status', 'scheduled')->update(['status' => 'menunggu_penjemput']);
        DB::table('deposits')->where('status', 'picked_up')->update(['status' => 'penjemput_menuju_lokasi']);
        DB::table('deposits')->where('status', 'verified')->update(['status' => 'penjemput_tiba']);
        DB::table('deposits')->where('status', 'completed')->update(['status' => 'selesai']);
        DB::table('deposits')->where('status', 'rejected')->update(['status' => 'ditolak']);
    }

    public function down(): void
    {
        DB::table('deposits')->where('status', 'menunggu_admin')->update(['status' => 'pending']);
        DB::table('deposits')->where('status', 'menunggu_penjemput')->update(['status' => 'scheduled']);
        DB::table('deposits')->where('status', 'penjemput_menuju_lokasi')->update(['status' => 'picked_up']);
        DB::table('deposits')->where('status', 'penjemput_tiba')->update(['status' => 'verified']);
        DB::table('deposits')->where('status', 'selesai')->update(['status' => 'completed']);
        DB::table('deposits')->where('status', 'ditolak')->update(['status' => 'rejected']);

        DB::statement("ALTER TABLE deposits MODIFY COLUMN status ENUM('pending','scheduled','picked_up','verified','completed','rejected') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE deposits MODIFY COLUMN category ENUM('plastik','kertas','kain') NOT NULL");

        Schema::table('deposits', function (Blueprint $table) {
            $table->dropForeign(['penjemput_id']);
            $table->dropColumn('penjemput_id');
        });
    }
};
