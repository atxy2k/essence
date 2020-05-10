<?php


namespace Atxy2k\Essence\Eloquent;


use Atxy2k\Essence\Infraestructure\Model;

class Interaction extends Model
{
    protected $table = 'interactions';
    protected $fillable = ['interaction_id', 'interactuable_id','interactuable_type','user_id'];
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function interaction_type()
    {
        return $this->belongsTo(InteractionType::class, 'interaction_id');
    }

}