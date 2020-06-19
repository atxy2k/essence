<?php


namespace Atxy2k\Essence\Validators;

use Atxy2k\Essence\Infraestructure\Validator;
class DevicesValidator extends Validator
{
    protected $rules = [
        'create' => [
            'identifier' => 'required',
            'name'       => 'required',
        ],
        'update' => [
            'name'       => 'required',
            'version'    => 'required',
        ],
        'enable' => [
            'device_id'  => 'required',
        ],
        'disabled' => [
            'device_id'  => 'required',
        ],
    ];
}