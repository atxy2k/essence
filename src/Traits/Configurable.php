<?php


namespace Atxy2k\Essence\Traits;


use Atxy2k\Essence\Eloquent\Configuration;

trait Configurable
{
    public function comments()
    {
        return $this->morphMany(Configuration::class, 'configurable');
    }
}