<?php namespace Atxy2k\Essence\Exceptions\Users;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 10:09
 */
use Exception;

class DoesntHaveRolesException extends Exception
{
    protected $message = 'El usuario no cuenta con ningún rol asignado';
}
