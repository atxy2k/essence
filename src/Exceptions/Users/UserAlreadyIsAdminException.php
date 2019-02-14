<?php namespace Atxy2k\Essence\Exceptions\Users;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 11:20
 */

use Exception;

class UserAlreadyIsAdminException extends Exception
{
    protected $message = 'El usuario ya es administrador';
}
