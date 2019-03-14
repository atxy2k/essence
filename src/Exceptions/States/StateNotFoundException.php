<?php namespace Atxy2k\Essence\Exceptions\States;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 18:30
 */
use Exception;

class StateNotFoundException extends Exception
{
    protected $message = 'Ocurrió un error al localizar el elemento';
}
