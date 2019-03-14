<?php namespace Atxy2k\Essence\Eloquent;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 17:06
 */
use Atxy2k\Essence\Infraestructure\Model;
use App\Modules\Eloquent\Municipio;
use Cviebrock\EloquentSluggable\Sluggable;
use App\Kernel\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $table = 'states';
    protected $fillable = [ 'name', 'slug', 'key',  'user_id', 'created_at', 'updated_at' ];
    protected $guarded  = [ 'id' ];

    use Sluggable;
    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

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
}
