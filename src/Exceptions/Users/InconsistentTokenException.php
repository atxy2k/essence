<?php


namespace Atxy2k\Essence\Exceptions\Users;

use Exception;
class InconsistentTokenException extends Exception
{

    /**
     * InconsistentTokenException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Inconsistent token exception'));
    }
}