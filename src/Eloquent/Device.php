<?php


namespace Atxy2k\Essence\Eloquent;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Device extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'type',
        'subtype',
        'label',
        'name',
        'last_connection',
        'enabled'
    ];
    protected $casts = [
        'enabled'   => 'boolean',
        'type'      => 'integer',
        'subtype'   => 'integer'
    ];
    protected $dates = ['last_connection'];

    public function access_history()
    {
        return $this->hasMany(DeviceAccessHistory::class);
    }

    public function location_history()
    {
        return $this->hasMany(DeviceLocationHistory::class);
    }

    public function apps()
    {
        return $this->belongsToMany(Application::class, 'authorized_apps');
    }

}