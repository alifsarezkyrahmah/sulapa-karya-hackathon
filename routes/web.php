<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupabaseAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WasteDepositController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\BusinessPartnerController;

use App\Http\Controllers\NotificationController;
// ==========================================
// 1. RUTE PUBLIK (BISA DIAKSES TANPA LOGIN)

Route::get('/notifikasi/{id}/baca', [NotificationController::class, 'read'])->name('notifikasi.read');
Route::post('/notifikasi/baca-semua', [NotificationController::class, 'markAllAsRead'])->name('notifikasi.markAll');
// ==========================================
Route::get('/', function () {

    // 1. Data Produk Kriya
    $products = \App\Models\Product::where('status', 'available')
        ->orderBy('created_at', 'desc')
        ->take(8)
        ->get();

    $totalAvailableProducts = \App\Models\Product::where('status', 'available')->count();
    $showCatalogButton = $totalAvailableProducts > 8;

    // 2. Data Harga & Poin Sampah untuk List dan Kalkulator
    $wastePrices = \App\Models\WastePrice::orderBy('name', 'asc')->get();

    // 3. Tanggal Update Terakhir
    $latestWastePrice = \App\Models\WastePrice::orderBy('updated_at', 'desc')->first();
    $lastUpdatedDate = $latestWastePrice && $latestWastePrice->updated_at 
        ? $latestWastePrice->updated_at->translatedFormat('d F Y') 
        : date('d F Y');

    return view('welcome', compact('products', 'showCatalogButton', 'wastePrices', 'lastUpdatedDate'));

})->name('home');


Route::get('/notifikasi/{id}/baca', [NotificationController::class, 'read'])->name('notifikasi.read');
Route::post('/notifikasi/baca-semua', [NotificationController::class, 'markAllAsRead'])->name('notifikasi.markAll');

Route::get('/cara-memilah', function () {
    $wastePrices = \App\Models\WastePrice::orderBy('name', 'asc')->get();
    return view('cara-memilah', compact('wastePrices'));
})->name('cara-memilah');

Route::get('/auth/google', [SupabaseAuthController::class, 'redirectToGoogle'])->name('auth.google');

Route::get('/register', function () { return view('auth.register'); })->name('register');
Route::post('/register', [SupabaseAuthController::class, 'register'])->name('register.post');

Route::get('/login', function () { return view('auth.login'); })->name('login');
Route::post('/login', [SupabaseAuthController::class, 'login'])->name('login.post');

Route::get('/katalog', [ProductController::class, 'catalog'])->name('user.katalog');


Route::post('/midtrans-webhook', [BusinessPartnerController::class, 'handleWebhook'])->name('midtrans.webhook');
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
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{order_id}', [CheckoutController::class, 'success'])->name('checkout.success');
    
    // Riwayat Belanja Kriya & Lanjut Bayar Midtrans
    Route::get('/riwayat-pembelian', [CheckoutController::class, 'history'])->name('user.pembelian.history');
    Route::get('/checkout/resume/{order_id}', [CheckoutController::class, 'resume'])->name('checkout.resume');

    // Halaman form & riwayat pencairan
    Route::get('/pencairan-poin', [WithdrawalController::class, 'index'])->name('pencairan.index');

    // Proses form pencairan
    Route::post('/pencairan/tarik', [WithdrawalController::class, 'store'])->name('pencairan.store');

