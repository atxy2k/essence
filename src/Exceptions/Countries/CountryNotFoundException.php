<?php namespace Atxy2k\Essence\Exceptions\Countries;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 18:43
 */
use Exception;

class CountryNotFoundException extends Exception
{
    protected  $message = 'Ocurrió un error al localizar el elemento';
}
