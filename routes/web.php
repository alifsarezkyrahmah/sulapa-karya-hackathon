<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupabaseAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WasteDepositController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;

// ==========================================
// 1. RUTE PUBLIK (BISA DIAKSES TANPA LOGIN)
// ==========================================
Route::get('/', function () { 
    return view('welcome'); 
})->name('home');

Route::get('/auth/google', [SupabaseAuthController::class, 'redirectToGoogle'])->name('auth.google');

Route::get('/register', function () { return view('auth.register'); })->name('register');
Route::post('/register', [SupabaseAuthController::class, 'register'])->name('register.post');

Route::get('/login', function () { return view('auth.login'); })->name('login');
Route::post('/login', [SupabaseAuthController::class, 'login'])->name('login.post');


// ==========================================
// 2. GRUP UTAMA (WAJIB LOGIN SUPABASE)
// ==========================================
Route::middleware('supabase.auth')->group(function () {
    
    // Fungsi Keluar Sesi
    Route::post('/logout', [SupabaseAuthController::class, 'logout'])->name('logout');
    
    // Pintu Utama Pengarah (Membaca session role untuk mengarahkan ke dashboard yang pas)
    Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard');

    // ------------------------------------------------------------------
    // A. FITUR BERSAMA / SHARED FEATURES (USER & ADMIN BISA AKSES)
    // ------------------------------------------------------------------
    
    // Tampilan Dashboard Warga
    Route::get('/user/dashboard', [DashboardController::class, 'userIndex'])->name('user.dashboard');
    
    // Transaksi Setor Sampah Digital
    Route::get('/setor-sampah', [WasteDepositController::class, 'create'])->name('setor-sampah.create');
    Route::post('/setor-sampah/store', [WasteDepositController::class, 'store'])->name('setor-sampah.store');
    Route::get('/riwayat-setoran', [WasteDepositController::class, 'history'])->name('setor-sampah.history');

    // Katalog Produk & Penukaran Poin Kriya
    Route::get('/katalog', [ProductController::class, 'catalog'])->name('user.katalog');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{order_id}', [CheckoutController::class, 'success'])->name('checkout.success');
    
    // Riwayat Belanja Kriya & Lanjut Bayar Midtrans
    Route::get('/riwayat-pembelian', [CheckoutController::class, 'history'])->name('user.pembelian.history');
    Route::get('/checkout/resume/{order_id}', [CheckoutController::class, 'resume'])->name('checkout.resume');


    // ------------------------------------------------------------------
    // B. KHUSUS ROLE: ADMIN UTAMA (STRICT - USER BIASA TIDAK BISA MASUK)
    // ------------------------------------------------------------------
    Route::middleware('supabase.role:admin')->group(function () {
        
        // Tampilan Utama Dashboard Admin
        Route::get('/dashboard-admin', [DashboardController::class, 'adminIndex'])->name('admin.dashboard');

        // CRUD Kelola Akun Pengguna / Management Users
        Route::get('/kelola-pengguna', [AdminController::class, 'manageUsers'])->name('admin.users');
        Route::post('/kelola-pengguna/store', [AdminController::class, 'store'])->name('admin.users.store');
        Route::put('/kelola-pengguna/{id}/update', [AdminController::class, 'update'])->name('admin.users.update');
        Route::delete('/kelola-pengguna/{id}', [AdminController::class, 'destroy'])->name('admin.users.destroy');

        // Verifikasi & Penugasan Kurir untuk Setoran Sampah Warga
        Route::get('/verifikasi-setoran', [AdminController::class, 'manageDeposits'])->name('admin.deposits');
        Route::post('/verifikasi-setoran/{id}/setujui', [AdminController::class, 'approveDeposit'])->name('admin.deposits.approve');

        // CRUD Manajemen Data Katalog Produk Kriya (Upload, Edit, Hapus)
        Route::get('/kelola-produk', [ProductController::class, 'index'])->name('admin.products.index');
        Route::post('/kelola-produk/store', [ProductController::class, 'store'])->name('admin.products.store');
        Route::put('/kelola-produk/{id}/update', [ProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/kelola-produk/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    });


    // ------------------------------------------------------------------
    // C. PROFIL & KEAMANAN AKUN (BISA DIAKSES SEMUA PERAN)
    // ------------------------------------------------------------------
    Route::get('/set-pin', function () { return view('set-pin'); })->name('set-pin');
    Route::post('/set-pin', [SupabaseAuthController::class, 'setPin'])->name('set-pin.post');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::post('/profile/update', 'update')->name('profile.update');
        Route::post('/profile/switch-role', 'switchRole')->name('profile.switch-role');
        Route::post('/profile/password', 'updatePassword')->name('password.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});