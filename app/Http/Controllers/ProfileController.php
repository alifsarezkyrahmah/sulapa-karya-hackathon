<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Schema;
class ProfileController extends Controller
{
/**
     * Menampilkan Master Halaman Profil
     */
    public function index()
    {
        $user = User::find(session('user_id'));

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Silakan masuk akun terlebih dahulu.');
        }

        return view('profile.index', compact('user'));
    }

    /**
     * Memproses Pembaruan Data & Avatar
     */
    public function update(Request $request)
    {
        $user = User::find(session('user_id'));

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Sesi Anda telah berakhir.');
        }

        // Validasi input form
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone'       => ['required', 'string', 'max:20'],
            'address'     => ['required', 'string', 'min:5'],
            'foto_profil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'address.min' => 'Alamat rumah wajib ditulis minimal 5 karakter.',
            'foto_profil.max' => 'Ukuran pasfoto tidak boleh lebih dari 2MB.',
        ]);

        // Update data text
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;

// Di dalam method update() pada ProfileController.php

        // 💡 SEBELUM LOGIKA UPLOAD FILE, TAMBAHKAN BLOK INI:
        if ($request->input('delete_avatar') === '1') {
            if ($user->foto_profil) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->foto_profil);
            }
            $user->foto_profil = null;
        }

        // Logika upload foto profil baru bawaan Anda (Biarkan tetap seperti ini di bawahnya)
        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->foto_profil);
            }
            $path = $request->file('foto_profil')->store('profile-photos', 'public');
            $user->foto_profil = $path;
        }

        $user->save();

        // Singkronkan data nama di sesi navbar
        session(['name' => $user->name]);

        return redirect()->route('profile.index')->with('success', 'Profil Anda berhasil diperbarui!');
    }

    /**
     * Beralih Peran ke Artisan
     */
    public function switchRole()
    {
        $user = User::find(session('user_id'));
        
        $user->role = 'artisan';
        $user->save();

        session(['role' => 'artisan']);

        return redirect()->to('/dashboard')->with('success', 'Akun Anda resmi beralih menjadi Mitra Artisan Kriya.');
    }


    public function updatePassword(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = User::find(session('user_id'));

        if (!$user || !$user->supabase_id) {
            return redirect()->to('/login')->with('error', 'Sesi Anda telah berakhir atau akun tidak valid.');
        }

        // 1. Validasi awal format data di sisi Laravel
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::defaults()->min(6)],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required'        => 'Kata sandi baru wajib diisi.',
            'password.confirmed'       => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min'             => 'Kata sandi baru minimal harus 6 karakter.',
        ]);

        // Ambil kredensial .env
        $supabaseUrl = rtrim(trim(env('SUPABASE_URL'), '"\''), '/');
        $anonKey     = trim(env('SUPABASE_ANON_KEY'), '"\'');
        $serviceKey  = trim(env('SUPABASE_SERVICE_ROLE_KEY'), '"\'');
        $serviceKey  = str_replace('Bearer ', '', $serviceKey);

        // 🔍 PROTEKSI DINI ANTI-CACHE: Deteksi jika nilai .env terbaca null / kosong
        if (empty($anonKey) || empty($serviceKey) || empty($supabaseUrl)) {
            return redirect()->back()->with('error', 'Sistem gagal membaca file .env Anda. Selesaikan dengan menjalankan perintah "php artisan config:clear" di terminal Anda.');
        }

        // 2. TAHAP 1: Verifikasi kata sandi lama (Login Klien Publik)
        $authCheck = Http::withHeaders([
            'apikey'       => $anonKey,
            'Content-Type' => 'application/json',
        ])->post($supabaseUrl . '/auth/v1/token?grant_type=password', [
            'email'    => trim($user->email),
            'password' => $request->current_password,
        ]);

        if ($authCheck->failed()) {
            $supabaseError = $authCheck->json();
            $realReason = $supabaseError['error_description'] ?? $supabaseError['message'] ?? 'Kata sandi lama salah.';
            return redirect()->back()->withErrors([
                'current_password' => 'Verifikasi Gagal: ' . $realReason
            ]);
        }

        // 3. TAHAP 2: Simpan Kata Sandi Baru via Admin API Supabase
        $supabaseAdmin = Http::withHeaders([
            'apikey'        => $serviceKey,
            'Authorization' => 'Bearer ' . $serviceKey,
            'Content-Type'  => 'application/json',
        ])->put($supabaseUrl . '/auth/v1/admin/users/' . trim($user->supabase_id), [
            'password' => $request->password
        ]);

        // Jika proses penyimpanan gagal
        if ($supabaseAdmin->failed()) {
            $adminError = $supabaseAdmin->json();
            $adminReason = $adminError['message'] ?? $adminError['msg'] ?? $supabaseAdmin->body();

            return redirect()->back()->withErrors([
                'password' => 'Supabase Menolak: ' . $adminReason
            ]);
        }

        // 4. TAHAP 3: Sinkronisasi otomatis ke database MySQL lokal
        if (Schema::hasColumn('users', 'password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return redirect()->route('profile.edit')->with('success_password', 'Kata sandi Anda berhasil diperbarui di server Supabase!');
    }

    
    // ... fungsionalitas destroy() atau switchRole() di bawahnya tetap biarkan ...
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}