<?php


namespace Atxy2k\Essence\Interfaces\Repositories;


use Atxy2k\Essence\Eloquent\Application;
use Atxy2k\Essence\Interfaces\RepositoryInterface;

interface ApplicationRepositoryInterface extends RepositoryInterface
{
    public function findByAppId(string $appId) : ?Application;
    public function findByAppIdAndAppSecret(string $appId, string $appSecret) : ?Application;
    public function isEnabledByAppId(string $appId) : bool ;
    public function isEnabled(int $appId) : bool ;
    public function enable(int $appId) : bool ;
    public function enableFromApplicationId(string $application_id) : bool ;
    public function disable(int $appId) : bool ;
    public function disableFromApplicationId(string $application_id) : bool ;

}