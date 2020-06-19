<?php namespace Atxy2k\Essence\JsonWebTokens\Exceptions;

use Exception;

class AudienceNotMatchException extends Exception
{

    /**
     * AudienceNotMatchException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Audience not match'));
    }

}
