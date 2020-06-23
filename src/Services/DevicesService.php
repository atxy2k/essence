<?php


namespace Atxy2k\Essence\Services;


use Atxy2k\Essence\Eloquent\Device;
use Atxy2k\Essence\Eloquent\DeviceLocationHistory;
use Atxy2k\Essence\Exceptions\Applications\DeviceNotFoundException;
use Atxy2k\Essence\Exceptions\Applications\DeviceShouldBeDisableException;
use Atxy2k\Essence\Exceptions\Applications\DeviceShouldBeEnableException;
use Atxy2k\Essence\Exceptions\Essence\ValidationException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Repositories\DevicesRepository;
use Atxy2k\Essence\Validators\DevicesValidator;
use DB;
use Auth;
use Throwable;
use Exception;
use Illuminate\Support\Arr;
use Atxy2k\Essence\Repositories\UsersRepository;
use Essence;

class DevicesService extends Service
{

    protected $validator;
    protected $devicesRepository;
    /** @var DeviceAccessHistoryService */
    protected $devicesAccessHistoryService;
    /** @var DeviceLocationHistoryService */
    protected $deviceLocationHistoryService;
    /** @var UsersRepository */
    protected $usersRepository;

    public function __construct( DevicesRepository $devicesRepository,
                                 DevicesValidator $devicesValidator,
                                 DeviceAccessHistoryService $deviceAccessHistoryService,
                                 UsersRepository $usersRepository,
                                 DeviceLocationHistoryService $deviceLocationHistoryService)
    {
        parent::__construct();
        $this->devicesRepository = $devicesRepository;
        $this->validator = $devicesValidator;
        $this->devicesAccessHistoryService = $deviceAccessHistoryService;
        $this->deviceLocationHistoryService = $deviceLocationHistoryService;
        $this->usersRepository = $usersRepository;
    }

    public function create(array $data = []) : ?Device
    {
        $return = null;
        try
        {
            DB::beginTransaction();
            throw_unless($this->validator->with($data)->passes('create'),
                new ValidationException($this->validator->errors()->first()));
            $existing = $this->devicesRepository->findByIdentifier($data['identifier']);
            if( is_null($existing) )
            {
                $autoactivated_devices = config('essence.auto_activate', []);
                $data['enabled'] = in_array($data['type'], $autoactivated_devices);
                $data['last_connection'] = date('Y-m-d H:i:s');
                $data['label'] = Arr::get($data,'label', $data['name'] );
                $data['user_id'] = null;
                if (Arr::has($data, 'email'))
                {
                    $user = $this->usersRepository->findByEmail($data['email']);
                    $data['user_id'] = !is_null($user) ? $user->id : null;
                }
                $item = $this->devicesRepository->create($data);
                $return = $this->devicesRepository->find($item->identifier);
            }
            else
            {
                $existing->last_connection = date('Y-m-d H:i:s');
                $existing->save();
                $return = $existing;
            }
            DB::commit();
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            Essence::log($e);
            DB::rollback();
        }
        return $return;
    }

    public function updateLastAccess(string $device_id, array $extra = []) : bool
    {
        $return = false;
        try
        {
            $data = array_merge([ 'device_id' => $device_id ], $extra);
            throw_unless($this->devicesAccessHistoryService->create($data),
                new ValidationException($this->devicesAccessHistoryService->errors()));
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    public function delete($id)
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $element = $this->devicesRepository->find($id);
            throw_if(is_null($element), DeviceNotFoundException::class);
            $element->delete();
            $return = true;
            DB::commit();
        }
        catch (Throwable $e)
        {
            DB::rollback();
            Essence::log($e);
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    public function registerLocationHistory(array $data) : ?DeviceLocationHistory
    {
        $return = null;
        try
        {
            $registered_object = $this->deviceLocationHistoryService->create($data);
            throw_if( is_null($registered_object), new Exception($this->deviceLocationHistoryService->errors()->first()));
            $return = $registered_object;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    public function disable(array $data) : bool
    {
        $return = false;
        try
        {
            $device = $this->devicesRepository->find(Arr::get($data,'device_id'));
            throw_if(is_null($device), DeviceNotFoundException::class);
            throw_unless($this->validator->with($data)->passes('enable'), new Exception($this->validator->errors()->first()));
            throw_if( !$device->enabled, DeviceShouldBeEnableException::class );
            $device->enabled = false;
            $device->save();
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    public function enable(array $data) : bool
    {
        $return = false;
        try
        {
            $device = $this->devicesRepository->find(Arr::get($data,'device_id', -1));
            throw_if(is_null($device), DeviceNotFoundException::class);
            throw_unless($this->validator->with($data)->passes('enable'), new Exception($this->validator->errors()->first()));
            throw_if( $device->enabled, DeviceShouldBeDisableException::class );
            $device->enabled = true;
            $device->save();
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

}