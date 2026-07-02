<?php

namespace App\Services\Kai\Contracts;

use App\Models\User;

interface AiResponder
{
    /**
     * @param  array<string, mixed>  $context
     * @return array{reply: string, suggestions: array<int, string>}
     */
    public function respond(string $message, array $context, ?User $user = null): array;
}
