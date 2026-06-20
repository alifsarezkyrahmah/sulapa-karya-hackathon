<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sulapa.com'],
            [
                'name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'phone' => '081111111111',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@sulapa.com'],
            [
                'name' => 'Regular User',
                'email' => 'user@gmail`.com',
                'phone' => '082222222222',
                'role' => 'user',
                'password' => Hash::make('password'),
            ]
        );
    }
}