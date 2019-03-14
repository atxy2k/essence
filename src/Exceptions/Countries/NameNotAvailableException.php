<?php namespace Atxy2k\Essence\Exceptions\Countries;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 18:40
 */
use Exception;

class NameNotAvailableException extends Exception
{
    protected  $message = 'El nombre no está disponible';
}
