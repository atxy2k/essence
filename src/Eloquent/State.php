<?php namespace Atxy2k\Essence\Eloquent;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 17:06
 */
use Atxy2k\Essence\Infraestructure\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $table = 'states';
    protected $fillable = [ 'name', 'slug', 'country_id', 'key', 'user_id', 'created_at', 'updated_at' ];
    protected $guarded  = [ 'id' ];

    public function __toString() : ?string
    {
        return (string) $this->nombre;
    }

    public function user() : ?BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function municipalities() : ?HasMany
    {
        return $this->hasMany(Municipality::class);
    }

    public function country() : ?BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
