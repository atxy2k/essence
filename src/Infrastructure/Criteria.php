<?php namespace Atxy2k\Essence\Infrastructure;

use Atxy2k\Essence\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 15:42
 */


abstract class Criteria
{
    public abstract function apply(Model|Builder $builder, RepositoryInterface $repository) : Builder;
}
