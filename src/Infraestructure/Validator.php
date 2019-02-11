<?php namespace Atxy2k\Essence\Infraestructure;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 12:19
 */
use Atxy2k\Essence\Interfaces\ValidatorInterface;
use Illuminate\Support\MessageBag;
use Validator as LaravelValidator;

class Validator implements ValidatorInterface
{
    protected $rules = [
        'create' => [],
        'update' => []
    ];
    /** @var array  */
    protected $ignore = [];
    /** @var MessageBag  */
    protected $errors = null;
    /** @var array  */
    protected $data = [];

    public function __construct()
    {
        $this->errors = new MessageBag();
    }

    /**
     * @param array $data
     * @return ValidatorInterface
     */
    public function with(array $data = []): ValidatorInterface
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param bool $key
     * @return array
     */
    public function getRules($key = false): array
    {
        if ( $key!=false && is_string($key) && strlen($key) > 0 && isset($this->rules[$key]) ) return $this->rules[$key];
        return $this->rules;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function passes($key = 'create'): bool
    {
        $aux = $this->rules[$key];
        foreach ( $this->ignore as $ignore )
        {
            unset($aux[$ignore]);
        }
        $validator = LaravelValidator::make($this->data, $aux);
        $return = $validator->passes();
        $this->errors = $validator->errors();
        return $return;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function fails($key = 'create'): bool
    {
        $aux = $this->rules[$key];
        foreach ( $this->ignore as $ignore )
        {
            unset($aux[$ignore]);
        }
        $validator = LaravelValidator::make($this->data, $aux);
        $return = $validator->fails();
        $this->errors = $validator->errors();
        return $return;
    }

    /**
     * @param string $parent
     * @param bool $key
     * @param bool $value
     * @return ValidatorInterface
     */
    public function add($parent = 'create', $key = false, $value = false): ValidatorInterface
    {
        if ( isset($this->rules[$parent]) && $key!= false && $value!= false && is_string($key) && strlen($key) > 0  && $value!= false)
        {
            $this->rules[$parent] +=  [ $key => $value ];
        }
        return $this;
    }

    /**
     * @param array $key
     * @return ValidatorInterface
     */
    public function ignore(array $key = []): ValidatorInterface
    {
        $this->ignore = $key;
        return $this;
    }

    /**
     * @return MessageBag
     */
    public function errors(): MessageBag
    {
        return $this->errors;
    }
}
