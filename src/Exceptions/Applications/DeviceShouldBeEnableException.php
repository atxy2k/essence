<?php namespace Atxy2k\Essence\Exceptions\Applications;
/**
 * Created by PhpStorm.
 * User: atxy2
 * Date: 6/11/2019
 * Time: 13:19
 */
use Exception;

class DeviceShouldBeEnableException extends Exception
{

    /**
     * DeviceShouldBeEnableException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Device should be enabled to continue'));
    }

}