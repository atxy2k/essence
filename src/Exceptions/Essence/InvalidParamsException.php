<?php namespace Atxy2k\Essence\Exceptions\Essence;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 10:38
 */

use Exception;

class InvalidParamsException extends Exception
{
    protected $message = 'Parámetros incorrectos';
}
