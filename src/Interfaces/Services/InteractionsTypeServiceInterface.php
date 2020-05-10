<?php


namespace Atxy2k\Essence\Interfaces\Services;


use Atxy2k\Essence\Eloquent\InteractionType;

interface InteractionsTypeServiceInterface
{
    function create(array $data) : ?InteractionType;
}