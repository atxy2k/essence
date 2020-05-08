<?php namespace Atxy2k\Essence\Eloquent;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 15:00
 */
use Atxy2k\Essence\Infraestructure\Model;
use Throwable;
use Essence;

class Configuration extends Model
{

    protected $table    = 'configurations';
    protected $fillable = ['key', 'value', 'encode','configurable_id', 'configurable_type'];
    protected $guarded  = ['id'];
    protected $casts = [
        'encode' => 'bool'
    ];

    public function configurable()
    {
        return $this->morphMany();
    }

    public function getValueAttribute()
    {
        $return = null;
        try
        {
            $return = $this->encode ? json_decode($this->attributes['value'], true) : $this->attributes['value'];
        }
        catch (Throwable $e)
        {
            Essence::log($e);
        }
        return $return;
    }

}
