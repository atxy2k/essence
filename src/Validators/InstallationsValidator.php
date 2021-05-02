<?php


namespace Atxy2k\Essence\Validators;


use Atxy2k\Essence\Infraestructure\Validator;

class InstallationsValidator extends Validator
{
    protected array $rules = [
        'create' => [
            'device_id' => 'required|exists:devices,id',
            'id' => 'required'
        ],
        'match' => [
            'installation_id' => 'required|exists:installations,id',
            'device_id'       => 'required|exists:devices,id',
            'authorization_code' => 'required'
        ]
    ];
}