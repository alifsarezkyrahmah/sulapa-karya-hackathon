<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SupabaseAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'password' => 'required|min:6'
        ]);

        if (User::where('email', $request->email)->exists()) {
            return back()->withErrors([
                'email' => 'Email sudah terdaftar.'
            ])->withInput();
        }

        // 1. Daftarkan akun baru ke Supabase
        $response = Http::withHeaders([
            'apikey' => config('services.supabase.anon_key'),
            'Content-Type' => 'application/json',
        ])->post(
            config('services.supabase.url') . '/auth/v1/signup',
            [
                'email' => trim($request->email),
                'password' => $request->password,
            ]
        );

        if (!$response->successful()) {
            return back()->withErrors([
                'register' => $response->json()['msg']
                    ?? $response->json()['error_description']
                    ?? 'Registrasi gagal'
            ])->withInput();
        }

        $data = $response->json();

        $supabaseId = $data['id'] ?? $data['user']['id'] ?? null;

        if (!$supabaseId) {
            return back()->withErrors([
                'register' => 'Gagal mengambil data akun dari Supabase.'
            ])->withInput();
        }

        // 2. Simpan ke MySQL
        User::create([
            'supabase_id' => $supabaseId,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'user',
            'qr_code' => Str::uuid(),
        ]);

        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // ========================================================
        // LOGIKA BARU: CEK EMAIL DI DATABASE LOKAL TERLEBIH DAHULU
        // ========================================================
        $userExists = User::where('email', trim($request->email))->exists();
        
        if (!$userExists) {
            return back()->withInput()->withErrors([
                'email' => 'Alamat email ini belum terdaftar dalam gerakan kami.'
            ]);
        }

        // Jika email ada di database lokal, lanjutkan proses verifikasi password ke Supabase
        $response = Http::withHeaders([
            'apikey' => config('services.supabase.anon_key'),
            'Content-Type' => 'application/json',
        ])->post(
            config('services.supabase.url') . '/auth/v1/token?grant_type=password',
            [
                'email' => trim($request->email),
                'password' => $request->password,
            ]
        );

        if (! $response->successful()) {
            // Jika gagal di sini, berarti email ada tapi password-nya yang keliru
            return back()->withInput()->withErrors([
                'password' => 'Kata sandi yang Anda masukkan salah.',
            ]);
        }

        $data = $response->json();

        $user = User::where(
            'supabase_id',
            $data['user']['id']
        )->first();

        if (!$user) {
            return back()->withErrors([
                'login' => 'Data user tidak ditemukan di database aplikasi.'
            ]);
        }

        session([
            'user_id' => $user->id,
            'supabase_id' => $user->supabase_id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
        ]);

        return redirect('/')->with('success', 'Login berhasil! Selamat datang kembali, ' . $user->name . '.');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Anda berhasil logout.');
    }

    public function redirectToGoogle()
    {
        $supabaseUrl = config('services.supabase.url');
        $googleAuthUrl = $supabaseUrl . '/auth/v1/authorize?provider=google';

        return redirect()->away($googleAuthUrl);
    }

    public function setPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:6'
        ]);

        $user = User::find(session('user_id'));

        $user->update([
            'transaction_pin' => Hash::make($request->pin),
            'transaction_pin_set_at' => now(),
        ]);

        return redirect('/dashboard')->with('success', 'PIN berhasil dibuat');
    }
}