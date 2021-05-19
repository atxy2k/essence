<?php


namespace Atxy2k\Essence\Services;


use Atxy2k\Essence\Eloquent\Installation;
use Atxy2k\Essence\Exceptions\Essence\InvalidParamsException;
use Atxy2k\Essence\Exceptions\Installations\InstallationAlreadyAuthorized;
use Atxy2k\Essence\Exceptions\Installations\InstallationNotFoundException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Repositories\InstallationsRepository;
use Atxy2k\Essence\Validators\InstallationsValidator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Throwable;
use DB;
use Essence;

class InstallationsService extends Service
{

    protected InstallationsRepository $installationsRepository;

    public function __construct(InstallationsRepository $installationsRepository,
                                InstallationsValidator $installationsValidator)
    {
        parent::__construct();
        $this->installationsRepository = $installationsRepository;
        $this->validator = $installationsValidator;
    }

    public function create(array $data) : ?Installation
    {
        $return = null;
        try
        {
            DB::beginTransaction();
            throw_unless($this->validator->with($data)->passes(), InvalidParamsException::class);
            $existent = $this->installationsRepository->find(Arr::get($data,'id'));
            if(is_null($existent))
            {
                $data = [
                    'id' => Arr::get($data, 'id'),
                    'authorization_code' => strtoupper(Str::random(5)),
                    'device_id' => Arr::get($data,'device_id'),
                    'authorized_at' => $data['activate'] ? date('Y-m-d H:i:s') : null
                ];
                $return = $this->installationsRepository->create($data);
            }
            else
            {
                $return = $existent;
            }
            DB::commit();
        }
        catch (Throwable $e)
        {
            DB::rollback();
        }
        return $return;
    }

    public function authorize(string $id) : bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $installation = $this->find($id);
            throw_if(is_null($installation), InstallationNotFoundException::class);
            throw_if($installation->is_authorized, InstallationAlreadyAuthorized::class);
            $return = $this->installationsRepository->authorize($id);
            DB::commit();
        }
        catch (Throwable $e)
        {
            DB::rollback();
        }
        return $return;
    }

}