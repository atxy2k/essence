<?php
use Atxy2k\Essence\Constants\Environment;

return [
    'admin_role_slug' => env(Environment::ESSENCE_ADMIN, 'developer'),
    'pages' => [
        'login' => env(Environment::LOGIN_PAGE, 'auth'),
        'main'  => env(Environment::DASHBOARD_PAGE, '/'),
    ]
];
