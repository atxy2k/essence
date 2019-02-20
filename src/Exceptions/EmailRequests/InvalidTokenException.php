<?php namespace Atxy2k\Essence\Exceptions\EmailRequests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 22:38
 */
use Exception;

class InvalidTokenException extends Exception
{
    protected $message = 'El token no es válido o ha expirado';
}
