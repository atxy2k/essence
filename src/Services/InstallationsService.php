<?php


namespace Atxy2k\Essence\Services;


use Atxy2k\Essence\Eloquent\Installation;
use Atxy2k\Essence\Exceptions\Applications\DeviceIsNotEnabledException;
use Atxy2k\Essence\Exceptions\Installations\InstallationNotCreatedException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Repositories\DevicesRepository;
use Atxy2k\Essence\Repositories\InstallationsRepository;
use Atxy2k\Essence\Validators\InstallationsValidator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Throwable;

class InstallationsService extends Service
{
    protected InstallationsRepository $installationsRepository;
    protected DevicesRepository $devicesRepository;

    public function __construct(InstallationsRepository $installationsRepository,
                                DevicesRepository $devicesRepository,
                                InstallationsValidator $installationsValidator)
    {
        $this->validator = $installationsValidator;
        $this->installationsRepository = $installationsRepository;
        $this->devicesRepository = $devicesRepository;
    }

    public function create(array $data) : ?Installation
    {
        $return = null;
        try
        {
            $device = $this->devicesRepository->find($data['device_id']);
            throw_unless($device->enabled, DeviceIsNotEnabledException::class);
            /** @var Installation $existent */
            $existent = $this->installationsRepository->find($data['id']);

            $auto_activated_devices = config('essence.auto_activate', []);
            $authorize = in_array($data['type'], $auto_activated_devices);
            if(is_null($existent))
            {
                $data = [
                    'id' => Arr::get($data, 'id'),
                    'authorization_code' => strtoupper(Str::random(5)),
                    'device_id' => Arr::get($data, 'device_id'),
                    'authorized_at' => $authorize ? now() : null
                ];
                $existent = $this->create($data);
                throw_if(is_null($existent), InstallationNotCreatedException::class);
            }
            $return = $existent;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

}