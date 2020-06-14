<?php


namespace Atxy2k\Essence\Exceptions\Claims;

use Exception;

class ClaimAlreadyEnabledException extends Exception
{

    /**
     * ClaimAlreadyException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Claim already enabled exception'));
    }
}