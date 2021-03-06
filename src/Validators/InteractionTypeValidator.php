<?php


namespace Atxy2k\Essence\Validators;


use Atxy2k\Essence\Infraestructure\Validator;

class InteractionTypeValidator extends Validator
{
    protected $rules = [
        'create' => [
            'name' => 'required|unique:interactions_type,name',
            'description' => 'required'
        ]
    ];
}