<?php


namespace Atxy2k\Essence\Exceptions\Users;

use Exception;

class CannotRemoveAdminRoleToUserAdmin extends Exception
{

    /**
     * CannotRemoveAdminRoleToUserAdmin constructor.
     */
    public function __construct()
    {
        parent::__construct(__("Can't remove admin role from user admin"));
    }
}