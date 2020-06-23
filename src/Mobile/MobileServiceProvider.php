<?php


namespace Atxy2k\Essence\Mobile;


use Illuminate\Support\ServiceProvider;
use App;

class MobileServiceProvider extends ServiceProvider
{
    public function register()
    {
        App::bind('mobile', function(){
            return App::make(Mobile::class);
        });
    }
}