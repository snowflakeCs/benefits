<?php

use Wotz\SwaggerUi\Http\Middleware\EnsureUserIsAuthorized;

return [
    'files' => [
        [
            'path' => 'swagger',
            'title' => env('APP_NAME') . ' - Swagger',
            'versions' => [
                'v1' => resource_path('swagger/openapi.json'),
            ],
            'default' => 'v1',
            'middleware' => [
                'web',
                EnsureUserIsAuthorized::class,
            ],
            'validator_url' => env('SWAGGER_UI_VALIDATOR_URL'),
            'modify_file' => true,
            'server_url' => env('APP_URL'),
            'oauth' => [
                'token_path' => 'oauth/token',
                'refresh_path' => 'oauth/token',
                'authorization_path' => 'oauth/authorize',

                'client_id' => env('SWAGGER_UI_OAUTH_CLIENT_ID'),
                'client_secret' => env('SWAGGER_UI_OAUTH_CLIENT_SECRET'),
            ],
            'stylesheet' => null,
        ],
    ],
];
