<?php namespace Atxy2k\Essence;

use Atxy2k\Essence\Services\SettingsService;
use Throwable;

class Essence
{
    /** @var SettingsService */
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function getOption(string $key, $default = '')
    {
        return $this->settingsService->getOption($key) ?? $default;
    }

    public function updateOption(string $key, $value)
    {
        return $this->settingsService->setOption($key, $value);
    }

    public function updateUserOption(string $key, $value)
    {
        return $this->settingsService->setUserOption($key, $value);
    }

    public function log(Throwable $e)
    {
        logger('*******************************************');
        logger(vsprintf('* Error in: %s line %s', [$e->getFile(), $e->getLine()]));
        logger('*******************************************');
        logger($e->getMessage());
    }

}
