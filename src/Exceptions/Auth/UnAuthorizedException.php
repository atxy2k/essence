<?php


namespace Atxy2k\Essence\Exceptions\Auth;

use Exception;
class UnAuthorizedException extends Exception
{

    /**
     * UnAuthorizedException constructor.
     */
    public function __construct()
    {
        parent::__construct(__("You can't access to this site"));
    }
}