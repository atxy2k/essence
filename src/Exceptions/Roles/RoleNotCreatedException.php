<?php namespace Atxy2k\Essence\Exceptions\Roles;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 13:46
 */

use Exception;

class RoleNotCreatedException extends Exception
{
    protected $message = 'Ocurrió un error al registrar el elemento';
}
