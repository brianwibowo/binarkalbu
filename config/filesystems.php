<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    */

    'disks' => [

        // KEMBALIKAN KE DEFAULT
        // Disk 'local' adalah root untuk penyimpanan internal non-publik.
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'), // <-- Path default yang benar
            'throw' => false,
        ],

        // --- PERBAIKAN UTAMA DI SINI ---
        // Disk 'public' sekarang akan menyimpan file LANGSUNG ke folder
        // public_html/storage, tanpa butuh symbolic link.
        'public' => [
            'driver' => 'local',
            'root' => public_path('storage'), // <-- Diubah dari storage_path('app/public')
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        // Disk 'private' ini sudah benar untuk file yang tidak boleh diakses web.
        'private' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    | Konfigurasi ini sekarang tidak lagi digunakan, tapi tidak masalah jika tetap ada.
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];