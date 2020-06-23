<?php


namespace Atxy2k\Essence\Validators;


use Atxy2k\Essence\Infraestructure\Validator;

class InteractionsValidator extends Validator
{
    protected $rules = [
        'create' => [
            'interaction_id' => 'required|integer',
            'interactuable_id' => 'required|integer',
            'interactuable_type' => 'required'
        ]
    ];
}