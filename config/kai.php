<?php

return [
    'responder' => env('KAI_RESPONDER', 'local'),

    'provider' => [
        'enabled' => env('KAI_PROVIDER_ENABLED', false),
        'name' => env('KAI_AI_PROVIDER', env('KAI_PROVIDER_NAME', 'openai')),
        'model' => env('KAI_AI_MODEL', env('KAI_PROVIDER_MODEL', 'gpt-4o-mini')),
        'endpoint' => env('KAI_PROVIDER_ENDPOINT'),
        'api_key' => env('KAI_PROVIDER_API_KEY'),
        'timeout' => env('KAI_PROVIDER_TIMEOUT', 30),
        'max_context_items' => env('KAI_MAX_CONTEXT_ITEMS', 25),
        'max_response_tokens' => env('KAI_MAX_RESPONSE_TOKENS', 512),
    ],
];
