<?php namespace Atxy2k\Essence\Exceptions\Municipalities;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 19:06
 */
use Exception;

class MunicipalityNotFoundException extends Exception
{
    protected $message = 'Ocurrió un error al localizar el elemento';
}
