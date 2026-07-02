<?php

namespace App\Providers;

use App\Services\Kai\Contracts\AiResponder;
use App\Services\Kai\ExternalAiResponder;
use App\Services\Kai\LocalAiResponder;
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
        //
    }
}
