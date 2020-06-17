<?php namespace Atxy2k\Essence\Eloquent;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 11:25
 */
use Atxy2k\Essence\Infraestructure\Model;
use Atxy2k\Essence\Traits\Configurable;
use Atxy2k\Essence\Traits\Interactuable;
use Illuminate\Foundation\Auth\User as Authenticable;

class User extends Authenticable
{
    use Interactuable;
    use Configurable;

    protected $fillable = [
        'email', 'password', 'permissions',
        'first_name', 'last_name', 'active',
        'activated_at', 'created_at', 'updated_at'];
    protected $guarded  = [ 'id' ];
    protected $appends  = [ 'full_name', 'is_admin' ];
    protected $dates    = ['activated_at'];
    protected $casts = [
        'active' => 'boolean'
    ];

    public function setEmailAttribute(string $email)
    {
        $this->attributes['email'] = $email;
    }

    public function getIsAdminAttribute()
    {
        return false;
    }

    public function getFullNameAttribute() : string
    {
        return vsprintf('%s %s', [ $this->first_name, $this->last_name ]);
    }

    public function changeEmailRequests()
    {
        return $this->hasMany(ChangeEmailRequest::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function claims()
    {
        return $this->belongsToMany(Claim::class, 'user_claims');
    }

}
