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
        'label',
        'name',
        'last_connection',
        'user_agent',
        'platform',
        'webdriver',
        'language',
        'color_depth',
        'device_memory',
        'hardware_concurrency',
        'timezone',
        'session_storage',
        'localstorage',
        'indexed_db',
        'open_database',
        'cpu_class',
        'enabled'
    ];
    protected $casts = [
        'session_storage' => 'boolean',
        'localstorage' => 'boolean',
        'indexed_db' => 'boolean',
        'open_database' => 'boolean',
        'cpu_class' => 'boolean',
        'enabled'   => 'boolean',
        'type'      => 'integer'
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