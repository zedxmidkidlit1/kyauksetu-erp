<?php

namespace App\Services\Kai;

use App\Models\User;
use App\Services\Kai\Contracts\AiResponder;
use Illuminate\Support\Arr;
use Laravel\Ai\AnonymousAgent;
use Throwable;

class ExternalAiResponder implements AiResponder
{
    private const SUGGESTIONS = [
        'Show my timetable',
        'Check unpaid fees',
        'Show latest results',
    ];

    public function __construct(
        private readonly LocalAiResponder $fallbackResponder,
        private readonly KaiPromptBuilder $promptBuilder,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     * @return array{reply: string, suggestions: array<int, string>}
     */
    public function respond(string $message, array $context, ?User $user = null): array
    {
        if (! $this->isConfigured() || ! $user instanceof User) {
            return $this->fallbackResponder->respond($message, $context, $user);
        }

        $this->configureProvider();
        $promptData = $this->promptBuilder->build($user, $message, $context);

        try {
            $response = (new AnonymousAgent(
                $promptData['system_instructions'],
                [],
                [],
            ))->prompt(
                $this->prompt($promptData),
                provider: $this->providerName(),
                model: $this->modelName(),
                timeout: (int) config('kai.provider.timeout', 30),
            );
        } catch (Throwable) {
            return $this->fallbackResponder->respond($message, $context, $user);
        }

        return [
            'reply' => filled($response->text) ? trim($response->text) : $this->fallbackResponder->respond($message, $context, $user)['reply'],
            'suggestions' => self::SUGGESTIONS,
        ];
    }

    private function isConfigured(): bool
    {
        return (bool) config('kai.provider.enabled')
            && filled($this->providerName())
            && filled($this->modelName())
            && filled(config('kai.provider.api_key'));
    }

    private function configureProvider(): void
    {
        $provider = $this->providerName();

        config()->set("ai.providers.{$provider}.key", config('kai.provider.api_key'));

        if (filled(config('kai.provider.endpoint'))) {
            config()->set("ai.providers.{$provider}.url", config('kai.provider.endpoint'));
        }
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function prompt(array $context): string
    {
        return json_encode(Arr::except($context, ['system_instructions']), JSON_THROW_ON_ERROR);
    }

    private function providerName(): string
    {
        return (string) config('kai.provider.name', 'openai');
    }

    private function modelName(): string
    {
        return (string) config('kai.provider.model', 'gpt-4o-mini');
    }
}
