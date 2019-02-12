<?php namespace Atxy2k\Essence\Repositories;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 17:47
 */
use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\Infraestructure\Repository;
use IteratorAggregate;
use Sentinel;

class UsersRepository extends Repository
{

    protected $model = User::class;

    public function admins() : IteratorAggregate
    {
        $admins = Sentinel::getRoleRepository()->findBySlug(config('essence.admin_role_slug'));
        return $admins->getUsers();
    }

}
