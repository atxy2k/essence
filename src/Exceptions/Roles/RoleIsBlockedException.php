<?php


namespace Atxy2k\Essence\Exceptions\Roles;

use Exception;

class RoleIsBlockedException extends Exception
{

    /**
     * RoleIsBlockedException constructor.
     */
    public function __construct()
    {
        parent::__construct('Role is blocked');
    }
}