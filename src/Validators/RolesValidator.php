<?php namespace Atxy2k\Essence\Validators;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 12:52
 */
use Atxy2k\Essence\Infrastructure\Validator;

class RolesValidator extends Validator
{
    protected $rules =
    [
        'create' => [
            'name'      => 'required|string|bail'
        ],
        'update' => [
            'name'      => 'required|string|bail'
        ],
    ];
}
