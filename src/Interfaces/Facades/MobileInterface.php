<?php


namespace Atxy2k\Essence\Interfaces\Facades;


use Atxy2k\Essence\Eloquent\Application;
use Atxy2k\Essence\Eloquent\Device;
use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\JsonWebTokens\JwtToken;

interface MobileInterface
{
    function setApplication(Application $application) : MobileInterface;
    function getApplication() : Application;

    function setDevice(Device $device) :  MobileInterface;
    function getDevice() : Device;

    function setUser(User $user) : MobileInterface;
    function getUser() : User;
    function isAuthenticated() : bool ;

    function check() : bool ;
    function with(string $token) : MobileInterface ;
    function token() : ?JwtToken;
    function lastError() : ?string;
    function errors() : array;
}