<?php namespace Atxy2k\Essence\Exceptions\Users;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 14/2/2019
 * Time: 11:18
 */

use Exception;

class UserNotUpdatedException extends Exception
{
    protected $message = 'Ocurrió un error al actualizar el usuario';
}
