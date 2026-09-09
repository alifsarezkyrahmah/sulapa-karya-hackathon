<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('withdrawal_code')->unique();
            $table->integer('points_redeemed');
            $table->integer('cash_amount');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_holder_name');
            $table->enum('status', ['pending', 'success', 'rejected'])->default('success');
            $table->string('reference_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};