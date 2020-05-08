<?php


namespace Atxy2k\Essence\Traits;


use Atxy2k\Essence\Eloquent\Interaction;

trait Interactuable
{
    public function interactions()
    {
        return $this->morphToMany(Interaction::class, 'interactuable');
    }
}