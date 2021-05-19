<?php


namespace Atxy2k\Essence\Validators;

use Atxy2k\Essence\Constants\Browsers;
use Atxy2k\Essence\Constants\Desktop;
use Atxy2k\Essence\Constants\DeviceTypes;
use Atxy2k\Essence\Constants\Phone;
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
                'id'         => 'required',
                'installation_id' => 'required',
                'name'       => 'required',
                'type'       => [
                    'required',
                    Rule::in([
                        DeviceTypes::MOBILE,
                        DeviceTypes::BROWSER,
                        DeviceTypes::DESKTOP_APPLICATION,
                    ])
                ],
                'subtype' => [
                    'required',
                    Rule::in([
                        Browsers::CHROME,
                        Browsers::EDGE,
                        Browsers::FIREFOX,
                        Browsers::INTERNET_EXPLORER,
                        Browsers::OPERA,
                        Browsers::SAFARI,
                        Browsers::UNKNOWN,
                        Phone::ANDROID,
                        Phone::IOS,
                        Desktop::Native,
                        Desktop::WINDOWS_UNIVERSAL_APPLICATION
                    ])
                ]
            ],
            'update' => [
                'label'       => 'required',
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