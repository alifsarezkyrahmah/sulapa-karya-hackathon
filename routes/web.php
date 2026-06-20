<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupabaseAuthController;

Route::get('/', function () {
    return view('welcome');
})->name('home');
    
// HAPUS MIDDLEWARE GUEST DARI SINI AGAR TIDAK BERTABRAKAN
Route::get('/auth/google', [SupabaseAuthController::class, 'redirectToGoogle'])->name('auth.google');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::post('/register', [SupabaseAuthController::class, 'register'])->name('register.post');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [SupabaseAuthController::class, 'login'])->name('login.post');


// Tetap gunakan middleware kustom Anda untuk menjaga halaman dalam
Route::middleware('supabase.auth')->group(function () {
    Route::post('/logout', [SupabaseAuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::get('/set-pin', function () {
        return view('set-pin');
    })->name('set-pin');
    Route::post('/set-pin', [SupabaseAuthController::class, 'setPin'])->name('set-pin.post');
});