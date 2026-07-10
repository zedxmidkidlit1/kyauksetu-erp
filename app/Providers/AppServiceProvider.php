<?php

namespace App\Providers;

use App\Services\Kai\Contracts\AiResponder;
use App\Services\Kai\ExternalAiResponder;
use App\Services\Kai\LocalAiResponder;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AiResponder::class, function (): AiResponder {
            return match (config('kai.responder', 'local')) {
                'external' => $this->app->make(ExternalAiResponder::class),
                default => $this->app->make(LocalAiResponder::class),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('web-login', function (Request $request): Limit {
            $email = $request->string('email')->lower()->toString();

            return Limit::perMinute((int) config('rate_limits.web_login_per_minute'))
                ->by($email.'|'.$request->ip());
        });

        RateLimiter::for('registration', fn (Request $request): Limit => Limit::perMinute(
            (int) config('rate_limits.registration_per_minute'),
        )->by($request->ip()));

        RateLimiter::for('mobile-login', function (Request $request): Limit {
            $email = $request->string('email')->lower()->toString();

            return Limit::perMinute((int) config('rate_limits.mobile_login_per_minute'))
                ->by($email.'|'.$request->ip());
        });

        RateLimiter::for('kai-chat', function (Request $request): Limit {
            $userIdentifier = $request->user()?->getAuthIdentifier() ?? 'guest';

            return Limit::perMinute((int) config('rate_limits.kai_chat_per_minute'))
                ->by($userIdentifier.'|'.$request->ip());
        });
    }
}
