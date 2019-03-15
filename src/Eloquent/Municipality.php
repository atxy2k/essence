<?php namespace Atxy2k\Essence\Eloquent;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 17:10
 */
use Atxy2k\Essence\Infraestructure\Model;
use Atxy2k\Essence\Eloquent\State;
use Atxy2k\Essence\Eloquent\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Eloquent\Localidad;

class Municipality extends Model
{
    protected $table = 'municipalities';
    protected $fillable = [ 'name','key','slug','state_id', 'user_id', 'created_at', 'updated_at','deleted_at' ];
    protected $guarded  = [ 'id' ];
    use SoftDeletes;

    public function __toString() : ?string
    {
        return (string) $this->nombre;
    }

    public function user() : ?BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function state() : ?BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function suburbs() : ?HasMany
    {
        return $this->hasMany(Suburb::class);
    }
}
