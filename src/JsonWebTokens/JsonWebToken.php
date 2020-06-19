<?php namespace Atxy2k\Essence\JsonWebTokens;


use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\Eloquent\Application;
use Atxy2k\Essence\Eloquent\Device;

class JsonWebToken implements JsonWebTokenInterface
{

    public function authenticateApp(Application $application, Device $device): JwtToken
    {
        $encoded = JwtToken::create([
            'application_id'    => $application->id,
            'device_identifier' => $device->identifier
        ]);
        return $encoded;
    }

    function authenticateUser(JwtToken $token, User $user): JwtToken
    {
        $token->addToPayload('user_email', $user->email);
        return $token;
    }

    function add(string $token, array $data): JwtToken
    {
        $token = JwtToken::decode($token);
        $token->addDataToPayload($data);
        return $token;
    }

    function addPayload(string $token, string $key, string $value): JwtToken
    {
        $token = JwtToken::decode($token);
        $token->addToPayload($key, $value);
        return $token;
    }

}
