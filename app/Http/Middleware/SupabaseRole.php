<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class SupabaseRole
{
    /**
     * Memastikan user yang masuk memiliki role yang sesuai dengan rute.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Ambil data user langsung dari database secara real-time
        $user = User::find(session('user_id'));

        // Cek jika akun tidak ditemukan ATAU role di DB tidak sesuai dengan yang diminta rute
        if (!$user || $user->role !== $role) {
            abort(403, 'Maaf, Anda tidak memiliki hak akses untuk melihat halaman ini.');
        }

        return $next($request);
    }
}