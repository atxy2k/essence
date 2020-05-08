<?php namespace Atxy2k\Essence\Repositories;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 18:02
 */

use Atxy2k\Essence\Eloquent\Configuration;
use Atxy2k\Essence\Exceptions\Essence\UnexpectedException;
use Atxy2k\Essence\Infraestructure\Repository;
use Throwable;

class SettingsRepository extends Repository
{

    protected $model = Configuration::class;



}
