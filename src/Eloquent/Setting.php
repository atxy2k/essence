<?php namespace Atxy2k\Essence\Eloquent;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 15:00
 */
use Atxy2k\Essence\Infraestructure\Model;
use Throwable;

class Setting extends Model
{

    protected $table    = 'settings';
    protected $fillable = ['key', 'value', 'encode', 'user_id'];
    protected $guarded  = ['id'];
    protected $casts = [
        'encode' => 'bool'
    ];

    public function getValueAttribute()
    {
        $return = null;
        try
        {
            $return = $this->encode ? json_decode($this->attributes['value'], true) : $this->attributes['value'];
        }
        catch (Throwable $e)
        {
            logger($e->getMessage());
        }
        return $return;
    }

}
