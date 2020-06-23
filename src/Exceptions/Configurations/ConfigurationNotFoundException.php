<?php


namespace Atxy2k\Essence\Exceptions\Configurations;
use Exception;

class ConfigurationNotFoundException extends Exception
{

    /**
     * ConfigurationNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct(__('Configuration not found'));
    }
}