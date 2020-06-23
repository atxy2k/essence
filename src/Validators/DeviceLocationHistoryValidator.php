<?php


namespace Atxy2k\Essence\Validators;

use Atxy2k\Essence\Infraestructure\Validator;
class DeviceLocationHistoryValidator extends Validator
{
    protected $rules = [
        'create' => [
            'device_id' => 'required|exists:devices,identifier',
            'latitude'  => 'required',
            'longitude' => 'required'
        ],
        'register' => [
            'device_id' => 'required|exists:devices,identifier',
            'latitude'  => 'required',
            'longitude' => 'required',
            'date'      => 'required'
        ],
    ];
}