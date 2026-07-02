<?php

return [
    'responder' => env('KAI_RESPONDER', 'local'),

    'system_prompt' => env('KAI_SYSTEM_PROMPT') ?: 'You are KAI, a concise student ERP assistant. Answer only from the provided student context.',

    'context_limits' => [
        'timetable_items' => (int) env('KAI_CONTEXT_TIMETABLE_ITEMS', 5),
        'announcements' => (int) env('KAI_CONTEXT_ANNOUNCEMENTS', 3),
        'attendance_items' => (int) env('KAI_CONTEXT_ATTENDANCE_ITEMS', 5),
        'result_items' => (int) env('KAI_CONTEXT_RESULT_ITEMS', 5),
        'fee_items' => (int) env('KAI_CONTEXT_FEE_ITEMS', 5),
        'library_loan_items' => (int) env('KAI_CONTEXT_LIBRARY_LOAN_ITEMS', 5),
    ],

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
