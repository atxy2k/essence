<?php namespace Atxy2k\Essence\JsonWebTokens\Exceptions;

use Exception;

class ApplicationIdNotFoundException extends Exception
{
    /**
     * ApplicationIdNotFoundException constructor.
     * @param string $message
     */
    public function __construct()
    {
        parent::__construct(__("Application's id not found"));
    }

}
