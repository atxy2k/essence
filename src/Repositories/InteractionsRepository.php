<?php


namespace Atxy2k\Essence\Repositories;


use Atxy2k\Essence\Eloquent\Interaction;
use Atxy2k\Essence\Infraestructure\Repository;
use Atxy2k\Essence\Traits\Sluggable;

class InteractionsRepository extends Repository
{
    protected $model = Interaction::class;
    use Sluggable;
}