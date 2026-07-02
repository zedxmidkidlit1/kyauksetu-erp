<?php

return [
    'responder' => env('KAI_RESPONDER', 'local'),

    'provider' => [
        'enabled' => env('KAI_PROVIDER_ENABLED', false),
        'name' => env('KAI_PROVIDER_NAME', 'external'),
        'model' => env('KAI_PROVIDER_MODEL', 'kai-provider-model'),
        'endpoint' => env('KAI_PROVIDER_ENDPOINT'),
        'api_key' => env('KAI_PROVIDER_API_KEY'),
        'max_context_items' => env('KAI_MAX_CONTEXT_ITEMS', 25),
        'max_response_tokens' => env('KAI_MAX_RESPONSE_TOKENS', 512),
    ],
];
