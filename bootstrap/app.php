<?php

use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class, 
            'admin.only' => \App\Http\Middleware\AdminOnly::class, 
        ]);
    })
    ->withExceptions(function ($exceptions) {
        // Gère les exceptions ici si nécessaire
    })
    ->create();



    