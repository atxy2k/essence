<?php namespace Atxy2k\Essence\Exceptions\EmailRequests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 22:40
 */

use Exception;

class RequestExpiredException extends Exception
{
    protected $message = 'Request has ben expired';
}
