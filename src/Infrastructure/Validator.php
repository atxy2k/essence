<?php namespace Atxy2k\Essence\Infrastructure;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 12:19
 */
use Atxy2k\Essence\Contracts\ValidatorInterface;
use Atxy2k\Essence\Throwables\RuleNotFoundException;
use Atxy2k\Essence\Throwables\ValueCannotBeEmptyException;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator as LaravelValidator;

class Validator implements ValidatorInterface
{
    protected array $rules = [];
    protected array $ignore = [];
    protected array $data = [];

    public function __construct(private MessageBag $errors)
    {}
    public function with(array $data = []): ValidatorInterface
    {
        $this->data = $data;
        return $this;
    }
    public function getRules(string|null $key = null): array
    {
        throw_unless(!is_null($key) && !empty($this->rules[$key]), RuleNotFoundException::class);
        if(!is_null($key)) return $this->rules[$key];
        return $this->rules;
    }

    public function passes(string $key = 'create'): bool
    {
        throw_unless(!empty(Arr::has($this->getRules(), $key)), RuleNotFoundException::class);
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

    public function fails(string $key = 'create'): bool
    {
        throw_unless(!empty(Arr::has($this->getRules(), $key)), RuleNotFoundException::class);
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

    public function add(string $parent, string $key, string|array $value): ValidatorInterface
    {
        throw_if(!Arr::has($this->getRules(), $key), RuleNotFoundException::class);
        throw_if(empty($value), ValueCannotBeEmptyException::class);
        $this->rules[$parent] += [ $key => $value ];
        return $this;
    }

    public function ignore(string|array $value = []): ValidatorInterface
    {
        if(is_array($value))
        {
            $this->ignore = array_merge($this->ignore, $value);
        }
        else
        {
            $this->ignore[] = $value;
        }
        return $this;
    }

    public function errors(): MessageBag
    {
        return $this->errors;
    }

}
