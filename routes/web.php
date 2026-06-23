<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupabaseAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
})->name('home');
    
Route::get('/auth/google', [SupabaseAuthController::class, 'redirectToGoogle'])->name('auth.google');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::post('/register', [SupabaseAuthController::class, 'register'])->name('register.post');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [SupabaseAuthController::class, 'login'])->name('login.post');

// Grup Rute Terproteksi Auth Supabase
Route::middleware('supabase.auth')->group(function () {
    Route::post('/logout', [SupabaseAuthController::class, 'logout'])->name('logout');
    
    // Pintu Utama Pengarah Dashboard
    Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard');

    // ==========================================
    // KHUSUS ROLE: USER (Sebelumnya Warga)
    // ==========================================
    Route::middleware('supabase.role:user')->group(function () {
        Route::get('/user/dashboard', [DashboardController::class, 'userIndex'])->name('user.dashboard');
    });

    // ==========================================
    // KHUSUS ROLE: PENGRAJIN KRIYA
    // ==========================================
    Route::middleware('supabase.role:pengrajin')->group(function () {
        Route::get('/pengrajin/dashboard', [DashboardController::class, 'pengrajinIndex'])->name('pengrajin.dashboard');
    });

    // ==========================================
    // KHUSUS ROLE: ADMIN UTAMA
    // ==========================================
    Route::middleware('supabase.role:admin')->group(function () {
        Route::get('/dashboard-admin', [DashboardController::class, 'adminIndex'])->name('admin.dashboard');
    });

    Route::get('/set-pin', function () {
        return view('set-pin');
    })->name('set-pin');
    Route::post('/set-pin', [SupabaseAuthController::class, 'setPin'])->name('set-pin.post');

    Route::controller(ProfileController::class)->group(function () {
        // Menampilkan halaman profil & edit terpadu
        Route::get('/profile', 'index')->name('profile.index');
        
        // Memproses simpan biodata & ganti foto avatar
        Route::post('/profile/update', 'update')->name('profile.update');
        
        // Memproses switch-role akun menjadi pengrajin (artisan)
        Route::post('/profile/switch-role', 'switchRole')->name('profile.switch-role');
        
        // Memproses update password (jika Anda pasang formnya kembali)
        Route::post('/profile/password', 'updatePassword')->name('password.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});