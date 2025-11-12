<?php

return [
    'hosts' => [
        [
            'host' => env('ELASTICSEARCH_HOST', 'localhost'),
            'scheme' => env('ELASTICSEARCH_SCHEME', 'https'),
            'port' => env('ELASTICSEARCH_PORT', 9200),
            'user' => env('ELASTICSEARCH_USER', ''),
            'pass' => env('ELASTICSEARCH_PASS', ''),
        ],
    ],
];
