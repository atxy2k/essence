<?php namespace Atxy2k\Essence\Eloquent;
use Atxy2k\Essence\Infraestructure\Model;
use Atxy2k\Essence\Traits\Configurable;
use Atxy2k\Essence\Traits\Interactuable;

/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 11:25
 */

class User extends Model
{
    use Interactuable;
    use Configurable;

    protected $fillable = [
        'email', 'password', 'permissions',
        'first_name', 'last_name', 'active',
        'activated_at', 'created_at', 'updated_at'];
    protected $guarded  = [ 'id' ];
    protected $appends  = [ 'full_name', 'is_admin', 'is_activated' ];
    protected $dates    = ['activated_at'];

    public function getIsAdminAttribute()
    {
        return false;
    }

    public function getFullNameAttribute() : string
    {
        return vsprintf('%s %s', [ $this->first_name, $this->last_name ]);
    }

    public function getIsActivatedAttribute() : bool
    {
        return false;
    }

    public function changeEmailRequests()
    {
        return $this->hasMany(ChangeEmailRequest::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

}
