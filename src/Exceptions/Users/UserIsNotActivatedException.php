<?php


namespace Atxy2k\Essence\Exceptions\Users;
use Exception;

class UserIsNotActivatedException extends Exception
{

    /**
     * UserIsNotActivatedException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('User is not activated'));
    }
}