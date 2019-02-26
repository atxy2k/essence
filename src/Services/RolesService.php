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
use Atxy2k\Essence\Repositories\RolesRepository;
use Atxy2k\Essence\Validators\RolesValidator;
use Cartalyst\Sentinel\Roles\RoleInterface;
use Throwable;
use Sentinel;
use DB;

class RolesService extends Service
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
     * Update existing role
     * @param int $id
     * @param array $data
     * @return RoleInterface|null
     * @throws \Exception
     */
    public function update(int $id, array $data) : ?RoleInterface
    {
        $return = null;
        if ( $this->validator->with($data)->passes('update') )
        {
            try
            {
                \DB::beginTransaction();
                $role = Sentinel::getRoleRepository()->findById($id);
                throw_if(is_null($role), RoleNotFoundException::class);
                $permissions = [];
                foreach ( $data['routes'] as $route => $val )
                {
                    $permissions[ $route ] = boolval( intval($val) );
                }
                $role->update([
                    'name'  => array_get($data,'name'),
                    'slug'  => str_slug(array_get($data,'name')),
                    'permissions' => $permissions
                ]);
                $users = array_get($data,'users',[]);
                $role->users()->sync($users);
                $role->save();
                $return = $role;
                DB::commit();
            }
            catch ( \Throwable $e )
            {
                $this->pushError($e->getMessage());
                DB::rollBack();
            }
        }
        return $return;
    }

    /**
     * Create a new role
     * @param array $data
     * @return RoleInterface|null
     * @throws \Exception
     */
    public function create(array $data) : ?RoleInterface
    {
        $return = null;
        if ( $this->validator->with($data)->passes() )
        {
            try
            {
                DB::beginTransaction();
                $permissions = [];
                foreach ( array_get($data,'routes', []) as $route => $val )
                {
                    $permissions[ $route ] = (bool) (int) $val;
                }
                $role = $this->rolesRepository->create([
                    'name'  => array_get($data,'name'),
                    'slug'  => str_slug(array_get($data,'name')),
                    'blocked'  => str_slug(array_get($data,'blocked', 0)),
                    'permissions' => $permissions
                ]);
                throw_if(is_null($role), RoleNotCreatedException::class);
                $users = array_get($data,'users',[]);
                $role->users()->sync($users);
                $return = $this->rolesRepository->find($role->id);
                \DB::commit();
            }
            catch (Throwable $e)
            {
                \DB::rollBack();
                $this->pushError($e->getMessage());
            }
        }
        return $return;
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
            $slug = str_slug($name);
            $role = $this->rolesRepository->findBySlug($slug, $except);
            if ( !is_null($role) )
            {
                $return = false;
            }
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
        $role = $this->rolesRepository->find($id);
        try {
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

    /**
     * Clone a rol creating a copy of it and save it as new role.
     * @param int $id
     * @param string $name
     * @param bool $include_users
     * @return RoleInterface|null
     */
    public function clone( int $id, string $name , bool  $include_users = false) : ?RoleInterface
    {
        $return = null;
        $current_role = $this->rolesRepository->find($id);
        try {
            throw_if(is_null($current_role), RoleNotFoundException::class);
            $permissions = $current_role->permissions;
            $role = Sentinel::getRoleRepository()->createModel()->create([
                'name'  => trim($name),
                'slug'  => str_slug(trim($name)),
                'permissions' => $permissions
            ]);
            throw_if(is_null($role), RoleNotCreatedException::class);
            if( $include_users)
            {
                $users = $current_role->users()->pluck('id');
                $role->users()->sync($users);
            }
            $return = $role;
        } catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

}
