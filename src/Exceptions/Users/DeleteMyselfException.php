<?php namespace Atxy2k\Essence\Exceptions\Users;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 10:57
 */

use Exception;

class DeleteMyselfException extends Exception
{
    protected $message = 'Hey!, no puedes borrate a ti mismo.';
}
