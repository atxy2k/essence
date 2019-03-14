<?php namespace Atxy2k\Essence\Repositories;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 17:41
 */
use Atxy2k\Essence\Eloquent\Municipality;
use Atxy2k\Essence\Infraestructure\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MunicipalitiesRepository extends Repository
{
    protected $model = Municipality::class;

    public function findBySlug($state_id, $slug, int $id = null ) : ?Municipality
    {
        return $id !== null ?
            $this->query
                ->where('state_id', $state_id)
                ->where('slug', $slug)
                ->where('id','!=', $id)
                ->first() :
            $this->query
                ->where('state_id', $state_id)
                ->where('slug', $slug)
                ->first();
    }

    public function getByState($state_id) : Collection
    {
        return $this->query
            ->where('state_id', $state_id)
            ->orderBy('name','asc')->get();
    }

    public function slugIsAvailable($state_id, $slug, int $id = null ) : bool
    {
        return $this->findBySlug($state_id,$slug, $id) === null;
    }

    public function slugFromTextIsAvailable($state_id, $text, $id = null ) : bool
    {
        $slug = Str::slug( $text );
        return $this->findBySlug($state_id, $slug, $id) === null;
    }

}
