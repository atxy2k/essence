<?php namespace Atxy2k\Essence\Services;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 12:43
 */

use Atxy2k\Essence\Eloquent\Role;
use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\Exceptions\Essence\NameIsNotAvailableException;
use Atxy2k\Essence\Exceptions\Interactions\InteractionNotCreatedException;
use Atxy2k\Essence\Exceptions\Interactions\InteractionNotFoundException;
use Atxy2k\Essence\Exceptions\Roles\RoleNotCreatedException;
use Atxy2k\Essence\Exceptions\Roles\RoleNotFoundException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Interfaces\Services\RolesServiceInterface;
use Atxy2k\Essence\Repositories\InteractionsTypeRepository;
use Atxy2k\Essence\Repositories\RolesRepository;
use Atxy2k\Essence\Validators\RolesValidator;
use Illuminate\Support\Str;
use Throwable;
use DB;
use Illuminate\Support\Arr;
use Essence;
use Exception;

class RolesService extends Service implements RolesServiceInterface
{

    /** @var RolesRepository */
    protected $rolesRepository;
    /** @var InteractionsService */
    protected $interactionsService;

    public function __construct(RolesValidator $rolesValidator,
                                InteractionsService $interactionsService,
                                RolesRepository $rolesRepository)
    {
        parent::__construct();
        $this->rolesRepository = $rolesRepository;
        $this->validator = $rolesValidator;
        $this->interactionsService = $interactionsService;
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
            $role->delete();
            DB::commit();
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
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
            $role = $this->rolesRepository->create($data);
            throw_if(is_null($role), RoleNotCreatedException::class);

            $interaction = $this->interactionsService->generate('create', $role);
            throw_if(is_null($interaction), InteractionNotCreatedException::class);
            DB::commit();
            $return = $role;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
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
            $role = $this->rolesRepository->find($id);
            throw_if(is_null($role), RoleNotFoundException::class);
            throw_unless($this->rolesRepository->slugFromTextIsAvailable($data['name'], $role->id),
                NameIsNotAvailableException::class);
            $data['slug'] = Str::slug($data['name']);
            $this->rolesRepository->update($id, $data);

            $this->interactionsService->generate('update', $role);
            $return = true;
            DB::commit();
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
        }
        return $return;
    }
}
