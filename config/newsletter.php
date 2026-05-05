<?php

return [
    'mail' => [
        'driver' => env('NEWSLETTER_MAIL_DRIVER'),
        'host' => env('NEWSLETTER_MAIL_HOST'),
        'port' => env('NEWSLETTER_MAIL_PORT'),
        'username' => env('NEWSLETTER_MAIL_USERNAME'),
        'encryption' => env('NEWSLETTER_MAIL_ENCRYPTION', 'tls'),
        'from' => [
            'address' => env('NEWSLETTER_MAIL_FROM_ADDRESS', 'no-reply@arabsstock.com'),
            'name' => env('NEWSLETTER_MAIL_FROM_NAME', 'Arabstock'),
        ],
        'password' => env('NEWSLETTER_MAIL_PASSWORD'),
    ]
];
