<?php namespace Atxy2k\Essence\Contracts;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 15:39
 */
use Atxy2k\Essence\Infrastructure\Criteria;
use Atxy2k\Essence\Infrastructure\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface CriteriaInterface
{
    public function pushCriteria(Criteria $criteria) : RepositoryInterface;
    public function addCriteria(Criteria $criteria) : RepositoryInterface;
    public function getCriteria() : array;
    public function cleanCriteria() : RepositoryInterface;

}
