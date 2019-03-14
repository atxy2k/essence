<?php namespace Atxy2k\Essence\Validators;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 17:21
 */
use Atxy2k\Essence\Infraestructure\Validator;

class CountriesValidator extends Validator
{
    protected $rules = [
        'create' => [
            'name' => 'required'
        ] ,
        'update' => [
            'name' => 'required'
        ]
    ];
}
