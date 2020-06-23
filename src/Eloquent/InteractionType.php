<?php namespace Atxy2k\Essence\Eloquent;

use Atxy2k\Essence\Infraestructure\Model;

class InteractionType extends Model
{
    protected $table = 'interactions_type';
    protected $fillable = ['name', 'slug', 'description'];
    protected $guarded = ['id'];

    public function users()
    {
        return $this->morphedByMany(User::class,
            'interactuable',
            'interactions',
            'interaction_id'
        );
    }

    public function roles()
    {
        return $this->morphedByMany(Role::class,
            'interactuable',
            'interactions',
            'interaction_id'
        );
    }

}