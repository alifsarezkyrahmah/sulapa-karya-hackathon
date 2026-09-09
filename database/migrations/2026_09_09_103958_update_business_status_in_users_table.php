<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus constraint check enum lama dan ubah tipe kolom menjadi VARCHAR
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_business_status_check');
        DB::statement('ALTER TABLE users ALTER COLUMN business_status TYPE VARCHAR(50)');
        DB::statement("ALTER TABLE users ALTER COLUMN business_status SET DEFAULT 'none'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_business_status_check CHECK (business_status IN ('none', 'pending', 'approved', 'rejected', 'verified_unpaid'))");
    }
};