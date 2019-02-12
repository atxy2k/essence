<?php namespace Atxy2k\Essence\Exceptions\EmailRequests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 23:02
 */

use Exception;

class EmailNotAvailableException extends Exception
{
    protected $message = 'Email not available';
}
