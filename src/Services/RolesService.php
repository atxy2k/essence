<?php namespace Atxy2k\Essence\Services;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 12:43
 */

use Atxy2k\Essence\Constants\Interactions;
use Atxy2k\Essence\Eloquent\Role;
use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\Exceptions\Claims\ClaimNotFoundException;
use Atxy2k\Essence\Exceptions\Essence\NameIsNotAvailableException;
use Atxy2k\Essence\Exceptions\Interactions\InteractionNotCreatedException;
use Atxy2k\Essence\Exceptions\Interactions\InteractionNotFoundException;
use Atxy2k\Essence\Exceptions\Roles\IntegerOrStringRequiredException;
use Atxy2k\Essence\Exceptions\Roles\RoleAlreadyHaveClaimException;
use Atxy2k\Essence\Exceptions\Roles\RoleDoesNotHaveClaimException;
use Atxy2k\Essence\Exceptions\Roles\RoleIsBlockedException;
use Atxy2k\Essence\Exceptions\Roles\RoleNotCreatedException;
use Atxy2k\Essence\Exceptions\Roles\RoleNotFoundException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Interfaces\Services\RolesServiceInterface;
use Atxy2k\Essence\Repositories\InteractionsTypeRepository;
use Atxy2k\Essence\Repositories\RolesRepository;
use Atxy2k\Essence\Validators\RolesValidator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Throwable;
use DB;
use Illuminate\Support\Arr;
use Essence;
use Exception;
use Atxy2k\Essence\Repositories\ClaimsRepository;

class RolesService extends Service implements RolesServiceInterface
{

    /** @var RolesRepository */
    protected $rolesRepository;
    /** @var InteractionsService */
    protected $interactionsService;
    /** @var ClaimsRepository */
    protected $claimsRepository;

    public function __construct(RolesValidator $rolesValidator,
                                InteractionsService $interactionsService,
                                ClaimsRepository $claimsRepository,
                                RolesRepository $rolesRepository)
    {
        parent::__construct();
        $this->rolesRepository = $rolesRepository;
        $this->validator = $rolesValidator;
        $this->interactionsService = $interactionsService;
        $this->claimsRepository = $claimsRepository;
    }

    /**
     * @param string $name
     * @param int|null $except
     * @return bool
     */
    public function checkNameAvailability( string $name, int $except = null ) : bool
    {
        $return = true;
        try
        {
            $slug = Str::slug($name);
            $role = $this->rolesRepository->findBySlug($slug, $except);
            $return = is_null($role);
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            Essence::log($e);
        }
        return $return;
    }

    /**
     * Delete one role
     * @param int $id
     * @return bool
     */
    public function delete(int $id) : bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $role = $this->rolesRepository->find($id);
            throw_if(is_null($role), RoleNotFoundException::class);
            throw_if($role->blocked, RoleIsBlockedException::class);
            $role->delete();
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

    function create(array $data): ?Role
    {
        $return = null;
        try
        {
            DB::beginTransaction();
            throw_unless($this->validator->with($data)->passes('create'),
                new Exception($this->validator->errors()->first()));
            throw_unless($this->rolesRepository->slugFromTextIsAvailable($data['name']),
            NameIsNotAvailableException::class);
            $data['slug'] = Str::slug($data['name']);
            $data['blocked'] = Arr::get($data,'blocked', false);
            $role = $this->rolesRepository->create($data);
            throw_if(is_null($role), RoleNotCreatedException::class);

            $interaction = $this->interactionsService->generate(Interactions::CREATE, $role);
            throw_if(is_null($interaction), InteractionNotCreatedException::class);
            DB::commit();
            $return = $role;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
            Essence::log($e);
        }
        return $return;
    }

    function update(int $id, array $data): bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $role = $this->rolesRepository->find($id);
            throw_if(is_null($role), RoleNotFoundException::class);
            throw_unless($this->rolesRepository->slugFromTextIsAvailable($data['name'], $role->id),
                NameIsNotAvailableException::class);
            throw_if($role->blocked, RoleIsBlockedException::class);
            $data['slug'] = Str::slug($data['name']);
            $data['blocked'] = Arr::get($data,'blocked', $role->blocked );
            $this->rolesRepository->update($id, $data);

            $interaction = $this->interactionsService->generate(Interactions::UPDATE, $role);
            throw_if(is_null($interaction), InteractionNotCreatedException::class);
            $return = true;
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

    function addClaim(int $role_id, array $claims): bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $role = $this->rolesRepository->find($role_id);
            throw_if(is_null($role), RoleNotFoundException::class);
            foreach ($claims as $_claim)
            {
                throw_unless( is_integer($_claim) || is_string($_claim),
                IntegerOrStringRequiredException::class);
                $claim = null;
                if(is_integer($_claim))
                {
                    $claim = $this->claimsRepository->find($_claim);
                }
                else
                {
                    $claim = $this->claimsRepository->findByIdentifier($_claim);
                }
                throw_if(is_null($claim), ClaimNotFoundException::class);
                throw_if($role->claims->contains($claim), RoleAlreadyHaveClaimException::class);
                $role->claims()->attach($claim->id);
            }
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

    function removeClaim(int $role_id, array $claims): bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $role = $this->rolesRepository->find($role_id);
            throw_if(is_null($role), RoleNotFoundException::class);
            foreach ($claims as $_claim)
            {
                throw_unless( is_integer($_claim) || is_string($_claim),
                    IntegerOrStringRequiredException::class);
                $claim = null;
                if(is_integer($_claim))
                {
                    $claim = $this->claimsRepository->find($_claim);
                }
                else
                {
                    $claim = $this->claimsRepository->findByIdentifier($_claim);
                }
                throw_if(is_null($claim), ClaimNotFoundException::class);
                throw_unless($role->claims->contains($claim), RoleDoesNotHaveClaimException::class);
                $role->claims()->detach($claim->id);
            }
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

    function syncClaims(int $role_id, array $claims): bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $role = $this->rolesRepository->find($role_id);
            throw_if(is_null($role), RoleNotFoundException::class);
            $role->claims()->sync($claims);
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

    function getIdentifierClaims(int $role_id): ?array
    {
        $return = null;
        try
        {
            $role = $this->rolesRepository->find($role_id);
            throw_if(is_null($role), RoleNotFoundException::class);
            $return = $role->claims()->pluck('role_claims.claim_id')->all();
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            Essence::log($e);
        }
        return $return;
    }


}
