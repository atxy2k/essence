<?php namespace Atxy2k\Essence\Exceptions\Users;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/2/2019
 * Time: 14:41
 */

use Exception;

class IncorrectPasswordException extends Exception
{
    protected $message = 'Contraseña incorrecta';
}
