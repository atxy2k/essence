<?php namespace Atxy2k\Essence\Exceptions\Applications;
/**
 * Created by PhpStorm.
 * User: atxy2
 * Date: 6/11/2019
 * Time: 13:20
 */
use Exception;

class DeviceShouldBeDisableException extends Exception
{

    /**
     * DeviceShouldBeDisableException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Device should be disabled to continue'));
    }

}