<?php namespace Atxy2k\Essence\Eloquent;
use Cartalyst\Sentinel\Roles\EloquentRole;

/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 12:14
 */
class Role extends EloquentRole
{

    protected $fillable = ['slug', 'name', 'permissions', 'created_at', 'updated_at','blocked'];
    protected $guarded = ['id'];
    protected $casts = [
        'blocked' => 'bool'
    ];

}
