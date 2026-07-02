<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Laravel\Ai\AnonymousAgent;
use Throwable;

#[Signature('kai:smoke {--send : Send one tiny external provider prompt when KAI external AI is enabled and configured}')]
#[Description('Check KAI external AI development smoke configuration')]
class KaiSmokeCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $driver = (string) config('kai.responder', 'local');
        $providerEnabled = (bool) config('kai.provider.enabled');
        $provider = (string) config('kai.provider.name', '');
        $model = (string) config('kai.provider.model', '');
        $hasKey = filled(config('kai.provider.api_key'));

        $this->line('KAI responder: '.$driver);

        if ($driver !== 'external') {
            $this->info('External AI smoke skipped. KAI_RESPONDER is not external.');

            return self::SUCCESS;
        }

        $this->line('KAI provider enabled: '.($providerEnabled ? 'yes' : 'no'));
        $this->line('KAI provider: '.($provider ?: 'missing'));
        $this->line('KAI model: '.($model ?: 'missing'));
        $this->line('KAI provider key: '.($hasKey ? 'configured' : 'missing'));

        if (! $providerEnabled || blank($provider) || blank($model) || ! $hasKey) {
            $this->error('External AI smoke is not ready. Check KAI_PROVIDER_ENABLED, KAI_AI_PROVIDER, KAI_AI_MODEL, and KAI_PROVIDER_API_KEY.');

            return self::FAILURE;
        }

        $this->configureProvider($provider);

        if (! $this->option('send')) {
            $this->info('External AI config is ready. Re-run with --send to send a tiny dev smoke prompt.');

            return self::SUCCESS;
        }

        try {
            $response = (new AnonymousAgent(
                'You are KAI smoke test. Reply with a short confirmation only.',
                [],
                [],
            ))->prompt(
                'Reply with: KAI external smoke ok.',
                provider: $provider,
                model: $model,
                timeout: (int) config('kai.provider.timeout', 30),
            );
        } catch (Throwable) {
            $this->error('External AI smoke failed safely. Check provider credentials, model, endpoint, and network access.');

            return self::FAILURE;
        }

        $this->info('External AI smoke response received.');
        $this->line('Reply: '.trim($response->text));

        return self::SUCCESS;
    }

    private function configureProvider(string $provider): void
    {
        config()->set("ai.providers.{$provider}.key", config('kai.provider.api_key'));

        if (filled(config('kai.provider.endpoint'))) {
            config()->set("ai.providers.{$provider}.url", config('kai.provider.endpoint'));
        }
    }
}
