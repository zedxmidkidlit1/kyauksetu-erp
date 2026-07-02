<?php

namespace App\Services\Kai;

use App\Services\Kai\Contracts\AiResponder;

class ExternalAiResponder implements AiResponder
{
    public function __construct(
        private readonly LocalAiResponder $fallbackResponder,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     * @return array{reply: string, suggestions: array<int, string>}
     */
    public function respond(string $message, array $context): array
    {
        if (! $this->isConfigured()) {
            return $this->fallbackResponder->respond($message, $context);
        }

        return [
            'reply' => sprintf(
                'KAI provider integration is configured for %s, but live provider calls are disabled in this foundation.',
                config('kai.provider.model'),
            ),
            'suggestions' => [
                'Show my timetable',
                'Check unpaid fees',
                'Show latest results',
            ],
        ];
    }

    private function isConfigured(): bool
    {
        return (bool) config('kai.provider.enabled')
            && filled(config('kai.provider.endpoint'))
            && filled(config('kai.provider.api_key'));
    }
}
