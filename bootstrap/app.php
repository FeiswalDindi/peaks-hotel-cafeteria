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
        // ✅ Fixes Spatie Conflict
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // ✅ NEW: Allow Safaricom to hit our webhook without a CSRF token
        $middleware->validateCsrfTokens(except: [
            'api/mpesa/callback', 
        ]);
    })
 
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();