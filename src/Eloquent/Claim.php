<?php


namespace Atxy2k\Essence\Eloquent;


use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $table = 'claims';
    protected $fillable = ['identifier','name','description','enabled'];
    protected $guarded = ['id'];
    protected $casts = [
        'enabled' => 'boolean'
    ];
}