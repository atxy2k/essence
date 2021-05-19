<?php


namespace Atxy2k\Essence\Repositories;


use Atxy2k\Essence\Eloquent\Installation;
use Atxy2k\Essence\Exceptions\Installations\InstallationAlreadyAuthorized;
use Atxy2k\Essence\Exceptions\Installations\InstallationNotFoundException;
use Atxy2k\Essence\Infrastructure\Repository;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class InstallationsRepository extends Repository
{
    protected ?string $model = Installation::class;

    public function isAuthorized(string $id) : bool
    {
        $return = false;
        try
        {
            $installation = $this->find($id);
            throw_if(is_null($installation), InstallationNotFoundException::class);
            $return = $installation->is_authorized;
        }
        catch (Throwable $e)
        {
            logger($e->getMessage());
        }
        return $return;
    }

    public function authorize(string $id) : bool
    {
        $return = false;
        try
        {
            $installation = $this->find($id);
            throw_if(is_null($installation), InstallationNotFoundException::class);
            throw_if($installation->is_authorized, InstallationAlreadyAuthorized::class);
            $installation->authorized_at = date('Y-m-d H:i:s');
            $installation->save();
            $return = true;
        }
        catch (Throwable $e)
        {
            logger($e->getMessage());
        }
        return $return;
    }

    public function findByDeviceId(string $device_id) : Builder
    {
        return $this->query->where('device_id', $device_id);
    }

}