<?php


namespace Atxy2k\Essence\Services;


use Atxy2k\Essence\Eloquent\DeviceAccessHistory;
use Atxy2k\Essence\Exceptions\Essence\ValidationException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Repositories\DeviceAccessHistoryRepository;
use Atxy2k\Essence\Validators\DeviceAccessHistoryValidator;
use Auth;
use Illuminate\Support\Arr;
use Throwable;
use DB;
use Essence;

class DeviceAccessHistoryService extends Service
{
    protected $validator;
    protected $deviceAccessHistoryRepository;

    public function __construct(DeviceAccessHistoryRepository $deviceAccessHistoryRepository,
                                DeviceAccessHistoryValidator $deviceLocationHistoryValidator)
    {
        parent::__construct();
        $this->deviceAccessHistoryRepository = $deviceAccessHistoryRepository;
        $this->validator = $deviceLocationHistoryValidator;
    }

    public function create(array $data = []) : ?DeviceAccessHistory
    {
        $return = null;
        try
        {
            DB::beginTransaction();
            throw_unless($this->validator->with($data)->passes('create'),
                new ValidationException($this->validator->errors()->first()));
            if( Arr::has($data,'location_id'))
            {
                $data['device_location_history_id'] = Arr::get($data, 'location_id');
                unset($data['location_id']);
            }
            $current = $this->deviceAccessHistoryRepository->lastAccess($data['device_id']);
            $data['old_access'] = !is_null($current) ? $current->created_at : null;
            $data['user_id'] = Auth::check() ? Auth::id() : null;
            $item = $this->deviceAccessHistoryRepository->create($data);
            DB::commit();
            $return = $this->deviceAccessHistoryRepository->find($item->id);
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            Essence::log($e);
            DB::rollback();
        }
        return $return;
    }
}