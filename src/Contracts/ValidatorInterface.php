<?php namespace Atxy2k\Essence\Contracts;
use Illuminate\Support\MessageBag;

/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 12:20
 */
interface ValidatorInterface
{
    public function with(array $data = []) : ValidatorInterface;
    public function getRules( string $key = null ) : array;
    public function passes(string $key = 'create') : bool;
    public function fails(string $key = 'create') : bool;
    public function add( string $parent, string $key, array|string $value ) : ValidatorInterface;
    public function ignore(string|array $value) : ValidatorInterface;
    public function errors() : MessageBag;

}
