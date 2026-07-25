<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:3000',
        'https://eva-netting-hub.vercel.app',
    ],
    'allowed_origins_patterns' => [
        '/^https:\/\/eva-netting-hub-.*\.vercel\.app$/',
        '/^https:\/\/.*-abo7tabs-projects\.vercel\.app$/',
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
