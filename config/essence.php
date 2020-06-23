<?php

use Atxy2k\Essence\Constants\DeviceTypes;
use Atxy2k\Essence\Constants\Environment;

return [
    'admin_role_slug' => env(Environment::ESSENCE_ADMIN, 'developer'),
    /**
     * Password recovery timeout
     */
    'password_recovery_timeout' => env(Environment::RECOVERY_PASSWORD_TIMEOUT, 30),
    /**
     * Auto activate device's types.
     */
    'auto_activate' => [
        DeviceTypes::BROWSER
    ],

    'default_user' => [
        'first_name'=> 'User',
        'last_name' => 'developer',
        'email'     => 'developer@essence.com',
        'password'  => 'passwd'
    ]

];
