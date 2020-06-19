<?php


namespace Atxy2k\Essence\Services;


use Atxy2k\Essence\Exceptions\Applications\ApplicationIsNotEnabledException;
use Atxy2k\Essence\Exceptions\Applications\ApplicationNotFoundException;
use Atxy2k\Essence\Exceptions\Applications\DeviceNotFoundException;
use Atxy2k\Essence\Exceptions\Essence\UnexpectedException;
use Atxy2k\Essence\Exceptions\Essence\ValidationException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Interfaces\RepositoryInterface;
use Atxy2k\Essence\Repositories\ApplicationsRepository;
use Atxy2k\Essence\Repositories\DevicesRepository;
use Atxy2k\Essence\Validators\ApplicationsValidator;
use DB;
use Illuminate\Support\Str;
use Throwable;
use Atxy2k\Essence\Eloquent\Application;
use Exception;
use Illuminate\Support\Arr;
use Essence;

class ApplicationsService extends Service
{
    /** @var RepositoryInterface */
    protected $repository;
    /** @var DevicesRepository */
    protected $devicesRepository;

    public function __construct(ApplicationsValidator $applicationsValidator,
                                ApplicationsRepository $applicationsRepository, DevicesRepository $devicesRepository)
    {
        parent::__construct();
        $this->validator = $applicationsValidator;
        $this->repository = $applicationsRepository;
        $this->devicesRepository = $devicesRepository;
    }

    public function create(array $data) : ?Application
    {
        $return = null;
        try
        {
            DB::beginTransaction();
            throw_unless($this->validator->with($data)->passes('create'),
                new ValidationException($this->validator->errors()->first()) );
            $data['label'] = $data['name'];
            $data['app_id'] = strtoupper(uniqid());
            $data['app_secret'] = Str::uuid()->toString();
            $item = $this->repository->create($data);
            throw_if(is_null($item), UnexpectedException::class);
            DB::commit();
            $return = $this->repository->find($item->id);
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            Essence::log($e);
            DB::rollback();
        }
        return $return;
    }

    public function update(int $app_id, array $data) : ?bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $application = $this->repository->find($app_id);
            throw_if(is_null($application), ApplicationNotFoundException::class);
            throw_unless($this->validator->with($data)->passes('update'),
                new ValidationException($this->validator->errors()->first()));
            $complete = $this->repository->update($app_id, Arr::only($data, ['name','description','label']));
            throw_if(!$complete, UnexpectedException::class);
            DB::commit();
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            Essence::log($e);
            DB::rollback();
        }
        return $return;
    }

    public function delete(int $app_id) : bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $application = $this->repository->find($app_id);
            throw_if(is_null($application), ApplicationNotFoundException::class);
            $application->delete();
            DB::commit();
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            Essence::log($e);
            DB::rollback();
        }
        return $return;
    }

    public function enable(array $data) : bool
    {
        $return = false;
        try
        {
            throw_unless($this->validator->with($data)->passes('enable'),
                new ValidationException($this->validator->errors()->first()));
            $return = $this->repository->enable($data['application_id']);
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    public function disable(array $data) : bool
    {
        $return = false;
        try
        {
            throw_unless($this->validator->with($data)->passes('disable'),
                new ValidationException($this->validator->errors()->first()));
            $return = $this->repository->disable($data['application_id']);
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    public function authenticate(string $appId, string $appSecret, int $device_id) : ?JwtToken
    {
        $return = null;
        try
        {
            $application = $this->repository->findByAppIdAndAppSecret($appId, $appSecret);
            $device = $this->devicesRepository->find($device_id);
            throw_if(is_null($device), DeviceNotFoundException::class);
            throw_if( is_null($application), ApplicationNotFoundException::class );
            throw_unless($this->repository->isEnabled($application->id), ApplicationIsNotEnabledException::class);
            $return = Jwt::authenticateApp($application, $device);
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    public function stateless(string $appId, string $appSecret) : bool
    {
        $return = false;
        try
        {
            $application = $this->repository->findByAppIdAndAppSecret($appId, $appSecret);
            throw_if( is_null($application), ApplicationNotFoundException::class );
            throw_unless($this->repository->isEnabled($application->id), ApplicationIsNotEnabledException::class);
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }


}