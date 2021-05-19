<?php


namespace Atxy2k\Essence\Exceptions\Installations;
use Exception;

class InstallationNotFoundException extends Exception
{
    protected $message = 'Installation not found';
}