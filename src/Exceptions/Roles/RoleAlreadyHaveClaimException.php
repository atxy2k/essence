<?php


namespace Atxy2k\Essence\Exceptions\Roles;
use Exception;

class RoleAlreadyHaveClaimException extends Exception
{

    /**
     * RoleAlreadyHaveClaimException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Role already have claim'));
    }
}