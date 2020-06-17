<?php
use Atxy2k\Essence\Constants\Environment;

return [
    'admin_role_slug' => env(Environment::ESSENCE_ADMIN, 'developer'),
    /**
     * Password recovery timeout
     */
    'password_recovery_timeout' => env(Environment::RECOVERY_PASSWORD_TIMEOUT, 30)
];
