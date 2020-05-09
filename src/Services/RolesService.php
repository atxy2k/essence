<?php namespace Atxy2k\Essence\Services;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 12:43
 */

use Atxy2k\Essence\Eloquent\Role;
use Atxy2k\Essence\Exceptions\Roles\RoleNotCreatedException;
use Atxy2k\Essence\Exceptions\Roles\RoleNotFoundException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Interfaces\Services\RolesServiceInterface;
use Atxy2k\Essence\Repositories\RolesRepository;
use Atxy2k\Essence\Validators\RolesValidator;
use Illuminate\Support\Str;
use Throwable;
use DB;
use Illuminate\Support\Arr;
use Essence;

class RolesService extends Service implements RolesServiceInterface
{

    /** @var RolesRepository */
    protected $rolesRepository;

    public function __construct(RolesValidator $rolesValidator, RolesRepository $rolesRepository)
    {
        parent::__construct();
        $this->rolesRepository = $rolesRepository;
        $this->validator = $rolesValidator;
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
            $return = !is_null($role);
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
            $role = $this->rolesRepository->find($id);
            throw_if(is_null($role), RoleNotFoundException::class);
            $role->delete();
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    function create(array $data): ?Role
    {
        // TODO: Implement create() method.
    }

    function update(int $id, array $data): ?Role
    {
        // TODO: Implement update() method.
    }
}
