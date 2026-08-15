<?php

return [
    'name' => getenv('APP_NAME') ?: 'Golden Tap POS',
    'environment' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOL),
    'timezone' => getenv('APP_TIMEZONE') ?: 'Africa/Lusaka',
    'session_name' => getenv('SESSION_NAME') ?: 'golden_tap_session',
    'business' => [
        'name' => getenv('BUSINESS_NAME') ?: 'Golden Tap Pub',
        'address' => getenv('BUSINESS_ADDRESS') ?: '65QH + J7V, Kafue Road, Kafue',
        'phone' => getenv('BUSINESS_PHONE') ?: '+260 777 611 830',
        'tax_number' => getenv('BUSINESS_TAX_NUMBER') ?: '',
        'currency' => getenv('CURRENCY_SYMBOL') ?: 'K',
        'tax_label' => getenv('TAX_LABEL') ?: 'VAT',
        'default_tax_rate' => (float) (getenv('DEFAULT_VAT_RATE') !== false ? getenv('DEFAULT_VAT_RATE') : 16),
    ],
    'database' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_DATABASE') ?: 'posystem',
        'user' => getenv('DB_USERNAME') ?: 'pos_app',
        'password' => getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '',
    ],
    'uploads' => [
        'max_bytes' => 5 * 1024 * 1024,
        'allowed_image_mimes' => [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ],
    ],
];
