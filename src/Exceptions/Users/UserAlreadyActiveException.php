<?php namespace Atxy2k\Essence\Exceptions\Users;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 10:48
 */

use Exception;

class UserAlreadyActiveException extends Exception
{
    protected $message = 'El usuario ya se encuentra activado';
}
