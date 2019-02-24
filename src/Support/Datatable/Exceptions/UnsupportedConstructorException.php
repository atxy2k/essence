<?php namespace Atxy2k\Essence\Support\Datatable\Exceptions;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 23/2/2019
 * Time: 10:40
 */
use Exception;
class UnsupportedConstructorException extends Exception
{
    protected $message = 'Constructor params is not supported';
}
