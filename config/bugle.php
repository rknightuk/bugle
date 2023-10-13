<?php

return [
    'version' => '1.0.0',
    'domain' => [
        'host' => env('DOMAIN'),
        'full' => 'https://' . env('DOMAIN'),
    ],
    'assetpath' => env('REMOTE_ASSET_URL', ''),
    'ntfy' => env('NTFYSH_KEY'),
];
