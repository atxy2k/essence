<?php namespace Atxy2k\Essence\Exceptions\Suburbs;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 19:18
 */
use Exception;

class SuburbNotFoundException extends Exception
{
    protected $message = 'Ocurrió un error al localizar el elemento';
}
