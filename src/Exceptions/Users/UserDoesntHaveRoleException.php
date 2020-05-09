<?php


namespace Atxy2k\Essence\Exceptions\Users;

use Exception;
class UserDoesntHaveRoleException extends Exception
{

    /**
     * UserAlreadyInRoleException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('User does not have role'));
    }
}