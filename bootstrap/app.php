<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Gabungkan seluruh pendaftaran alias di sini agar tidak konflik
        $middleware->alias([
            'supabase.auth'  => \App\Http\Middleware\SupabaseAuth::class,
            'supabase.guest' => \App\Http\Middleware\SupabaseGuest::class,
            'supabase.role'  => \App\Http\Middleware\SupabaseRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();