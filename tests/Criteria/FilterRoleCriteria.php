<?php namespace Atxy2k\Essence\Tests\Criteria;

use Atxy2k\Essence\Infrastructure\Criteria;
use Atxy2k\Essence\Infrastructure\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FilterRoleCriteria extends Criteria
{

    protected $query;

    public function __construct(string $query)
    {
        $this->query = strtolower(trim($query));
    }

    public function apply($builder, Repository $repository) : Builder
    {
        $p = $this->query;
        return $builder->where('slug', 'like', "%$p%");
    }

}