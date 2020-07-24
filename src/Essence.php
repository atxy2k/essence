<?php namespace Atxy2k\Essence;

use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\Services\ConfigurationsService;
use Throwable;

class Essence
{
    /** @var ConfigurationsService */
    protected $configurationsService;

    public function __construct(ConfigurationsService $configurationsService)
    {
        $this->configurationsService = $configurationsService;
    }

    public function getOption(string $key, $default = '', $configurable = null)
    {
        return $this->configurationsService->getConfiguration($key, $configurable) ?? $default;
    }

    public function setOption(string $key, $value, $encode = false, User $user = null)
    {
        return $this->configurationsService->setConfiguration($key, $value, $encode, $user);
    }

    public function log(Throwable $e)
    {
        logger('*******************************************');
        logger(vsprintf('* Error in: %s line %s', [$e->getFile(), $e->getLine()]));
        logger('*******************************************');
        logger($e->getMessage());
    }

}
