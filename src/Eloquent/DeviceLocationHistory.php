<?php


namespace Atxy2k\Essence\Eloquent;

use Illuminate\Database\Eloquent\Model;

class DeviceLocationHistory extends Model
{
    protected $table = 'device_location_history';
    protected $fillable = ['device_id', 'latitude', 'longitude', 'date','created_at', 'updated_at'];
    protected $dates = ['date'];
    protected $guarded  = ['id'];
    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
    ];
}