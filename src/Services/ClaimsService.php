<?php namespace Atxy2k\Essence\Services;

use Atxy2k\Essence\Eloquent\Claim;
use Atxy2k\Essence\Exceptions\Claims\ClaimAlreadyDisabledException;
use Atxy2k\Essence\Exceptions\Claims\ClaimAlreadyEnabledException;
use Atxy2k\Essence\Exceptions\Claims\ClaimNotCreatedException;
use Atxy2k\Essence\Exceptions\Claims\ClaimNotFoundException;
use Atxy2k\Essence\Exceptions\Claims\ClaimNotUpdatedException;
use Atxy2k\Essence\Exceptions\Essence\NameIsNotAvailableException;
use Atxy2k\Essence\Exceptions\Essence\ValidationException;
use Atxy2k\Essence\Infrastructure\Service;
use Atxy2k\Essence\Interfaces\Services\ClaimsServiceInterface;
use Atxy2k\Essence\Repositories\ClaimsRepository;
use Atxy2k\Essence\Validators\ClaimsValidator;
use Illuminate\Support\Arr;
use Throwable;
use Exception;
use Essence;
use DB;

class ClaimsService extends Service implements ClaimsServiceInterface
{
    /** @var ClaimsRepository */
    protected $repository;

    /**
     * ClaimsService constructor.
     */
    public function __construct(ClaimsRepository $claimsRepository,
                                ClaimsValidator $claimsValidator)
    {
        parent::__construct();
        $this->repository = $claimsRepository;
        $this->validator = $claimsValidator;
    }

    function create(array $data): ?Claim
    {
        $return = null;
        try
        {
            DB::beginTransaction();
            throw_unless($this->validator->with($data)->passes('create'),
                new ValidationException($this->validator->errors()->first()));
            throw_unless($this->repository->identifierIsAvailable($data['identifier']),
            NameIsNotAvailableException::class);
            $item = $this->repository->create($data);
            throw_if(is_null($item), ClaimNotCreatedException::class);
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

    function update(int $id, array $data): bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $item = $this->repository->find($id);
            throw_if(is_null($item), ClaimNotFoundException::class);
            throw_unless($this->validator->with($data)->passes('update'),
                new ValidationException($this->validator->errors()->first()));
            throw_unless($this->repository->identifierIsAvailable($data['identifier'], $id),
                NameIsNotAvailableException::class);
            $updated = $this->repository->update($id, $data);
            throw_unless($updated, ClaimNotUpdatedException::class);
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

    function delete(int $id): bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $item = $this->repository->find($id);
            throw_if(is_null($item), ClaimNotFoundException::class);
            $item->delete();
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

    function isIdentifierAvailability(string $name, int $except_id = null): bool
    {
        return $this->repository->identifierIsAvailable($name, $except_id);
    }

    function enabled(int $id): bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $item = $this->repository->find($id);
            throw_if(is_null($item), ClaimNotFoundException::class);
            throw_if($item->enabled, ClaimAlreadyEnabledException::class);
            $item->enabled = true;
            $item->save();
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

    function disabled(int $id): bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $item = $this->repository->find($id);
            throw_if(is_null($item), ClaimNotFoundException::class);
            throw_if(!$item->enabled, ClaimAlreadyDisabledException::class);
            $item->enabled = false;
            $item->save();
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
}