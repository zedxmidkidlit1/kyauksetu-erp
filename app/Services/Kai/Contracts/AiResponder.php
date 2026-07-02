<?php

namespace App\Services\Kai\Contracts;

interface AiResponder
{
    /**
     * @param  array<string, mixed>  $context
     * @return array{reply: string, suggestions: array<int, string>}
     */
    public function respond(string $message, array $context): array;
}
