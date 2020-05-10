<?php


namespace Atxy2k\Essence\Eloquent;


use Atxy2k\Essence\Infraestructure\Model;

class Interaction extends Model
{
    protected $table = 'interactions';
    protected $fillable = ['interaction_id', 'interactuable_id','interactuable_type'];
    protected $guarded = ['id'];
}