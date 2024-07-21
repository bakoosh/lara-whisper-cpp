<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'transcribe'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'], // Adjust this if you need to specify allowed origins


    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['X-Custom-Header'],

    'max_age' => 3600,

    'supports_credentials' => false, // Отключаем для curl запросов
];
