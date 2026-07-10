<?php

namespace App\Exceptions;

use RuntimeException;

class KaiProviderException extends RuntimeException
{
    public function __construct(
        private readonly string $provider,
        private readonly string $model,
    ) {
        parent::__construct('The external KAI provider request failed.');
    }

    /**
     * @return array{provider: string, model: string}
     */
    public function context(): array
    {
        return [
            'provider' => $this->provider,
            'model' => $this->model,
        ];
    }
}
