<?php


namespace Atxy2k\Essence\Validators;

use Atxy2k\Essence\Infrastructure\Validator;

class DeviceAccessHistoryValidator extends Validator
{
    protected $rules = [
        'create' => [
            'device_id' => 'required|exists:devices,id'
        ]
    ];
}