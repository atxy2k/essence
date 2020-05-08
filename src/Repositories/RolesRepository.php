<?php namespace Atxy2k\Essence\Repositories;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 17:57
 */

use Atxy2k\Essence\Eloquent\Role;
use Atxy2k\Essence\Infraestructure\Repository;
use Atxy2k\Essence\Traits\Sluggable;
use Illuminate\Support\Collection;

class RolesRepository extends Repository
{
    protected $model = Role::class;
    use Sluggable;

    public function notAdminRoles() : Collection
    {
        return $this->query->where('slug','!=', config('essence.admin_role_slug'))->get();
    }

    public function isAdminRole(int $role_id) : bool
    {
        $role = $this->find($role_id);
        return $role->slug === config('essence.admin_role_slug');
    }
}
