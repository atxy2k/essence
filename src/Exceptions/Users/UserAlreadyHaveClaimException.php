<?php


namespace Atxy2k\Essence\Exceptions\Users;

use Exception;
class UserAlreadyHaveClaimException extends Exception
{

    /**
     * UserAlreadyHaveClaimException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('User already have claim'));
    }
}