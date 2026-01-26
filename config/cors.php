<?php

return [

    'paths' => ['api/*', 'oauth/*'],  // Include Passport routes

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:3000', 'http://192.168.1.151:3000'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Authorization'],  // Expose token header

    'max_age' => 0,

    'supports_credentials' => true, // If you send cookies
];
