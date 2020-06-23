<?php


namespace Atxy2k\Essence\Exceptions\Users;

use Exception;

class UserDoesNotHaveClaimException extends Exception
{

    /**
     * UserDoesNotHaveRoleException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('User does not have claim'));
    }
}