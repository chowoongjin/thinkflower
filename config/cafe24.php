<?php

return [
    'host' => env('CAFE24_FTP_HOST'),
    'username' => env('CAFE24_FTP_USERNAME'),
    'password' => env('CAFE24_FTP_PASSWORD'),
    'cdn_url' => rtrim(env('CAFE24_CDN_URL', ''), '/'),
    'base_dir' => trim(env('CAFE24_FTP_BASE_DIR', '/'), '/'),
    'ssl' => filter_var(env('CAFE24_FTP_SSL', false), FILTER_VALIDATE_BOOL),
];
