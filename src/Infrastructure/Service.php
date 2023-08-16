<?php namespace Atxy2k\Essence\Infrastructure;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 15:19
 */
use Atxy2k\Essence\Infrastructure\Validator;
use Atxy2k\Essence\Contracts\ServiceInterface;
use Illuminate\Support\MessageBag;

class Service implements ServiceInterface
{
    protected Validator|null $validator;
    protected MessageBag $errors;
    public function __construct(){
        $this->errors = new MessageBag();
    }

    public function errors() : MessageBag
    {
        if(!is_null($this->validator))
            $this->errors->merge($this->validator->errors());
        return $this->errors;
    }

    public function pushErrors(MessageBag $errors) : ServiceInterface
    {
        $this->errors->merge($errors);
        return $this;
    }

    public function pushError(string $message, string $key = 'error') : ServiceInterface
    {
        $this->errors->add($key, $message);
        return $this;
    }

    public function cleanErrors() : ServiceInterface
    {
        $this->errors = new MessageBag();
        return $this;
    }

}