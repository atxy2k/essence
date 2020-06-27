<?php namespace Atxy2k\Essence\Repositories;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 17:47
 */
use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\Infraestructure\Repository;
use Illuminate\Support\Collection;
use Atxy2k\Essence\Eloquent\Role;

class UsersRepository extends Repository
{

    protected $model = User::class;

    public function findByEmail(string $email, $except_id = null) : ?User
    {
        return !is_null($except_id) ?
            $this->query
                ->where('email', $email)
                ->where('id', '!=',$except_id)->first() :
            $this->query
                ->where('email', $email)->first();
    }

    public function claims(int $user_id) : Collection
    {
        $user = $this->find($user_id);
        /** @var Collection $claims */
        $claims = new Collection();
        $user->claims->each(function($c) use (&$claims){
            $claims->add($c);
        });
        $user->roles->each(function($role) use (&$claims){
            $role->claims->each(function($c) use (&$claims){
                $claims->add($c);
            });
        });
        return $claims;
    }

    public function getIdentifierPermissions(User $user) : array
    {
        $claims = $user->claims()->pluck('identifier')->all();
        $role_claims = $user->roles->map(function($item){
           return $item->claims;
        });
        foreach ($role_claims as $claim_collection)
        {
            $claim_collection->each(function($claim) use (&$claims){
                if( !in_array($claim->identifier, $claims) )
                {
                    $claims[] = $claim->identifier;
                }
            });
        }
        return $claims;
    }

    public function hasPermission(User $user, string $permission) : bool
    {
        $claims = $user->claims()->pluck('identifier')->all();
        $role_claims = $user->roles->map(function($item){
           return $item->claims;
        });
        foreach ($role_claims as $claim_collection)
        {
            $claim_collection->each(function($claim) use (&$claims){
                if( !in_array($claim->identifier, $claims) )
                {
                    $claims[] = $claim->identifier;
                }
            });
        }
        return in_array($permission, $claims);
    }


}
