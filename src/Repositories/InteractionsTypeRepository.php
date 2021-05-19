<?php


namespace Atxy2k\Essence\Repositories;


use Atxy2k\Essence\Eloquent\InteractionType;
use Atxy2k\Essence\Infraestructure\Repository;
use Atxy2k\Essence\Traits\Sluggable;

class InteractionsTypeRepository extends Repository
{
    protected ?string $model = InteractionType::class;
    use Sluggable;
}