<?php


namespace Atxy2k\Essence\Validators;

use Atxy2k\Essence\Infrastructure\Validator;
class DeviceLocationHistoryValidator extends Validator
{
    protected $rules = [
        'create' => [
            'device_id' => 'required|exists:devices,id',
            'latitude'  => 'required',
            'longitude' => 'required'
        ],
        'register' => [
            'device_id' => 'required|exists:devices,id',
            'latitude'  => 'required',
            'longitude' => 'required',
            'date'      => 'required'
        ],
    ];
}