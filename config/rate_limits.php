<?php

return [
    'web_login_per_minute' => (int) env('WEB_LOGIN_RATE_LIMIT', 5),
    'registration_per_minute' => (int) env('REGISTRATION_RATE_LIMIT', 3),
    'mobile_login_per_minute' => (int) env('MOBILE_LOGIN_RATE_LIMIT', 10),
    'kai_chat_per_minute' => (int) env('KAI_CHAT_RATE_LIMIT', 20),
];
