<?php namespace Atxy2k\Essence\Eloquent;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 11:25
 */
use Cartalyst\Sentinel\Users\EloquentUser;
use Activation;

class User extends EloquentUser
{

    protected $fillable = [ 'email', 'password', 'permissions', 'last_login', 'first_name', 'last_name', 'created_at', 'updated_at'];
    protected $guarded  = [ 'id' ];
    protected $dates    = [ 'last_login', 'created_at', 'updated_at' ];
    protected $appends  = [ 'full_name' ];

    public function getIsAdminAttribute()
    {
        return $this->inRole( config('essence.admin_role_slug') );
    }

    public function getFullNameAttribute() : string
    {
        return vsprintf('%s %s', [ $this->first_name, $this->last_name ]);
    }

    public function getIsActivatedAttribute() : bool
    {
        $activation = Activation::completed($this);
        return !is_null($activation) && $activation!=false;
    }

    public function changeEmailRequests()
    {
        return $this->hasMany(ChangeEmailRequest::class);
    }

}
