<?php

namespace App\Services\Kai\Contracts;

use App\Services\Kai\LocalAiResponder;
use Illuminate\Container\Attributes\Bind;

#[Bind(LocalAiResponder::class)]
interface AiResponder
{
    /**
     * @param  array<string, mixed>  $context
     * @return array{reply: string, suggestions: array<int, string>}
     */
    public function respond(string $message, array $context): array;
}
