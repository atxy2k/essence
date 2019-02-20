<?php namespace Atxy2k\Essence\Exceptions\Users;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/2/2019
 * Time: 12:15
 */

use Exception;

class UserNotCreatedException extends Exception
{
    protected $message = 'Ocurrió un error al registrar al usuario';
}
