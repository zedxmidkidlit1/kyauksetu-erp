<?php

namespace App\Services\Kai;

use App\Models\KaiChatSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KaiChatLogger
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{driver: string|null, provider: string|null, model: string|null}
     */
    public function logExchange(User $user, string $message, array $payload, ?string $requestId = null): array
    {
        $provider = $this->providerMetadata();

        DB::transaction(function () use ($user, $message, $payload, $requestId, $provider): void {
            $session = $this->sessionFor($user, $message, $provider);
            $contextKeys = data_get($payload, 'context_used.keys', []);

            $session->messages()->create([
                'user_id' => $user->id,
                'role' => 'user',
                'content' => $this->safeContent($message),
                'context_keys' => null,
                ...$provider,
                'metadata' => $requestId ? ['request_id' => $requestId] : null,
            ]);

            $session->messages()->create([
                'user_id' => $user->id,
                'role' => 'assistant',
                'content' => $this->safeContent((string) ($payload['reply'] ?? '')),
                'context_keys' => is_array($contextKeys) ? array_values($contextKeys) : [],
                ...$provider,
                'metadata' => $requestId ? ['request_id' => $requestId] : null,
            ]);

            $session->update([
                ...$provider,
                'last_message_at' => now(),
            ]);
        });

        return $provider;
    }

    /**
     * @param  array{driver: string|null, provider: string|null, model: string|null}  $provider
     */
    private function sessionFor(User $user, string $message, array $provider): KaiChatSession
    {
        $session = $user->kaiChatSessions()
            ->where('status', 'active')
            ->latest('last_message_at')
            ->latest()
            ->first();

        if ($session instanceof KaiChatSession) {
            return $session;
        }

        return $user->kaiChatSessions()->create([
            'title' => Str::limit($this->safeContent($message), 80, ''),
            ...$provider,
            'last_message_at' => now(),
        ]);
    }

    /**
     * @return array{driver: string|null, provider: string|null, model: string|null}
     */
    private function providerMetadata(): array
    {
        $driver = (string) config('kai.responder', 'local');

        return [
            'driver' => $driver,
            'provider' => $driver === 'external' ? (string) config('kai.provider.name') : null,
            'model' => $driver === 'external' ? (string) config('kai.provider.model') : null,
        ];
    }

    private function safeContent(string $content): string
    {
        $content = preg_replace(
            '/\b(api[_-]?key|token|secret|password)\s*[:=]\s*["\']?[^\\s"\']+/i',
            '$1=[redacted]',
            $content,
        ) ?? $content;

        return preg_replace('/\bsk-[A-Za-z0-9_-]{10,}\b/', '[redacted-api-key]', $content) ?? $content;
    }
}
