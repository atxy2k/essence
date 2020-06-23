<?php namespace Atxy2k\Essence\Exceptions\Applications;
/**
 * Created by PhpStorm.
 * User: atxy2
 * Date: 6/11/2019
 * Time: 18:01
 */
use Exception;

class DeviceIsNotEnabledException extends Exception
{
    /**
     * DeviceIsNotEnabledException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Device is locked'));
    }
}