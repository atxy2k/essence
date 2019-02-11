<?php

namespace Atxy2k\Essence\Facades;

use Illuminate\Support\Facades\Facade;

class Essence extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'essence';
    }
}
