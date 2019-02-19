<?php namespace Atxy2k\Essence\Traits;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 17:04
 */
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

Trait SluggableTrait
{
    public function findBySlug( $slug, $id = null ) : ?Model
    {
        return !is_null($id) ? $this->query->where('slug', $slug)->where('id','!=', $id)->first() : $this->query->where('slug', $slug)->first();
    }

    public function slugIsAvailable( $slug, $id = null ) : bool
    {
        return is_null($this->findBySlug($slug, $id));
    }

    public function slugFromTextIsAvailable( $text, $id = null ) : bool
    {
        $slug = Str::slug( $text );
        return is_null($this->findBySlug($slug, $id));
    }
}
