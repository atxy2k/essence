<?php namespace Atxy2k\Essence\Repositories;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 17:39
 */
use Atxy2k\Essence\Eloquent\Country;
use Atxy2k\Essence\Infraestructure\Repository;
use Atxy2k\Essence\Traits\SluggableTrait;

class CountriesRepository extends Repository
{
    protected $model = Country::class;
    use SluggableTrait;
}
