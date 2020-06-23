<?php


namespace Atxy2k\Essence\Exceptions\Claims;

use Exception;

class ClaimNotFoundException extends Exception
{

    /**
     * ClaimNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Claim not found'));
    }
}