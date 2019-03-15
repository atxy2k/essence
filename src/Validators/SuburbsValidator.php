<?php namespace Atxy2k\Essence\Validators;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 17:26
 */
use Atxy2k\Essence\Infraestructure\Validator;

class SuburbsValidator extends Validator
{
    protected $rules = [
        'create' => [
            'name' => 'required',
            'postal_code' => 'required',
            'type' => 'required',
            'municipality_id' => 'required|exists:municipalities,id',
        ],
        'update' => [
            'name' => 'required',
            'postal_code' => 'required',
            'type' => 'required',
            'municipality_id' => 'required|exists:municipalities,id',
        ],
    ];
}
