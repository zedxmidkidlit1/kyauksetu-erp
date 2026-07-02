<?php

namespace App\Services\Kai;

use App\Models\User;
use App\Services\Kai\Contracts\AiResponder;

class KaiResponder
{
    public function __construct(
        private readonly StudentContextBuilder $contextBuilder,
        private readonly AiResponder $aiResponder,
    ) {}

    /**
     * @return array{reply: string, context_used: array{keys: array<int, string>}, suggestions: array<int, string>, request_id?: string}
     */
    public function respondFor(User $user, string $message, ?string $requestId = null): array
    {
        $context = $this->contextBuilder->buildFor($user);
        $response = $this->aiResponder->respond($message, $context);

        $payload = [
            'reply' => $response['reply'],
            'context_used' => [
                'keys' => array_values(array_diff(array_keys($context), ['generated_at'])),
            ],
            'suggestions' => $response['suggestions'],
        ];

        if ($requestId) {
            $payload['request_id'] = $requestId;
        }

        return $payload;
    }
}
