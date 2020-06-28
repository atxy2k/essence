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

    public function getStandardRoles() : Collection
    {
        $admin_role = config('essence.admin_role_slug');
        return $this->query->where('slug','!=', $admin_role)->get();
    }

    public function getAdminRole() : ?Role
    {
        $admin_role = config('essence.admin_role_slug');
        return $this->query->where('slug', $admin_role)->first();
    }

}
