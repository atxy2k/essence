<?php namespace Atxy2k\Essence\Exceptions\Users;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 23:08
 */

use Exception;

class UserNotFoundException extends Exception
{
    protected $message = 'No se encontró ningún usuario relacionado con los datos proporcionados';
}
