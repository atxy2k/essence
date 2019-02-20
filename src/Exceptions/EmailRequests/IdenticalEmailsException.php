<?php namespace Atxy2k\Essence\Exceptions\EmailRequests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 10:22
 */

use Exception;

class IdenticalEmailsException extends Exception
{
    protected $message = 'Los emails de entrada y salida son identicos';
}
