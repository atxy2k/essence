<?php


namespace Atxy2k\Essence\Validators;


use Atxy2k\Essence\Infrastructure\Validator;

class InstallationsValidator extends Validator
{
    protected $rules = [
        'create' =>  [
            'id' => 'required',
            'device_id' => 'required|exists:devices,id',
            'activate' => 'required'
        ]
    ];
}