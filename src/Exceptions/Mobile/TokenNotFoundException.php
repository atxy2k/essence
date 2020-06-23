<?php


namespace Atxy2k\Essence\Exceptions\Mobile;

use Exception;
class TokenNotFoundException extends Exception
{

    /**
     * TokenNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Token not found'));
    }
}