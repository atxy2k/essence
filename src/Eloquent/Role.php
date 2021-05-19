<?php namespace Atxy2k\Essence\Eloquent;

use Atxy2k\Essence\Traits\Interactuable;
use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 12:14
 */
class Role extends Model
{
    use Interactuable;

    protected $fillable = ['slug', 'name', 'blocked'];
    protected $guarded = ['id'];
    protected $casts = [
        'blocked' => 'bool'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function claims()
    {
        return $this->belongsToMany(Claim::class, 'role_claims');
    }

}
