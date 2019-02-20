<?php namespace Atxy2k\Essence\Exceptions\EmailRequests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 22:42
 */

use Exception;

class RequestNotFoundException extends Exception
{
    protected $message = 'Request not found';
}
