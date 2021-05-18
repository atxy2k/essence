<?php


namespace Atxy2k\Essence\Tests;

use Atxy2k\Essence\JsonWebTokens\JwtToken;

class JwtTest extends TestCase
{
    public function testCreateSimpleInstanceReturnJwtToken()
    {
        $identifier = config('app.url');
        $audience   = config('app.url');
        $hash       = config('app.key');
        $jwtToken = new JwtToken();
        $this->assertNotNull($jwtToken->getIdentifier());
        $this->assertNotNull($jwtToken->getAudience());
        $this->assertNotNull($jwtToken->getHash());
        $this->assertEquals($identifier, $jwtToken->getIdentifier());
        $this->assertEquals($audience, $jwtToken->getAudience());
        $this->assertEquals($hash, $jwtToken->getHash());
        $this->assertNull($jwtToken->getExpiration());
        $this->assertNotNull($jwtToken->getPayload());
        $this->assertIsArray($jwtToken->getPayload());
        $this->assertNotNull($jwtToken->toString());
        $this->assertIsString($jwtToken->toString());
    }

    public function testEncodeWithPayloadDataNotThrowError()
    {
        $identifier = config('app.url');
        $audience   = config('app.url');
        $hash       = config('app.key');

        $payload = [
            'name' => 'Ivan',
            'last_name' => 'Alvarado'
        ];
        $jwtToken = JwtToken::create($payload);
        $this->assertNotNull($jwtToken);
        $jwt = $jwtToken->toString();
        $this->assertIsString($jwt);

        $decoded = JwtToken::decode($jwt);
        $this->assertNotNull($decoded);
        $this->assertInstanceOf(JwtToken::class, $decoded);

        $this->assertNotNull($decoded->getIdentifier());
        $this->assertNotNull($decoded->getAudience());
        $this->assertNotNull($decoded->getHash());
        $this->assertEquals($identifier, $decoded->getIdentifier());
        $this->assertEquals($audience, $decoded->getAudience());
        $this->assertEquals($hash, $decoded->getHash());
        $this->assertEquals($payload['name'], $decoded->getPayload()['name']);
        $this->assertEquals($payload['last_name'], $decoded->getPayload()['last_name']);
    }

    public function testEncodeWithBigPayloadDataNotThrowError()
    {
        $identifier = config('app.url');
        $audience   = config('app.url');
        $hash       = config('app.key');

        $payload = [
            'name'      => 'Ivan',
            'last_name' => 'Alvarado',
            'user_id'   => 1,
            'email'     => 'ivan.alvarado@serprogramador.es',
            'app_id'    => 'this is my app id',
            'app_secret'=> 'this ni my app secret',
            'another_jwt_data' => 'lorem impsum dolor'
        ];
        $jwtToken = JwtToken::create($payload);
        $this->assertNotNull($jwtToken);
        $jwt = $jwtToken->toString();
        $this->assertIsString($jwt);

        $decoded = JwtToken::decode($jwt);
        $this->assertNotNull($decoded);
        $this->assertInstanceOf(JwtToken::class, $decoded);

        $this->assertNotNull($decoded->getIdentifier());
        $this->assertNotNull($decoded->getAudience());
        $this->assertNotNull($decoded->getHash());
        $this->assertEquals($identifier, $decoded->getIdentifier());
        $this->assertEquals($audience, $decoded->getAudience());
        $this->assertEquals($hash, $decoded->getHash());
        $this->assertEquals($payload['name'], $decoded->getPayload()['name']);
        $this->assertEquals($payload['last_name'], $decoded->getPayload()['last_name']);
        $this->assertEquals($payload['user_id'], $decoded->getPayload()['user_id']);
        $this->assertEquals($payload['email'], $decoded->getPayload()['email']);
        $this->assertEquals($payload['app_id'], $decoded->getPayload()['app_id']);
        $this->assertEquals($payload['app_secret'], $decoded->getPayload()['app_secret']);
        $this->assertEquals($payload['another_jwt_data'], $decoded->getPayload()['another_jwt_data']);
    }
}