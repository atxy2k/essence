<?php namespace Atxy2k\Essence\Eloquent;
/*
* Created by PhpStorm.
* User: Ivan Alvarado
* Date: 31/05/2018* Time: 16:14*/
use Cviebrock\EloquentSluggable\Sluggable;
use Atxy2k\Essence\Infraestructure\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Country extends Model
{
    protected $table = 'countries';
    protected $fillable = [ 'name','slug','user_id', 'created_at', 'updated_at' ];
    protected $guarded  = [ 'id' ];

    use Sluggable;

    public function sluggable() : array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function __toString() : ?string
    {
        return (string) $this->name;
    }

    public function user() : ?BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function user_updated() : ?BelongsTo
    {
        return $this->belongsTo(User::class,'user_updated_id');
    }

}
