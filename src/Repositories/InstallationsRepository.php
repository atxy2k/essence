<?php


namespace Atxy2k\Essence\Repositories;


use Atxy2k\Essence\Eloquent\Installation;
use Atxy2k\Essence\Exceptions\Installations\AuthorizationCodeNotMatchException;
use Atxy2k\Essence\Exceptions\Installations\DeviceNotMatchException;
use Atxy2k\Essence\Exceptions\Installations\InstallationNotFoundException;
use Atxy2k\Essence\Infraestructure\Repository;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class InstallationsRepository extends Repository
{
    protected string $model = Installation::class;

    public function findByDeviceId(string $device_id) : Builder
    {
        return $this->query->orderBy('created_at', 'desc')
            ->where('device_id', $device_id);
    }

    public function isAuthorized(string $installation_id) : bool
    {
        $installation = $this->query->where('id', $installation_id);
        if (is_null($installation)) return false;
        return $installation->is_authorized;
    }

    public function match(string $installation_id, string $device_id, string $code) : bool
    {
        $response = false;
        try
        {
            $installation = $this->query->where('id', $installation_id);
            throw_if(is_null($installation), InstallationNotFoundException::class);
            throw_unless($installation->device_id === $device_id, DeviceNotMatchException::class);
            throw_unless($installation->authorization_code === $code, AuthorizationCodeNotMatchException::class );
            $response = true;
        }
        catch (Throwable $e)
        {
            logger($e->getMessage());
        }
        return $response;
    }

}