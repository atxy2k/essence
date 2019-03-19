<?php namespace Atxy2k\Essence\Eloquent;
/*
* Created by PhpStorm.
* User: Ivan Alvarado
* Date: 31/05/2018* Time: 16:14*/
use Atxy2k\Essence\Infraestructure\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $table = 'countries';
    protected $fillable = [ 'name','slug','user_id', 'created_at', 'updated_at' ];
    protected $guarded  = [ 'id' ];

    public function __toString() : ?string
    {
        return (string) $this->name;
    }

    public function user() : ?BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function states() : ?HasMany
    {
        return $this->hasMany(State::class);
    }

}
