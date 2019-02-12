<?php namespace Atxy2k\Essence\Http;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 17:07
 */
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Response;
use Illuminate\Support\MessageBag;

class HttpResponse implements Arrayable, Jsonable
{
    /** @var int  */
    protected $status = Response::HTTP_OK;
    /** @var array  */
    protected $errors = [];
    /** @var array|null */
    protected $data   = null;

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray() : array
    {
        return [
            'status'    => $this->status,
            'errors'    => $this->errors,
            'first_error' => count($this->errors) > 0 ? array_first($this->errors) : null,
            'data'      => $this->data
        ];
    }

    /**
     * Add message to messages stack
     * @param string $error
     * @return HttpResponse
     */
    public function addError(string $error) : HttpResponse
    {
        $this->errors[] = $error;
        return $this;
    }

    /**
     * Add message to messages stack
     * @param string $error
     * @return HttpResponse
     */
    public function pushError(string $error) : HttpResponse
    {
        return $this->addError($error);
    }

    /**
     * Add errors to http response
     * @param array|MessageBag $errors
     * @return HttpResponse
     */
    public function addErrors( $errors = [] ) : HttpResponse
    {
        if( $errors instanceof MessageBag)
            foreach ($errors->toArray() as $key => $value)
                $this->pushError($value);
        if(is_array($errors))
            $this->errors = array_merge($this->errors, $errors);
        return $this;
    }

    /**
     * Alternative to addErrors function
     * @param array $errors
     * @return HttpResponse
     */
    public function pushErrors( $errors = [] ) : HttpResponse
    {
        return $this->addErrors($errors);
    }

    /**
     * Set status to http response, default is OK
     * @param int $status
     * @return HttpResponse
     */
    public function withStatus( int $status ) : HttpResponse
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Add data to httpResponse, you can pass any Arrayable object
     * @param $data array|Arrayable
     * @return HttpResponse
     */
    public function withData( $data ) : HttpResponse
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Return current data of the response
     * @return array|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Make instance
     * @return HttpResponse
     */
    public static function create() : HttpResponse
    {
        return new HttpResponse();
    }

    /**
     * Make instance
     * @return HttpResponse
     */
    public static function make() : HttpResponse
    {
        return new HttpResponse();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Get current status
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

}
