<?php


namespace Atxy2k\Essence\Repositories;

use Atxy2k\Essence\Eloquent\DeviceAccessHistory;
use Atxy2k\Essence\Infraestructure\Repository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DeviceAccessHistoryRepository extends Repository
{
    protected ?string $model = DeviceAccessHistory::class;

    public function lastAccess(string $device_id) : ?DeviceAccessHistory
    {
        return $this->query
            ->orderBy('created_at', 'desc')
            ->where('device_id', $device_id)->first();
    }

    public function allByDevice(string $device_id) : Collection
    {
        return $this->query
            ->orderBy('created_at', 'desc')
            ->where('device_id', $device_id)->get();
    }
}