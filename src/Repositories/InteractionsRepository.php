<?php


namespace Atxy2k\Essence\Repositories;


use Atxy2k\Essence\Eloquent\Interaction;
use Atxy2k\Essence\Infraestructure\Repository;

class InteractionsRepository extends Repository
{
    protected ?string $model = Interaction::class;
}