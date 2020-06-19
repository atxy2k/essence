<?php


namespace Atxy2k\Essence\Repositories;


use Atxy2k\Essence\Eloquent\Application;
use Atxy2k\Essence\Infraestructure\Repository;
use Atxy2k\Essence\Interfaces\Repositories\ApplicationRepositoryInterface;

class ApplicationsRepository extends Repository implements ApplicationRepositoryInterface
{
    protected $model = Application::class;

    public function findByAppId(string $appId): ?Application
    {
        return $this->query->where('app_id', $appId)->first();
    }

    public function findByAppIdAndAppSecret(string $appId, string $appSecret): ?Application
    {
        return $this->query->where('app_id', $appId)
            ->where('app_secret', $appSecret)->first();
    }

    public function isEnabledByAppId(string $appId): bool
    {
        $existent = $this->query->where('app_id', $appId)->first();
        return $existent !== null && $existent->enabled;
    }

    public function isEnabled(int $appId): bool
    {
        $existent = $this->query->where('id', $appId)->first();
        return $existent !== null && $existent->enabled;
    }

    public function enable(int $appId) : bool
    {
        $return = false;
        $existing = $this->query->where('id', $appId)->first();
        if(!is_null($existing))
        {
            if(!$existing->enabled)
            {
                $existing->enabled = true;
                $existing->save();
                $return = true;
            }
        }
        return $return;
    }

    public function enableFromApplicationId(string $application_id): bool
    {
        $return = false;
        $existing = $this->query->where('app_id', $application_id)->first();
        if(!is_null($existing))
        {
            if(!$existing->enabled)
            {
                $existing->enabled = true;
                $existing->save();
                $return = true;
            }
        }
        return $return;
    }

    public function disable(int $appId): bool
    {
        $return = false;
        $existing = $this->query->where('id', $appId)->first();
        if(!is_null($existing))
        {
            if($existing->enabled)
            {
                $existing->enabled = false;
                $existing->save();
                $return = true;
            }
        }
        return $return;
    }

    public function disableFromApplicationId(string $application_id): bool
    {
        $return = false;
        $existing = $this->query->where('app_id', $application_id)->first();
        if(!is_null($existing))
        {
            if($existing->enabled)
            {
                $existing->enabled = false;
                $existing->enabled = false;
                $existing->save();
                $return = true;
            }
        }
        return $return;
    }

}