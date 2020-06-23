<?php


namespace Atxy2k\Essence\Validators;

use Atxy2k\Essence\Constants\DeviceTypes;
use Atxy2k\Essence\Infraestructure\Validator;
use Illuminate\Validation\Rule;

class DevicesValidator extends Validator
{
    /**
     * DevicesValidator constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->rules = [
            'create' => [
                'identifier' => 'required',
                'name'       => 'required',
                'type'       => [
                    'required',
                    Rule::in([
                        DeviceTypes::IOS,
                        DeviceTypes::ANDROID,
                        DeviceTypes::WINDOWS_PHONE,
                        DeviceTypes::MOBILE,
                        DeviceTypes::BROWSER,
                        DeviceTypes::DESKTOP_APPLICATION,
                        DeviceTypes::WINDOWS_UNIVERSAL_APPLICATION,
                    ])
                ],
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

}