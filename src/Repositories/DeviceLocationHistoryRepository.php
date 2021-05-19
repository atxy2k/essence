<?php


namespace Atxy2k\Essence\Repositories;

use Atxy2k\Essence\Eloquent\DeviceLocationHistory;
use Atxy2k\Essence\Infrastructure\Repository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DeviceLocationHistoryRepository extends Repository
{

    protected ?string $model = DeviceLocationHistory::class;

    public function exists(string $device_id, Carbon $date) : bool
    {
        return $this->query
                ->where('device_id', $device_id)
                ->where('date', $date->format('Y-m-d H:i:s'))
                ->count() === 1;
    }

    public function findByDate(Carbon $date) : Collection
    {
        return $this->query
            ->whereBetween('date', [
            $date->copy()->startOfDay()->format('Y-m-d H:i:s'),
            $date->copy()->endOfDay()->format('Y-m-d H:i:s'),
        ])->orderBy('date', 'asc')->get();
    }

}