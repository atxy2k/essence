<?php namespace Atxy2k\Essence\Validators;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 17:23
 */
use Atxy2k\Essence\Infraestructure\Validator;

class MunicipalitiesValidator extends Validator
{
    protected $rules = [
        'create' => [
            'name'      => 'required',
            'state_id'  => 'required|exists:states,id'
        ],
        'update' => [
            'name'      => 'required',
            'state_id'  => 'required|exists:states,id'
        ],
    ];
}
