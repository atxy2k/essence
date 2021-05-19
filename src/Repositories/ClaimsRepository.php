<?php


namespace Atxy2k\Essence\Repositories;


use Atxy2k\Essence\Eloquent\Claim;
use Atxy2k\Essence\Infraestructure\Repository;
use Atxy2k\Essence\Interfaces\RepositoryInterface;
use Composer\Repository\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClaimsRepository extends Repository implements RepositoryInterface
{
    protected ?string $model = Claim::class;

    public function findByIdentifier(string $identifier, $id = null ) : ?Model
    {
        return !is_null($id) ?
            $this->query
                ->where('identifier', $identifier)
                ->where('id','!=', $id)->first() :
            $this->query
                ->where('identifier', $identifier)->first();
    }

    public function identifierIsAvailable(string $identifier, $id = null ) : bool
    {
        return is_null($this->findByIdentifier($identifier, $id));
    }

    public function identifierFromTextIsAvailable(string $text, $id = null ) : bool
    {
        $identifier = strtolower( str_replace(' ','.', trim($text)) );
        return is_null($this->findByIdentifier($identifier, $id));
    }

}