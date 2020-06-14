<?php


namespace Atxy2k\Essence\Exceptions\Roles;
use Exception;

class IntegerOrStringRequiredException extends Exception
{

    /**
     * IntegerOrStringRequiredException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Integer or string are required'));
    }
}