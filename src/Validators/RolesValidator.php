<?php namespace Atxy2k\Essence\Validators;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 12:52
 */
use Atxy2k\Essence\Infraestructure\Validator;

class RolesValidator extends Validator
{
    protected $rules =
    [
        'create' => [
            'name'      => 'required|string|bail',
            'routes'    => 'nullable|array|bail'
        ],
        'update' => [
            'name'      => 'required|string|bail',
            'routes'    => 'nullable|array|bail'
        ],
    ];
}
