<?php

use App\Http\Middleware\ActiveUserMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CustomerMiddleware;
use App\Http\Middleware\StaffMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->validateCsrfTokens(except: [
            'webhooks/maya',
        ]);

        $middleware->alias([
            'active' => ActiveUserMiddleware::class,
            'admin' => AdminMiddleware::class,
            'staff' => StaffMiddleware::class,
            'customer' => CustomerMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
