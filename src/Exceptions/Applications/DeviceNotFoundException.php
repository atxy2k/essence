<?php namespace Atxy2k\Essence\Exceptions\Applications;
/**
 * Created by PhpStorm.
 * User: atxy2
 * Date: 5/11/2019
 * Time: 16:14
 */
use Exception;

class DeviceNotFoundException extends Exception
{

    /**
     * DeviceNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Device not found'));
    }

}