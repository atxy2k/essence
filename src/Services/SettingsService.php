<?php namespace Atxy2k\Essence\Services;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 18:55
 */

use Atxy2k\Essence\Exceptions\Users\UserNotFoundException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Repositories\SettingsRepository;
use Sentinel;
use Throwable;

class SettingsService extends Service
{
    /** @var SettingsRepository */
    protected $settingsRepository;

    public function __construct(SettingsRepository $settingsRepository)
    {
        parent::__construct();
        $this->settingsRepository = $settingsRepository;
    }

    public function getOption(string $key, int $user_id = null)
    {
        return $this->settingsRepository->getValue($key, $user_id);
    }

    public function setOption(string $key, $value) : bool
    {
        return is_array($value) ? $this->settingsRepository->setEncodedValue($key, $value)
            : $this->settingsRepository->setValue($key, $value);
    }

    public function setUserOption(string $key, $value) : bool
    {
        $return = false;
        try {
            $user = Sentinel::getUser();
            throw_if(is_null($user), UserNotFoundException::class);
            $return = is_array($value) ? $this->settingsRepository->setEncodedValue($key, $value, $user->id)
                : $this->settingsRepository->setValue($key, $value, $user->id);
        } catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

}
