<?php


namespace Atxy2k\Essence\Mobile;


use Atxy2k\Essence\Eloquent\Application;
use Atxy2k\Essence\Eloquent\Device;
use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\Exceptions\Applications\ApplicationIsNotEnabledException;
use Atxy2k\Essence\Exceptions\Applications\ApplicationNotFoundException;
use Atxy2k\Essence\Exceptions\Applications\DeviceIsNotEnabledException;
use Atxy2k\Essence\Exceptions\Applications\DeviceNotFoundException;
use Atxy2k\Essence\Exceptions\Users\UserIsNotActivatedException;
use Atxy2k\Essence\Exceptions\Users\UserNotFoundException;
use Atxy2k\Essence\Interfaces\Facades\MobileInterface;
use Atxy2k\Essence\JsonWebTokens\Exceptions\ApplicationIdNotFoundException;
use Atxy2k\Essence\JsonWebTokens\Exceptions\AudienceNotMatchException;
use Atxy2k\Essence\JsonWebTokens\Exceptions\DeviceIdentifierNotFoundException;
use Atxy2k\Essence\JsonWebTokens\Exceptions\IdentifierNotMatchException;
use Atxy2k\Essence\JsonWebTokens\JwtToken;
use Atxy2k\Essence\Repositories\ApplicationsRepository;
use Atxy2k\Essence\Repositories\DevicesRepository;
use Atxy2k\Essence\Repositories\UsersRepository;
use Atxy2k\Essence\Services\DevicesService;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Arr;

class Mobile implements MobileInterface
{
    /** @var Application|null */
    protected $application = null;
    /** @var Device|null  */
    protected $device = null;
    /** @var User|null  */
    protected $user = null;
    /** @var array  */
    protected $errors = [];
    /** @var null|JwtToken */
    protected $token = null;
    /** @var ApplicationsRepository */
    protected $applicationsRepository;
    /** @var DevicesRepository */
    protected $devicesRepository;
    /** @var UsersRepository */
    protected $usersRepository;
    /** @var DevicesService */
    protected $devicesService;

    /** @var null|string  */
    protected $IDENTIFIER   = null;
    /** @var null|string  */
    protected $AUDIENCE     = null;
    /** @var null|Carbon  */
    protected $EXPIRATION   = null;

    public function __construct(ApplicationsRepository $applicationsRepository, DevicesService $devicesService,
                                DevicesRepository $devicesRepository, UsersRepository $usersRepository)
    {
        $this->applicationsRepository = $applicationsRepository;
        $this->devicesRepository = $devicesRepository;
        $this->usersRepository = $usersRepository;
        $this->devicesService = $devicesService;
        $this->IDENTIFIER   = config('app.url');
        $this->AUDIENCE     = config('app.url');
    }

    function setApplication(Application $application): MobileInterface
    {
        $this->application = $application;
        return $this;
    }

    function getApplication(): Application
    {
        return $this->application;
    }

    function setDevice(Device $device): MobileInterface
    {
        $this->device = $device;
        return $this;
    }

    function getDevice(): Device
    {
        return $this->device;
    }

    function setUser(User $user = null): MobileInterface
    {
        if(is_null($user))
        {
            $this->user = null;
            return $this;
        }
        $this->user = $user;
        Auth::login($user);
        return $this;
    }

    function getUser(): User
    {
        return $this->user;
    }

    function isAuthenticated(): bool
    {
        return Auth::check();
    }

    function check(): bool
    {
        $response = $this->application !== null && $this->device !== null;
        return $response;
    }

    function with(string $token): MobileInterface
    {
        $this->token = JwtToken::decode($token);
        /****************************************
         * Getting data
         ****************************************/
        throw_unless( $this->IDENTIFIER == $this->token->getIdentifier(), IdentifierNotMatchException::class );
        throw_unless($this->AUDIENCE == $this->token->getAudience(), AudienceNotMatchException::class );
        $application_id = Arr::get($this->token()->getPayload(), 'application_id');
        $device_identifier = Arr::get($this->token()->getPayload(), 'device_identifier');
        throw_if(is_null($application_id), ApplicationIdNotFoundException::class);
        throw_if(is_null($device_identifier), DeviceIdentifierNotFoundException::class);
        /** @var Application $application */
        $application = $this->applicationsRepository->find($application_id);
        $device      = $this->devicesRepository->findByIdentifier($device_identifier);
        throw_if(is_null($application), ApplicationNotFoundException::class);
        throw_if(is_null($device), DeviceNotFoundException::class);
        throw_unless($this->applicationsRepository->isEnabled($application->id), ApplicationIsNotEnabledException::class);
        throw_unless($device->enabled, DeviceIsNotEnabledException::class);
        $this->setApplication($application);
        $this->setDevice($device);
        $user_email = Arr::get($this->token()->getPayload(), 'user_email');
        $user = null;
        if( !is_null($user_email) )
        {
            $user = $this->usersRepository->findByEmail($user_email);
            throw_if(is_null($user), UserNotFoundException::class);
            throw_unless($user->active, UserIsNotActivatedException::class);
//            $this->devicesService->addUserHistory([
//                'device_id' => $device->id,
//                'user_id' => $user->id
//            ]);
        }
        $this->setUser($user);
        $this->devicesService->updateLastAccess($device->identifier);
        return $this;
    }

    function lastError(): ?string
    {
        return count($this->errors) > 0 ? $this->errors[count($this->errors)-1] : null;
    }

    function errors(): array
    {
        return $this->errors;
    }

    function token(): ?JwtToken
    {
        return $this->token;
    }
}