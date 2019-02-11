<?php namespace Atxy2k\Essence\Interfaces;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 15:39
 */
use Atxy2k\Essence\Infraestructure\Criteria;
use Atxy2k\Essence\Infraestructure\Repository;

interface CriteriaInterface
{
    public function pushCriteria(Criteria $criteria) : Repository;
    public function addCriteria(Criteria $criteria) : Repository;
    public function applyCriteria() : ?Repository;
}
