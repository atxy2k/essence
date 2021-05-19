<?php


namespace Atxy2k\Essence\Validators;


use Atxy2k\Essence\Infrastructure\Validator;

class InteractionTypeValidator extends Validator
{
    protected $rules = [
        'create' => [
            'name' => 'required|unique:interactions_type,name',
            'description' => 'required'
        ]
    ];
}