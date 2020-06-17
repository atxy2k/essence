<?php


namespace Atxy2k\Essence\Exceptions\Users;

use Exception;
class TokenExpiredException extends Exception
{

    /**
     * TokenExpiredException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Token expired'));
    }
}