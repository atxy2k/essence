<?php


namespace Atxy2k\Essence\Exceptions\Installations;
use Exception;

class InstallationAlreadyAuthorized extends Exception
{
    protected $message = 'Installation already authorized';
}