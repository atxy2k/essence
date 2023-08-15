<?php namespace Atxy2k\Essence\Throwables;

use Exception;
class ValueCannotBeEmptyException extends Exception
{
    public function __construct()
    {
        parent::__construct(__('Value cannot be empty'));
    }
}