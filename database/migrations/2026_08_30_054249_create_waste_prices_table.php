<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('waste_prices')) {
            Schema::create('waste_prices', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('category')->nullable();
                $table->integer('price_per_kg')->default(0);
                $table->integer('point_per_kg')->default(0);
                $table->string('unit')->default('kg');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_prices');
    }
};