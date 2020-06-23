<?php namespace Atxy2k\Essence\Exceptions\Roles;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 13:05
 */

use Exception;

class RoleNotFoundException extends Exception
{
    protected $message = 'Role not found';
}
