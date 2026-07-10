<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil data user aktif dari MySQL berdasarkan session user_id
        $user = User::find(session('user_id'));

        // LOGIKA BARU: Cek apakah user ditemukan, dan apakah alamat masih kosong
        if (!$user || empty($user->address)) {
            // Langsung alihkan ke halaman edit profil dengan pesan khusus alamat
            return redirect()->route('profile.edit')
                ->with('error', 'Silakan lengkapi lokasi atau alamat penjemputan Anda di Makassar terlebih dahulu untuk menggunakan fitur Setor Sampah.');
        }

        return $next($request);
    }
}