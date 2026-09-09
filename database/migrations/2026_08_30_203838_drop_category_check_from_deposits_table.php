<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE deposits DROP CONSTRAINT IF EXISTS deposits_category_check");
    }

    public function down(): void
    {
        // Kosongkan agar aman jika di-rollback
    }
};