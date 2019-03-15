<?php namespace Atxy2k\Essence\Repositories;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 17:40
 */
use Atxy2k\Essence\Eloquent\State;
use Atxy2k\Essence\Infraestructure\Repository;
use Illuminate\Support\Str;

class StatesRepository extends Repository
{
    protected $model = State::class;

    public function findBySlug(int $country_id, string $slug, int $id = null ) : ?State
    {
        return $id !== null ?
            $this->query
                ->where('country_id', $country_id)
                ->where('slug', $slug)
                ->where('id','!=', $id)
                ->first() :
            $this->query
                ->where('country_id', $country_id)
                ->where('slug', $slug)
                ->first();
    }

    public function getByCountry(int $country_id) : Collection
    {
        return $this->query
            ->where('country_id', $country_id)
            ->orderBy('name','asc')->get();
    }

    public function slugIsAvailable(int $country_id, string $slug, int $id = null ) : bool
    {
        return $this->findBySlug($country_id,$slug, $id) === null;
    }

    public function slugFromTextIsAvailable($country_id, $text, $id = null ) : bool
    {
        $slug = Str::slug( $text );
        return $this->findBySlug($country_id, $slug, $id) === null;
    }

}