// ==========================================
    // FITUR SULAPAKARYA PRO (MITRA BISNIS B2B)
    // ==========================================
    Route::prefix('mitra-bisnis')->name('bisnis.')->group(function () {
        Route::get('/', [BusinessPartnerController::class, 'index'])->name('index');
        Route::post('/daftar', [BusinessPartnerController::class, 'register'])->name('register');
        Route::post('/snap-token', [BusinessPartnerController::class, 'getSnapToken'])->name('snap-token');
        Route::post('/bayar-sukses', [BusinessPartnerController::class, 'paymentSuccess'])->name('pay.success');
        Route::post('/jadwal', [BusinessPartnerController::class, 'saveSchedule'])->name('schedule.save');
        Route::post('/jadwal/toggle', [BusinessPartnerController::class, 'toggleSchedule'])->name('schedule.toggle');
        Route::post('/admin/verifikasi/{id}', [BusinessPartnerController::class, 'verifyByAdmin'])->name('admin.verify');

        // Perbaikan di sini: path cukup '/laporan-esg' dan name cukup 'report.export'
        Route::get('/laporan-esg', [BusinessPartnerController::class, 'exportReport'])->name('report.export');
    });

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

        // Statistik Akumulasi Platform
        Route::get('/statistik', [AdminController::class, 'statistics'])->name('admin.statistics');
        Route::get('/statistik/export', [AdminController::class, 'exportCategoryData'])->name('admin.statistics.export');

        // Verifikasi & Penugasan Kurir untuk Setoran Sampah Warga
        Route::get('/verifikasi-setoran', [AdminController::class, 'manageDeposits'])->name('admin.deposits');
        Route::post('/verifikasi-setoran/{id}/setujui', [AdminController::class, 'approveDeposit'])->name('admin.deposits.approve');

        // Verifikasi Poin Pending dari Kurir
        Route::get('/verifikasi-poin', [AdminController::class, 'pendingPoints'])->name('admin.pending-points');
        Route::post('/verifikasi-poin/{id}/proses', [AdminController::class, 'approvePoints'])->name('admin.points.approve');

        // CRUD Manajemen Data Katalog Produk Kriya (Upload, Edit, Hapus)
        Route::get('/kelola-produk', [ProductController::class, 'index'])->name('admin.products.index');
        Route::post('/kelola-produk/store', [ProductController::class, 'store'])->name('admin.products.store');
        Route::put('/kelola-produk/{id}/update', [ProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/kelola-produk/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
                
        Route::get('/harga-sampah', [AdminController::class, 'manageWastePrices'])->name('admin.waste-prices.index');
        Route::post('/harga-sampah', [AdminController::class, 'storeWastePrice'])->name('admin.waste-prices.store');
        Route::put('/harga-sampah/{id}', [AdminController::class, 'updateWastePrice'])->name('admin.waste-prices.update');
        Route::delete('/harga-sampah/{id}', [AdminController::class, 'destroyWastePrice'])->name('admin.waste-prices.destroy');

        Route::get('/jadwal-mitra-pro', [AdminController::class, 'businessSchedules'])->name('admin.bisnis.schedules');
    });


    Route::middleware('supabase.role:penjemput')->group(function () {
        // Tampilan Utama Dashboard Penjemput
    // ==========================================
        // KHUSUS ROLE: PENJEMPUT / KURIR LAPANGAN
        // ==========================================
        Route::get('/penjemput/dashboard', [CourierController::class, 'index'])->name('penjemput.dashboard');
        Route::post('/penjemput/update-status/{id}', [CourierController::class, 'updateStatus'])->name('penjemput.updateStatus');
        Route::post('/penjemput/complete-transaction/{id}', [CourierController::class, 'completeTransaction'])->name('penjemput.completeTransaction');


        Route::get('/misi', [CourierController::class, 'index'])->name('penjemput.dashboard');
        Route::post('/update-status/{id}', [CourierController::class, 'updateStatus'])->name('penjemput.updateStatus');
        Route::post('/selesaikan-transaksi/{id}', [CourierController::class, 'completeTransaction'])->name('penjemput.completeTransaction');
        Route::get('/riwayat', [CourierController::class, 'history'])->name('penjemput.history');

        Route::post('/kurir/klaim/{id}', [CourierController::class, 'claimTask'])->name('penjemput.claim');

    });

    // ------------------------------------------------------------------
    // C. PROFIL & KEAMANAN AKUN (BISA DIAKSES SEMUA PERAN)
    // ------------------------------------------------------------------
    Route::get('/set-pin', function () { return view('set-pin'); })->name('set-pin');
    Route::post('/set-pin', [SupabaseAuthController::class, 'setPin'])->name('set-pin.post');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        // Route::post('/profile/update', 'update')->name('profile.update');
        Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
        // ATAU mendukung POST & PUT sekaligus:
        Route::match(['post', 'put'], '/profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/switch-role', 'switchRole')->name('profile.switch-role');
        Route::post('/profile/password', 'updatePassword')->name('password.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });


    // ==========================================
    // FITUR KERANJANG BELANJA KRIYA (SESSION)
    // ==========================================
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/keranjang/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
});