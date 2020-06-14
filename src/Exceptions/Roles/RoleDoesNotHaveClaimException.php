<?php


namespace Atxy2k\Essence\Exceptions\Roles;

use Exception;

class RoleDoesNotHaveClaimException extends Exception
{

    /**
     * RoleHasNotClaimException constructor.
     */
    public function __construct()
    {
        parent::__construct(__("Role doesn't have claim"));
    }
}