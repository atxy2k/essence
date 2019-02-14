<?php namespace Atxy2k\Essence\Exceptions\Users;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 14/2/2019
 * Time: 11:37
 */

use Exception;

class UserDoesNotAdminException extends Exception
{
    protected $message = 'El usuario no es administrador';
}
