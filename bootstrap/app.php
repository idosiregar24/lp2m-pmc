<?php

use App\Exceptions\InvalidStatusTransition;
use App\Http\Middleware\AddNoIndexHeader;
use App\Http\Middleware\EnsurePasswordChanged;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\EnsureUserIsActive;
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
            'role' => EnsureRole::class,
            'active' => EnsureUserIsActive::class,
            'password.changed' => EnsurePasswordChanged::class,
        ]);

        $middleware->web(append: [AddNoIndexHeader::class]);

        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Verification state-machine refusals are user-facing messages, not errors.
        $exceptions->render(function (InvalidStatusTransition $e) {
            return back()->withInput()->with('error', $e->getMessage());
        });
    })->create();
