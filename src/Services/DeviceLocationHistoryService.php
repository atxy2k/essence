<?php


namespace Atxy2k\Essence\Services;
use Atxy2k\Essence\Eloquent\DeviceLocationHistory;
use Atxy2k\Essence\Exceptions\Applications\DeviceNotFoundException;
use Atxy2k\Essence\Exceptions\Applications\LocationHistoryAlreadyExistsException;
use Atxy2k\Essence\Exceptions\Essence\ValidationException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Repositories\DeviceLocationHistoryRepository;
use Atxy2k\Essence\Repositories\DevicesRepository;
use Atxy2k\Essence\Validators\DeviceLocationHistoryValidator;
use Exception;
use Auth;
use Essence;
use Illuminate\Support\Arr;
use Throwable;

class DeviceLocationHistoryService extends Service
{
    /** @var DeviceLocationHistoryRepository */
    protected $repository;
    /** @var DevicesRepository */
    protected $devicesRepository;

    public function __construct(DeviceLocationHistoryValidator $deviceAccessHistoryValidator,
                                DeviceLocationHistoryRepository $deviceLocationHistoryRepository,
                                DevicesRepository $devicesRepository)
    {
        parent::__construct();
        $this->validator = $deviceAccessHistoryValidator;
        $this->repository = $deviceLocationHistoryRepository;
        $this->devicesRepository = $devicesRepository;
    }

    public function create(array $data) : ?DeviceLocationHistory
    {
        $return = null;
        $device = $this->devicesRepository->find(Arr::get($data,'device_id'));
        try
        {
            $data['date'] = date('Y-m-d H:i:s');
            throw_if(is_null($device), DeviceNotFoundException::class);
            throw_unless( $this->validator->with($data)->passes('create'), new Exception($this->validator->errors()->first()) );
            throw_if($this->repository->exists(Arr::get($data,'device_id',-1), now()), LocationHistoryAlreadyExistsException::class);
            $return = $this->repository->create($data);
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    public function register(array $data) : ?DeviceLocationHistory
    {
        $return = null;
        $device = $this->devicesRepository->find(Arr::get($data,'device_id'));
        try
        {
            throw_if(is_null($device), DeviceNotFoundException::class);
            throw_unless( $this->validator->with($data)->passes('register'),
                new ValidationException($this->validator->errors()->first()) );
            throw_if($this->repository->exists(Arr::get($data,'device_id',-1), now()),
                LocationHistoryAlreadyExistsException::class);
            $return = $this->repository->create($data);
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    public function delete(int $device_id) : bool
    {
        $return = false;
        try
        {
            $device = $this->devicesRepository->find($device_id);
            throw_if(is_null($device), DeviceNotFoundException::class);
            $device->delete();
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }
}