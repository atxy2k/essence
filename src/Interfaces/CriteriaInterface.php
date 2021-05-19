<?php namespace Atxy2k\Essence\Interfaces;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 15:39
 */
use Atxy2k\Essence\Infrastructure\Criteria;
use Atxy2k\Essence\Infrastructure\Repository;

interface CriteriaInterface
{
    /**
     * Add a Criteria object to criteria to going to apply later.
     * @param Criteria $criteria
     * @return Repository
     */
    public function pushCriteria(Criteria $criteria) : Repository;
    /**
     * Add a Criteria object to criteria to going to apply later.
     * @param Criteria $criteria
     * @return Repository
     */
    public function addCriteria(Criteria $criteria) : Repository;

    /**
     * Return all criteria added
     * @return array
     */
    public function getCriteria() : array;

    /**
     * Clean all criteria
     * @return Repository
     */
    public function cleanCriteria() : Repository;

}
