<?php


namespace Atxy2k\Essence\Exceptions\Installations;
use Exception;

class AuthorizationCodeNotMatchException extends Exception
{
    public $message = 'Authorization code not match';
}