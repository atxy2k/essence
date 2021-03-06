<?php


namespace Atxy2k\Essence\Traits;


use Atxy2k\Essence\Eloquent\InteractionType;

trait Interactuable
{
    public function interactions()
    {
        return $this->morphToMany(InteractionType::class,
            'interactuable',
            'interactions',
            'interactuable_id',
            'interaction_id'
        )->withPivot(['created_by','created_at','updated_at']);
    }
}