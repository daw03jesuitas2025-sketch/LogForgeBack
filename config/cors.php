<?php
// Para comunicar Laravel con Angular
return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // he añadido la url de Vercel
    'allowed_origins' => [
        'https://logforge-front.vercel.app',
        'https://logforgefront-production.up.railway.app',
        'http://localhost:4200' // Para poder seguir probando en local
    ],
    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
