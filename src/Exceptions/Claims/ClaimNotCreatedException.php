<?php


namespace Atxy2k\Essence\Exceptions\Claims;

use Exception;

class ClaimNotCreatedException extends Exception
{

    /**
     * ClaimNotCreatedException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Claim not created'));
    }
}