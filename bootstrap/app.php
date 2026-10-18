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
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'emergency-access.unlocked' => \Modules\EmergencyAccess\Http\Middleware\EnsureVaultUnlocked::class,
        ]);

        // API responses are sent plain: encrypted DB columns are decrypted by their
        // Eloquent `encrypted` casts. Any leftover "enc:" values the frontend sends
        // back are still decrypted here, before validation.
        $middleware->api(prepend: [
            \Modules\Core\Http\Middleware\DecryptApiRequest::class,
        ]);

        $middleware->redirectGuestsTo('/admin/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API errors (validation, auth, 404...) are always JSON, even without an Accept header,
        // instead of redirecting to the website's home page.
        $exceptions->shouldRenderJsonWhen(fn ($request) => $request->is('api/*') || $request->expectsJson());

        // Request body exceeded PHP's post_max_size: return a clear JSON error instead of an empty 413.
        $exceptions->render(fn (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) => response()->json([
            'success' => false,
            'message' => 'The uploaded file is too large. Maximum allowed size is '.ini_get('post_max_size').'.',
            'errors' => null,
        ], 413));
    })->create();
