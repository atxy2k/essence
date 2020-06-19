<?php namespace Atxy2k\Essence\JsonWebTokens\Exceptions;

use Exception;

class DeviceIdentifierNotFoundException extends Exception
{

    /**
     * DeviceIdentifierNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Device identifier not found'));
    }

}
