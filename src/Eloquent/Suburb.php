<?php namespace Atxy2k\Essence\Eloquent;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 17:14
 */
use Atxy2k\Essence\Infraestructure\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suburb extends Model
{
    protected $table = 'suburbs';
    protected $fillable = [ 'name','slug','country_id','zone','postal_code', 'municipality_id', 'settlement', 'type','user_id', 'created_at', 'updated_at' ];
    protected $guarded  = [ 'id' ];

    public function __toString() : ?string
    {
        return (string) $this->nombre;
    }

    public function municipality() : ?BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function user() : ?BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
