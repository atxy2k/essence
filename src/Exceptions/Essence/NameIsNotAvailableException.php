<?php


namespace Atxy2k\Essence\Exceptions\Essence;

use Exception;
class NameIsNotAvailableException extends Exception
{


    /**
     * NameIsNotAvailableException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Name is not available'));
    }
}