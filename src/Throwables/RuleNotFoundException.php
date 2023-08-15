<?php namespace Atxy2k\Essence\Throwables;

use Exception;
class RuleNotFoundException extends Exception
{
    public function __construct(){
        parent::__construct(__('Rule not found'));
    }
}