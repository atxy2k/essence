<?php


namespace Atxy2k\Essence\Mobile;


use Illuminate\Support\Facades\Facade;

class MobileFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mobile';
    }
}