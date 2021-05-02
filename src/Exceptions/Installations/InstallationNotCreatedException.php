<?php


namespace Atxy2k\Essence\Exceptions\Installations;

use Exception;

class InstallationNotCreatedException extends Exception
{
    public $message = 'Something was wrong creating installation object';
}