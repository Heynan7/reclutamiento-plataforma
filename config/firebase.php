<?php

declare(strict_types=1);

return [
    /*
     * ------------------------------------------------------------------------
     * Default Firebase project
     * ------------------------------------------------------------------------
     */
    'default' => env('FIREBASE_PROJECT', 'app'),

    /*
     * ------------------------------------------------------------------------
     * Firebase project configurations
     * ------------------------------------------------------------------------
     */
    'projects' => [
        'app' => [

            /*
             * ------------------------------------------------------------------------
             * Credentials / Service Account
             * ------------------------------------------------------------------------
             *
             * Fallback a storage/app/firebase/service-account.json si no hay env.
             */
       'credentials' => env(
            'FIREBASE_CREDENTIALS',
            storage_path('app/firebase/service-account.json')
        ),


            /*
             * ------------------------------------------------------------------------
             * Firebase Auth Component
             * ------------------------------------------------------------------------
             */
            'auth' => [
                'tenant_id' => env('FIREBASE_AUTH_TENANT_ID'),
            ],

            /*
             * ------------------------------------------------------------------------
             * Firestore
             * ------------------------------------------------------------------------
             */
            'firestore' => [
                // 'database' => env('FIREBASE_FIRESTORE_DATABASE'),
            ],

            /*
             * ------------------------------------------------------------------------
             * Realtime Database
             * ------------------------------------------------------------------------
             */
            'database' => [
                'url' => env('FIREBASE_DATABASE_URL'),
                // 'auth_variable_override' => ['uid' => 'my-service-worker'],
            ],

            /*
             * ------------------------------------------------------------------------
             * Dynamic Links
             * ------------------------------------------------------------------------
             */
            'dynamic_links' => [
                'default_domain' => env('FIREBASE_DYNAMIC_LINKS_DEFAULT_DOMAIN'),
            ],

            /*
             * ------------------------------------------------------------------------
             * Cloud Storage
             * ------------------------------------------------------------------------
             */
            'storage' => [
                // Acepta tu env FIREBASE_STORAGE_BUCKET o el estándar *_DEFAULT_BUCKET
                'default_bucket' => env('FIREBASE_STORAGE_DEFAULT_BUCKET', env('FIREBASE_STORAGE_BUCKET')),
            ],

            /*
             * ------------------------------------------------------------------------
             * Caching
             * ------------------------------------------------------------------------
             */
            'cache_store' => env('FIREBASE_CACHE_STORE', 'file'),

            /*
             * ------------------------------------------------------------------------
             * Logging
             * ------------------------------------------------------------------------
             */
            'logging' => [
                'http_log_channel' => env('FIREBASE_HTTP_LOG_CHANNEL'),
                'http_debug_log_channel' => env('FIREBASE_HTTP_DEBUG_LOG_CHANNEL'),
            ],

            /*
             * ------------------------------------------------------------------------
             * HTTP Client Options
             * ------------------------------------------------------------------------
             */
            'http_client_options' => [
                'proxy' => env('FIREBASE_HTTP_CLIENT_PROXY'),
                'timeout' => env('FIREBASE_HTTP_CLIENT_TIMEOUT'),
                'guzzle_middlewares' => [
                    // MyInvokableMiddleware::class,
                ],
            ],
        ],
    ],
];
