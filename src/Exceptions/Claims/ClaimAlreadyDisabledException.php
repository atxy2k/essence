<?php namespace Atxy2k\Essence\Exceptions\Claims;

use Exception;
class ClaimAlreadyDisabledException extends Exception
{

    /**
     * ClaimAlreadyDisabledException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Claim already disabled exception'));
    }
}