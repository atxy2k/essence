<?php namespace Atxy2k\Essence\Repositories;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 17:49
 */

use Atxy2k\Essence\Eloquent\Municipality;
use Atxy2k\Essence\Eloquent\Suburb;
use Atxy2k\Essence\Infraestructure\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SuburbsRepository extends Repository
{
    protected $model = Suburb::class;

    public function getByMunicipality(int $municipality_id) : Collection
    {
        return $this->query
            ->where('municipality_id', $municipality_id)
            ->orderBy('name','asc')
            ->get();
    }

    public function findBySlug(int $municipality_id, string $slug, int $id = null ) : ?Suburb
    {
        return $id !== null ?
            $this->query
                ->where('municipality_id', $municipality_id)
                ->where('slug', $slug)
                ->where('id','!=', $id)
                ->first() :
            $this->query
                ->where('municipality_id', $municipality_id)
                ->where('slug', $slug)
                ->first();
    }

    public function findByPostalCode(int $postal_code) : Collection
    {
        return $this->query
            ->where('postal_code', $postal_code)->get();
    }

    public function slugIsAvailable(int $municipality_id, string $slug, int $id = null ) : bool
    {
        return $this->findBySlug($municipality_id,$slug, $id) === null;
    }

    public function slugFromTextIsAvailable(int $municipality_id, string $text, int $id = null ) : bool
    {
        $slug = Str::slug( $text );
        return $this->findBySlug($municipality_id, $slug, $id) === null;
    }

}
