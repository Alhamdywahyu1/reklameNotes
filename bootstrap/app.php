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
    ->withMiddleware(function (Middleware $middleware): void {
        // Di balik nginx/Apache/load balancer: agar HTTPS terdeteksi benar, cookie sesi konsisten,
        // dan validasi CSRF (419) tidak gagal karena sesi hilang antar request.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'verify_email' => \App\Http\Middleware\VerifyEmail::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
