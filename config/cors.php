<?php
// Para comunicar Laravel con Angular
return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // he añadido la url de Vercel
    'allowed_origins' => ['https://logforge-front.vercel.app/'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
