<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],
   'braintree' => [
    'model'  => App\Models\User::class,
    'environment' => env('BRAINTREE_ENV'),
    'merchant_id' => env('BRAINTREE_MERCHANT_ID'),
    'public_key' => env('BRAINTREE_PUBLIC_KEY'),
    'private_key' => env('BRAINTREE_PRIVATE_KEY'),
    ],
    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'version' => '2020-08-27',
    ],

    'facebook' => [
      'client_id' => "773108557315183", // configure with your app id
      'client_secret' => 'd3fc266a405a5000f439ce45c33eb59b', // your app secret
      'redirect' => ('/oauth/facebook/callback'), // IMPORTANT NOT REMOVE /oauth/facebook/callback
      //'redirect' => app('url')->to('/oauth/facebook/callback'), // IMPORTANT NOT REMOVE /oauth/facebook/callback
      ],


      'google' => [
        'client_id' => env('CLIENT_ID_GOOGLE'),
        'client_secret' => env('CLIENT_SECRET_GOOGLE'),
        'redirect' => env('REDIRECT_GOOGLE'),
        ],

    'twitter' => [
      'client_id' => "APP_ID", // configure with your app id
      'client_secret' => 'APP_SECRET', // your app secret
      'redirect' => 'http://YOURSITE.COM/oauth/twitter/callback', // IMPORTANT NOT REMOVE /oauth/twitter/callback
      ],
    'sendgrid' => [
        'key' => env('SENDGRID_API_KEY'),
        'webhook_key' => env('SENDGRID_WEBHOOK_KEY'),
    ],
    'search_by_image' => [
        'url' => env('SEARCH_BY_IMAGE_URL'),
        'key' => env('SEARCH_BY_IMAGE_KEY'),
    ],
    'embedding_watermark' => [
        'host' => env('EMBEDDINGWATERMARK',"https://ai.arabsstock.com/watermark-api"),
        'endpoint' => "/embedding_watermark",
    ],

    'removebg' => [
        'free'=>[
            'api_key'=>env('REMOVE_BG_API_KEY_FREE'),
            'endpoint'=>env('REMOVE_BG_API_KEY_FREE_ENDPOINT',"http://159.89.105.253:5000/api/removebg")

        ],
        'paid'=>[
            'api_key'=>env('REMOVE_BG_API_KEY_PAID'),
            'endpoint'=>env('REMOVE_BG_API_KEY_PAID_ENDPOINT',"https://api.remove.bg/v1.0/removebg")

        ],
    ],
    'elasticsearch'=>[
        'user'=>env('ELASTICSEARCH_USER'),
        'password'=>env('ELASTICSEARCH_PASSWORD'),
        'endpoint'=>env('ELASTICSEARCH_ENDPOINT'),
    ]

];
