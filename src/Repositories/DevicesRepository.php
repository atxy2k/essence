<?php


namespace Atxy2k\Essence\Repositories;

use Atxy2k\Essence\Eloquent\Device;
use Atxy2k\Essence\Infraestructure\Repository;
use Exception;
use Illuminate\Support\Collection;
use Throwable;

class DevicesRepository extends Repository
{
    protected $model = Device::class;

    public function findByIdentifier(string $identifier) : ?Device
    {
        return $this->query
            ->where('identifier', $identifier )->first();
    }

    public function updateLastConnection(int $id) : bool
    {
        $return = false;
        try
        {
            $element =  $this->query->where('id', $id)->first();
            throw_if(is_null($element), new Exception(__('Device not found!')));
            $element->last_connection = date('Y-m-d H:i:s');
            $element->save();
            $return = true;
        }
        catch (Throwable $e)
        {
            logger($e->getMessage());
        }
        return $return;
    }

    public function getByIdentifiers(array $identifiers) : Collection
    {
        return $this->query->whereIn('identifier', $identifiers )->get();
    }
}