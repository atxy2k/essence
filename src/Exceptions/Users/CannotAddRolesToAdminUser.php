<?php


namespace Atxy2k\Essence\Exceptions\Users;
use Exception;

class CannotAddRolesToAdminUser extends Exception
{

    /**
     * CannotAddRolesToAdminUser constructor.
     */
    public function __construct()
    {
        parent::__construct(__("Can't add roles to admin user"));
    }
}