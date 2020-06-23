<?php namespace Atxy2k\Essence\JsonWebTokens;


use Illuminate\Support\ServiceProvider;
use App;

class JsonWebTokenServiceProvider extends ServiceProvider
{
    public function register()
    {
        App::bind('jwt', function(){
            return App::make(JsonWebToken::class);
        });
    }
}
