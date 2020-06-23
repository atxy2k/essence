<?php


namespace Atxy2k\Essence\JsonWebTokens;


use Illuminate\Support\Facades\Facade;

class JsonWebTokenFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'jwt';
    }
}
