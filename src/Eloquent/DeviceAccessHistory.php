<?php


namespace Atxy2k\Essence\Eloquent;


use Illuminate\Database\Eloquent\Model;

class DeviceAccessHistory extends Model
{
    protected $table = 'devices_access_history';
    protected $fillable = [ 'user_id', 'device_location_history_id','device_id', 'old_access', 'created_at', 'updated_at' ];
    protected $guarded  = [ 'id' ];
    protected $with     = ['location','user'];

    public function location()
    {
        return $this->belongsTo(DeviceLocationHistory::class, 'device_location_history_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class,'device_id');
    }
}