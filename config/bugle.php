<?php

return [
    'version' => '1.0.0',
    'domain' => [
        'host' => str_replace('http://', '', str_replace('https://', '', env('DOMAIN'))),
        'full' => env('DOMAIN'),
    ],
    'assetpath' => env('REMOTE_ASSET_URL', ''),
    'ntfy' => env('NTFYSH_KEY'),
];
