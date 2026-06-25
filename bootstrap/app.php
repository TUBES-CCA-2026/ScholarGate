<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/*
|--------------------------------------------------------------------------
| Laravel Application Bootstrap
|--------------------------------------------------------------------------
|
| File ini mendaftarkan route utama, command console, health check, dan alias
| middleware aplikasi. Alias admin dan student dipakai pada routes/web.php
| untuk memisahkan area kerja sesuai role pengguna.
|
*/

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'student' => \App\Http\Middleware\EnsureStudent::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Exception handling khusus dapat didaftarkan di sini bila aplikasi berkembang.
    })->create();
