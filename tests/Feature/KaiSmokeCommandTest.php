<?php

namespace Tests\Feature;

use Laravel\Ai\AnonymousAgent;
use Tests\TestCase;

class KaiSmokeCommandTest extends TestCase
{
    public function test_smoke_command_confirms_local_default_without_external_call(): void
    {
        config([
            'kai.responder' => 'local',
            'kai.provider.api_key' => 'super-secret-test-key',
        ]);

        AnonymousAgent::fake()->preventStrayPrompts();

        $this
            ->artisan('kai:smoke')
            ->expectsOutput('KAI responder: local')
            ->expectsOutput('External AI smoke skipped. KAI_RESPONDER is not external.')
            ->assertExitCode(0);

        AnonymousAgent::assertNeverPrompted();
    }

    public function test_smoke_command_missing_external_config_fails_safely_without_secret_leak(): void
    {
        config([
            'kai.responder' => 'external',
            'kai.provider.enabled' => true,
            'kai.provider.name' => 'openai',
            'kai.provider.model' => 'kai-test-model',
            'kai.provider.api_key' => null,
        ]);

        AnonymousAgent::fake()->preventStrayPrompts();

        $this
            ->artisan('kai:smoke')
            ->expectsOutput('KAI provider key: missing')
            ->expectsOutput('External AI smoke is not ready. Check KAI_PROVIDER_ENABLED, KAI_AI_PROVIDER, KAI_AI_MODEL, and KAI_PROVIDER_API_KEY.')
            ->assertExitCode(1);

        AnonymousAgent::assertNeverPrompted();
    }

    public function test_smoke_command_config_check_does_not_leak_configured_secret(): void
    {
        config([
            'kai.responder' => 'external',
            'kai.provider.enabled' => true,
            'kai.provider.name' => 'openai',
            'kai.provider.model' => 'kai-test-model',
            'kai.provider.api_key' => 'super-secret-test-key',
        ]);

        AnonymousAgent::fake()->preventStrayPrompts();

        $this
            ->artisan('kai:smoke')
            ->expectsOutput('KAI provider key: configured')
            ->expectsOutput('External AI config is ready. Re-run with --send to send a tiny dev smoke prompt.')
            ->doesntExpectOutput('super-secret-test-key')
            ->assertExitCode(0);

        AnonymousAgent::assertNeverPrompted();
    }

    public function test_smoke_command_send_uses_laravel_ai_fake_without_real_provider_call(): void
    {
        config([
            'kai.responder' => 'external',
            'kai.provider.enabled' => true,
            'kai.provider.name' => 'openai',
            'kai.provider.model' => 'kai-test-model',
            'kai.provider.api_key' => 'fake-provider-key',
        ]);

        AnonymousAgent::fake([
            'KAI external smoke ok.',
        ]);

        $this
            ->artisan('kai:smoke --send')
            ->expectsOutput('External AI smoke response received.')
            ->expectsOutput('Reply: KAI external smoke ok.')
            ->assertExitCode(0);

        AnonymousAgent::assertPrompted(function ($prompt): bool {
            return $prompt->contains('Reply with: KAI external smoke ok.')
                && $prompt->provider()->name() === 'openai'
                && $prompt->model === 'kai-test-model';
        });
    }
}
