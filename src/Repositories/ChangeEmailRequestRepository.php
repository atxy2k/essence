<?php namespace Atxy2k\Essence\Repositories;
use Atxy2k\Essence\Eloquent\ChangeEmailRequest;
use Atxy2k\Essence\Infrastructure\Repository;

/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 18:34
 */
class ChangeEmailRequestRepository extends Repository
{
    protected ?string $model = ChangeEmailRequest::class;

    public function findByEmail(string $email) : ?ChangeEmailRequest
    {
        return $this->query->where('before_email', $email)->first();
    }

}
