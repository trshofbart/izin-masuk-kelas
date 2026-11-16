<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        // Percayai header X-Forwarded-* dari proxy/tunnel (Cloudflare Tunnel,
        // reverse proxy, dll). Tanpa ini Laravel mengira request datang lewat
        // HTTP biasa, sehingga URL CSS/JS dibuat dengan http:// — dan browser
        // memblokirnya sebagai mixed content saat halaman dibuka lewat
        // https:// (domain tunnel), akibatnya CSS tidak terload.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
