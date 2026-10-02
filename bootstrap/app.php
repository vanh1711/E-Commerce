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
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('admin*') || $request->is('login')) {
                return response(
                    "<div style='font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;padding:30px;background:#0f172a;color:#f8fafc;min-height:100vh;box-sizing:border-box;'>" .
                    "<div style='max-width:900px;margin:0 auto;background:#1e293b;padding:25px;border-radius:16px;border:1px solid #334155;box-shadow:0 10px 25px rgba(0,0,0,0.3);'>" .
                    "<h2 style='color:#ef4444;margin-top:0;font-size:20px;'>❌ Lỗi Quản Trị: " . htmlspecialchars($e->getMessage()) . "</h2>" .
                    "<p style='color:#94a3b8;font-size:14px;'><strong>File:</strong> <span style='color:#f1f5f9;font-family:monospace;'>" . htmlspecialchars($e->getFile()) . "</span> (Dòng: <strong style='color:#38bdf8;'>" . $e->getLine() . "</strong>)</p>" .
                    "<h3 style='color:#38bdf8;font-size:15px;margin-top:20px;'>Chi tiết Stack Trace:</h3>" .
                    "<pre style='background:#090d16;color:#e2e8f0;padding:15px;border-radius:10px;overflow:auto;max-height:400px;font-size:12px;line-height:1.5;border:1px solid #1e293b;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>" .
                    "</div>" .
                    "</div>",
                    500
                )->header('Content-Type', 'text/html; charset=utf-8');
            }
        });
    })->create();