<?php namespace Atxy2k\Essence\Exceptions\Applications;

use Exception;

class ApplicationNotFoundException extends Exception
{

    /**
     * ApplicationNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Application not found'));
    }

}
