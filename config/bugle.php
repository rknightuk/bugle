<?php

return [
    'domain' => [
        'host' => env('DOMAIN'),
        'full' => 'https://' . env('DOMAIN'),
    ],
    'assetpath' => env('REMOTE_ASSET_URL', ''),
];
