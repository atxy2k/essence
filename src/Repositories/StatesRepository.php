<?php namespace Atxy2k\Essence\Repositories;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 17:40
 */
use Atxy2k\Essence\Eloquent\State;
use Atxy2k\Essence\Infraestructure\Repository;
use Atxy2k\Essence\Traits\SluggableTrait;

class StatesRepository extends Repository
{
    protected $model = State::class;
    use SluggableTrait;
}
