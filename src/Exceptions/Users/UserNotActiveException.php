<?php namespace Atxy2k\Essence\Exceptions\Users;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 12:48
 */

use Exception;

class UserNotActiveException extends Exception
{
    protected $message = 'El usuario no se encuentra activo';
}
