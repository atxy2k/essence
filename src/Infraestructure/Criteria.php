<?php namespace Atxy2k\Essence\Infraestructure;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 15:42
 */
use Illuminate\Database\Eloquent\Model;

abstract class Criteria
{
    public abstract function apply(Model $model, Repository $repository);
}
