<?php namespace Atxy2k\Essence\JsonWebTokens;


use Atxy2k\Essence\Eloquent\Application;
use Atxy2k\Essence\Eloquent\Device;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Nowakowskir\JWT\JWT;
use Nowakowskir\JWT\TokenDecoded;
use Nowakowskir\JWT\TokenEncoded;

class JwtToken implements Arrayable
{
    /** @var string */
    protected $identifier = null;
    /** @var string */
    protected $audience = null;
    /** @var Carbon|null  */
    protected $expiration = null;
    /** @var string */
    protected $hash = null;
    /** @var string  */
    protected $alg = JWT::ALGORITHM_HS256;
    /** @var string */
    private const DATE_FORMAT = "Y-m-d H:i:s";

    /** @var array  */
    protected $payload = [];

    /**
     * Don't use constructor, use create and decode functions.
     * JwtToken constructor.
     */
    public function __construct()
    {
        $this->identifier = config('app.url');
        $this->audience   = config('app.url');
        $this->hash       = config('app.key');
    }

    /**
     * Create jwtToken from payload and expiration time
     * @param array $payload
     * @param Carbon|null $expiration
     * @return JwtToken
     */
    public static function create(array $payload = [], Carbon $expiration = null) : JwtToken
    {
        $item = new self();
        $item->setPayload($payload);
        $item->setExpiration($expiration);
        return $item;
    }

    /**
     * Decode jwt token and return jwtToken object
     * @param string $token
     * @return JwtToken
     * @throws \Nowakowskir\JWT\Exceptions\EmptyTokenException
     */
    public static function decode(string $token) : JwtToken
    {
        $t = new JwtToken();
        $tokenEncoded = new TokenEncoded($token);
        $tokenEncoded->validate($t->hash, $t->alg);
        /** @var TokenDecoded $tokenDecode */
        $tokenDecode = $tokenEncoded->decode();
        $payload = $tokenDecode->getPayload();
        $item = new self();
        $item->setIdentifier(Arr::get($payload, 'identifier'));
        $item->setAudience(Arr::get($payload, 'audience'));
        $item->setPayload($payload);
        $item->setExpiration( $payload['expiration'] !== null ? Carbon::createFromFormat(self::DATE_FORMAT, $payload['expiration']) : null );
        return $item;
    }

    /**
     * Set payload for generate string token
     * @param array $payload
     */
    public function setPayload(array $payload) : void
    {
        $this->payload = $payload;
    }

    /**
     * Return payload
     * @return array
     */
    public function getPayload() : array
    {
        return $this->payload;
    }

    /**
     * Set expiration date time
     * @param Carbon|null $carbon
     */
    public function setExpiration(Carbon $carbon = null) : void
    {
        $this->expiration = $carbon;
    }

    /**
     * Return expiration time or null
     * @return Carbon|null
     */
    public function getExpiration() : ?Carbon
    {
        return $this->expiration;
    }

    public function getHash() : string
    {
        return $this->hash;
    }

    public function getIdentifier() : string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier) : void
    {
        $this->identifier = $identifier;
    }

    public function setAudience(string $audience) : void
    {
        $this->audience = $audience;
    }

    public function addToPayload(string $key, string $value) : JwtToken
    {
        $this->payload[$key] = $value;
        return $this;
    }

    public function addDataToPayload(array $payload) : JwtToken
    {
        $this->payload = array_merge($this->payload, $payload);
        return $this;
    }


    public function getAudience() : string
    {
        return $this->audience;
    }

    public function __toString() : string
    {
        $tokenDecode = new TokenDecoded([ 'alg' => $this->alg ], $this->toArray() );
        $tokenEncoded = $tokenDecode->encode($this->hash);
        return $tokenEncoded->__toString();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge([
            'identifier' => $this->identifier,
            'audience'   => $this->audience,
            'expiration' => $this->expiration !== null ? $this->expiration->format(self::DATE_FORMAT) : null
        ], $this->payload);
    }
}
