<?php

use Monolog\Handler\StreamHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => explode(',', env('LOG_CHANNELS')),
        ],
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'formatter' => Monolog\Formatter\LineFormatter::class,
            'formatter_with' => [
                'format' => "%level_name% %context% %datetime% %message%\n",
            ],
            'level' => 'debug',
            'bubble' => false,
            'permission' => 0777,
        ],
        'info' => [
            'driver' => 'daily',
            'path' => storage_path('logs/info/info.log'),
            'formatter' => Monolog\Formatter\LineFormatter::class,
            'formatter_with' => [
                'format' => "%level_name% %datetime% %context% %message%\n",
            ],
            'level' => 'debug',
            'bubble' => false,
            'permission' => 0777,
            'days' => 60,
        ],
        'admins' => [
            'driver' => 'daily',
            'path' => storage_path('logs/admins/info.log'),
            'formatter' => Monolog\Formatter\LineFormatter::class,
            'formatter_with' => [
                'format' => "%level_name% %datetime% %context% %message%\n",
            ],
            'level' => 'debug',
            'bubble' => false,
            'permission' => 0777,
            'days' => 30,
        ],
        'downloads' => [
            'driver' => 'single',
            'path' => storage_path('logs/downloads.log'),
            'formatter' => Monolog\Formatter\LineFormatter::class,
            'formatter_with' => [
                'format' => "%level_name% %datetime% %context% %message%\n",
            ],
            'level' => 'info',
            'bubble' => false,
            'permission' => 0777,
        ],
        'webhooks' => [
            'driver' => 'daily',
            'path' => storage_path('logs/webhooks/webhook.log'),
            'formatter' => Monolog\Formatter\LineFormatter::class,
            'formatter_with' => [
                'format' => "%level_name% %datetime% %context% %message%\n",
            ],
            'level' => 'info',
            'bubble' => false,
            'permission' => 0777,
            'days' => 90,
        ],
        'hashfile' => [
            'driver' => 'single',
            'path' => storage_path('logs/hashfile.log'),
            // 'formatter' => Monolog\Formatter\LineFormatter::class,
            'formatter_with' => [
                'format' => "%level_name% %context% %datetime% %message%\n",
            ],
            'level' => 'info',
            'bubble' => false,
        ],
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 7,
            'permission' => 0777,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Arabsstock Log',
            'emoji' => ':boom:',
            'level' => 'error',
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],
    ],

];
