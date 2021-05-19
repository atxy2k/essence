<?php


namespace Atxy2k\Essence\Tests\Criteria;


use Atxy2k\Essence\Infrastructure\Criteria;
use Atxy2k\Essence\Infrastructure\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BlockedCriteria extends Criteria
{

    public function apply($builder, Repository $repository) : Builder
    {
        return $builder->where('blocked', true);
    }
}