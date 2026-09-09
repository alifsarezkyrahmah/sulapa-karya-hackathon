<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom profil bisnis ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('name');
            $table->string('business_type')->nullable()->after('business_name');
            $table->string('business_photo_path')->nullable()->after('business_type');
            $table->unsignedInteger('waste_estimate_kg')->nullable()->after('business_photo_path');
            $table->text('business_notes')->nullable()->after('waste_estimate_kg');
            $table->enum('business_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('business_notes');
            $table->text('business_admin_notes')->nullable()->after('business_status');
        });

        // 2. Buat tabel jadwal penjemputan rutin mitra bisnis
        Schema::create('business_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->json('pickup_days'); // Contoh: ["Senin", "Kamis"]
            $table->string('pickup_time'); // Contoh: "09:00"
            $table->string('category_focus')->default('Kardus, Kaca & Plastik');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true); // Fitur jeda/aktif
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_schedules');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'business_type',
                'business_photo_path',
                'waste_estimate_kg',
                'business_notes',
                'business_status',
                'business_admin_notes',
            ]);
        });
    }
};