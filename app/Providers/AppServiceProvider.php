<?php

namespace App\Providers;

use App\Support\Verifiable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Polymorphic *_type columns store these aliases, never class names.
        Relation::enforceMorphMap(Verifiable::morphMap());

        Model::preventLazyLoading(! $this->app->isProduction());

        Password::defaults(fn () => Password::min(8));

        // 5 login attempts per minute per username + IP (security-standards §1).
        // The key is normalised like LoginRequest does, so " Admin " shares the bucket of "admin".
        RateLimiter::for('login', function (Request $request) {
            $username = $request->input('username');

            return Limit::perMinute(5)
                ->by(Str::lower(trim(is_string($username) ? $username : '')).'|'.$request->ip())
                ->response(fn (Request $request, array $headers) => back()
                    ->withInput($request->only('username', 'remember'))
                    ->withErrors(['username' => __('auth.throttle', ['seconds' => $headers['Retry-After'] ?? 60])]));
        });
    }
}
