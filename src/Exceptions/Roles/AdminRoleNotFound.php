<?php namespace Atxy2k\Essence\Exceptions\Roles;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 11:05
 */

use Exception;

class AdminRoleNotFound extends Exception
{
    protected $message = 'No se localizó un rol administrativo';
}
