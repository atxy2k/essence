<?php namespace Atxy2k\Essence\Repositories;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 17:57
 */

use Atxy2k\Essence\Eloquent\Role;
use Atxy2k\Essence\Infraestructure\Repository;
use Illuminate\Support\Collection;

class RolesRepository extends Repository
{
    protected $model = Role::class;

    public function notAdminRoles() : Collection
    {
        return $this->query->where('slug','!=', config('essence.admin_role_slug'))->get();
    }
}
