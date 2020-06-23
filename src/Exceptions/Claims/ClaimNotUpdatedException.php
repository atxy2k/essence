<?php


namespace Atxy2k\Essence\Exceptions\Claims;

use Exception;

class ClaimNotUpdatedException extends Exception
{

    /**
     * ClaimNotUpdatedException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Claim not updated!'));
    }
}