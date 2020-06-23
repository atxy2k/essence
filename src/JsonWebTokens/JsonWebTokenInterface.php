<?php


namespace Atxy2k\Essence\JsonWebTokens;


use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\Eloquent\Application;
use Atxy2k\Essence\Eloquent\Device;

interface JsonWebTokenInterface
{
    /**
     * Authenticate application with existing device
     * @param Application $application
     * @param Device $device
     * @return JwtToken
     */
    function authenticateApp(Application $application, Device $device) : JwtToken;
    function authenticateUser(JwtToken $token, User $user) : JwtToken;
    function add( string $token, array $data ) : JwtToken;
    function addPayload(string $token, string $key, string $value) : JwtToken;
}
