<?php


namespace Atxy2k\Essence\Exceptions\Installations;
use Exception;

class InstallationNotCreatedException extends Exception
{
    protected $message = 'Installation not created';
}