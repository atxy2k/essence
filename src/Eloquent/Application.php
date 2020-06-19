<?php namespace Atxy2k\Essence\Eloquent;


use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'applications';
    protected $fillable = ['app_id','app_secret', 'name', 'description', 'enabled','created_at','updated_at'];
    protected $guarded = ['id'];
    protected $casts = [
        'enabled' => 'boolean'
    ];

}