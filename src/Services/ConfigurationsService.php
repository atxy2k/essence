<?php


namespace Atxy2k\Essence\Services;


use Atxy2k\Essence\Eloquent\Configuration;
use Atxy2k\Essence\Exceptions\Configurations\ConfigurationNotFoundException;
use Atxy2k\Essence\Infrastructure\Service;
use Atxy2k\Essence\Interfaces\Services\ConfigurationsServiceInterface;
use Atxy2k\Essence\Repositories\ConfigurationsRepository;
use DB;
use Throwable;
use Exception;
use Essence;

class ConfigurationsService extends Service implements ConfigurationsServiceInterface
{
    /** @var ConfigurationsRepository */
    protected $configurationsRepository;

    public function __construct(ConfigurationsRepository $configurationsRepository)
    {
        parent::__construct();
        $this->configurationsRepository = $configurationsRepository;
    }


    function getConfiguration(string $key, $configurable = null): ?Configuration
    {
        $return = null;
        try
        {
            $return = $this->configurationsRepository->findByKey($key, $configurable);
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            Essence::log($e);
        }
        return $return;
    }


    function setConfiguration(string $key, $value, $encode = false, $configurable = null): ?Configuration
    {
        $return = null;
        try
        {
            DB::beginTransaction();
            $configuration = $this->getConfiguration($key, $configurable);
            if(is_null($configuration))
            {
               $data = [
                   'key'    => $key,
                   'value'  => $encode ? json_encode($value) : $value,
                   'encode' => $encode,
                   'configurable_id' => $configurable != null ? $configurable->id : null,
                   'configurable_type' => $configurable != null ? get_class($configurable) : null
               ];
               $configuration = $this->configurationsRepository->create($data);
            }
            else
            {
                $configuration->value = $configuration->encode ? json_encode($value) : $value;
                $configuration->save();
            }
            throw_if(is_null($configuration), new Exception(__('Something happend saving configuration')));
            $return = $configuration;
            DB::commit();
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
            Essence::log($e);
        }
        return $return;
    }

    function removeConfiguration(string $key, $configurable = null): bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $configuration = $this->getConfiguration($key, $configurable);
            throw_if(is_null($configuration), ConfigurationNotFoundException::class);
            $configuration->delete();
            DB::commit();
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
            Essence::log($e);
        }
        return $return;
    }
}