<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SupabaseRole
{
    /**
     * Memastikan user yang masuk memiliki role yang sesuai dengan rute.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek jika session role tidak sama dengan parameter yang diminta rute
        if (session('role') !== $role) {
            abort(403, 'Maaf, Anda tidak memiliki hak akses untuk melihat halaman ini.');
        }

        return $next($request);
    }
}