<?php namespace Atxy2k\Essence\Interfaces;
use Illuminate\Support\MessageBag;

/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 12:20
 */
interface ValidatorInterface
{
    /**
     * @param array $data
     * @return ValidatorInterface
     */
    public function with(array $data = []) : ValidatorInterface;

    /**
     * @param bool $key
     * @return array
     */
    public function getRules( $key = false ) : array;

    /**
     * @param string $key
     * @return bool
     */
    public function passes($key = 'create') : bool;

    /**
     * @param string $key
     * @return bool
     */
    public function fails($key = 'create') : bool;

    /**
     * @param string $parent
     * @param bool $key
     * @param bool $value
     * @return ValidatorInterface
     */
    public function add( $parent = 'create', $key = false, $value = false ) : ValidatorInterface;

    /**
     * @param array $key
     * @return ValidatorInterface
     */
    public function ignore( array $key = []) : ValidatorInterface;

    /**
     * @return MessageBag
     */
    public function errors() : MessageBag;

}
