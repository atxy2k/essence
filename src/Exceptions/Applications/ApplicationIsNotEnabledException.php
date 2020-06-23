<?php namespace Atxy2k\Essence\Exceptions\Applications;

use Exception;

class ApplicationIsNotEnabledException extends Exception
{
    /**
     * ApplicationIsNotEnabledException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Application is not enabled'));
    }

}
