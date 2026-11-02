<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Bridge API Kampus
    |--------------------------------------------------------------------------
    */

    'bridge' => [
        'base_url' => env(
            'BRIDGE_API_BASE_URL',
            'https://bridge.uinssc.ac.id'
        ),

        'timeout' => (int) env('BRIDGE_API_TIMEOUT', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Akun Contoh HAKIKU
    |--------------------------------------------------------------------------
    |
    | Digunakan untuk seeder di lingkungan lokal dan testing.
    | Jangan menyimpan password asli Portal Akademik di sini.
    */

    'hakiku_seed' => [
        'admin_password' => env('HAKIKU_SEED_ADMIN_PASSWORD'),
        'mahasiswa_password' => env('HAKIKU_SEED_MAHASISWA_PASSWORD'),
        'dosen_password' => env('HAKIKU_SEED_DOSEN_PASSWORD'),
    ],

];
