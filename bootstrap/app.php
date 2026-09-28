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
        // Tin cậy Reverse Proxy từ Render (HTTPS / SSL termination)
        $middleware->trustProxies(at: '*');

        // Đăng ký alias 'admin' cho Laravel 11
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // Bypass CSRF cho các Webhook từ cổng bên thứ 3 (GHN, MoMo IPN)
        $middleware->validateCsrfTokens(except: [
            'api/ghn/webhook',
            'ghn/webhook',
            'payment/momo/ipn',
            'payment/momo/*',
            'payment/ipn',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();