<?php


namespace Atxy2k\Essence\Exceptions\Users;

use Exception;
class AdminRoleCannotBeAddedFromAddRoleFunction extends Exception
{

    /**
     * AdminRoleCannotBeAddedFromAddRoleFunction constructor.
     */
    public function __construct()
    {
        parent::__construct(__("Admin role can't be added from another function, use granAdminPrivileges instead"));
    }
}