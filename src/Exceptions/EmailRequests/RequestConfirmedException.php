<?php namespace Atxy2k\Essence\Exceptions\EmailRequests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 22:35
 */
use Exception;

class RequestConfirmedException extends Exception
{
    protected $message = 'Request already confirmed';
}
