<?php namespace Atxy2k\Essence\Exceptions\Applications;
/**
 * Created by PhpStorm.
 * User: atxy2
 * Date: 6/11/2019
 * Time: 13:38
 */
use Exception;

class LocationHistoryAlreadyExistsException extends Exception
{

    /**
     * ApplicationIsNotEnabledException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Location already registered'));
    }

}