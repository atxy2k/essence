<?php namespace Atxy2k\Essence\JsonWebTokens\Exceptions;

use Exception;

class IdentifierNotMatchException extends Exception
{

    /**
     * IdentifierNotMatchException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Identifier not match'));
    }

}
