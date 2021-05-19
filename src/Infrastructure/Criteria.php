<?php namespace Atxy2k\Essence\Infrastructure;
use Illuminate\Database\Eloquent\Builder;

/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 15:42
 */


abstract class Criteria
{
    public abstract function apply($builder, Repository $repository) : Builder;
}
