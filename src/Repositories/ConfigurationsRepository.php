<?php


namespace Atxy2k\Essence\Repositories;


use Atxy2k\Essence\Eloquent\Configuration;
use Atxy2k\Essence\Infraestructure\Repository;

class ConfigurationsRepository extends Repository
{
    protected ?string $model = Configuration::class;

    public function findByKey(string $key, object $configurable = null) : ?Configuration
    {
        if(!is_null($configurable))
        {
            return $this->query
                ->where('configurable_id', $configurable->id)
                ->where('configurable_type', get_class($configurable))
                ->where('key', $key)
                ->first();
        }
        return $this->query
            ->whereNull('configurable_id')
            ->whereNull('configurable_type')
            ->where('key', $key)
            ->first();
    }

    public function countByKey(string $key, object $configurable = null) : int
    {
        if(!is_null($configurable))
        {
            return $this->query
                ->where('configurable_id', $configurable->id)
                ->where('configurable_type', get_class($configurable))
                ->where('key', $key)
                ->count();
        }
        return $this->query
            ->whereNull('configurable_id')
            ->whereNull('configurable_type')
            ->where('key', $key)
            ->count();
    }



}