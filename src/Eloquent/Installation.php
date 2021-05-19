<?php


namespace Atxy2k\Essence\Eloquent;


use Illuminate\Database\Eloquent\Model;

class Installation extends Model
{
    protected $keyType = 'string';
    protected $table = 'installations';
    protected $fillable = ['id','authorization_code', 'device_id', 'authorized_at'];
    protected $dates = ['authorized_at'];

    public function getIsAuthorizedAttribute()
    {
        return !is_null($this->attributes['authorized_at']);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

}