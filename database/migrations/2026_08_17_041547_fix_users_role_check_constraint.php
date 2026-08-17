<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');

        DB::table('users')
            ->where('role', 'penjemput_sampah')
            ->update(['role' => 'penjemput']);

        DB::table('users')
            ->where('role', 'artisan')
            ->update(['role' => 'pengrajin']);

        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('user', 'admin', 'penjemput', 'pengrajin'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');

        DB::table('users')
            ->where('role', 'penjemput')
            ->update(['role' => 'penjemput_sampah']);

        DB::table('users')
            ->where('role', 'pengrajin')
            ->update(['role' => 'artisan']);

        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('user', 'admin', 'artisan', 'penjemput_sampah'))");
    }
};
