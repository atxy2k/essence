<?php namespace Atxy2k\Essence\Exceptions\States;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 18:33
 */
use Exception;

class NameNotAvailableException extends Exception
{
    protected  $message = 'El nombre no está disponible';
}
