<?php

return [
    // Default account to use for connections
    'default' => env('IMAP_ACCOUNT', 'imap'),

    // List of IMAP accounts
    'accounts' => [
        'imap' => [
            'host'       => env('IMAP_HOST', 'imap.gmail.com'),
            'port'       => env('IMAP_PORT', 993),
            'encryption' => env('IMAP_ENCRYPTION', 'ssl'),
            'username'   => env('IMAP_USERNAME'),
            'password'   => env('IMAP_PASSWORD'),
            'protocol'   => 'imap',
            'validate_cert' => true,
        ],
    ],
];
