<?php

return [
    'base_url' => env('AI_BASE_URL'),
    'api_key' => env('AI_API_KEY'),
    'model' => env('AI_MODEL'),
    'timeout' => (int) env('AI_TIMEOUT', 30),
];
