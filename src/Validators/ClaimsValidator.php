<?php


namespace Atxy2k\Essence\Validators;


use Atxy2k\Essence\Infraestructure\Validator;

class ClaimsValidator extends Validator
{
    protected $rules = [
        'create' => [
            'identifier' => 'required|unique:claims,identifier|bail',
            'name'       => 'required'
        ],
        'update' => [
            'name' => 'required',
            'identifier' => 'required'
        ]
    ];
}