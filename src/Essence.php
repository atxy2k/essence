<?php namespace Atxy2k\Essence;

use Atxy2k\Essence\Services\SettingsService;

class Essence
{
    /** @var SettingsService */
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function getOption(string $key)
    {
        return $this->settingsService->getOption($key);
    }

    public function updateOption(string $key, $value)
    {
        return $this->settingsService->setOption($key, $value);
    }

    public function updateUserOption(string $key, $value)
    {
        return $this->settingsService->setUserOption($key, $value);
    }

}
