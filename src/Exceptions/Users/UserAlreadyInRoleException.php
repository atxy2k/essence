<?php


namespace Atxy2k\Essence\Exceptions\Users;

use Exception;
class UserAlreadyInRoleException extends Exception
{

    /**
     * UserAlreadyInRoleException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('User already in role'));
    }
}