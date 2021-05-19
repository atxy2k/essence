<?php


namespace Atxy2k\Essence\Validators;


use Atxy2k\Essence\Infrastructure\Validator;

class ApplicationsValidator extends Validator
{
    protected $rules = [
        'create' => [
            'name' => 'required'
        ],
        'update' => [
            'name' => 'required'
        ],
        'enable' => [
            'application_id' => 'required|exists:applications,id'
        ],
        'disable' => [
            'application_id' => 'required|exists:applications,id'
        ],
    ];
}