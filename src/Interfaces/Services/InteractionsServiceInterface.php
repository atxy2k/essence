<?php


namespace Atxy2k\Essence\Interfaces\Services;


use Atxy2k\Essence\Eloquent\Interaction;

interface InteractionsServiceInterface
{
    function create(array $data) : ?Interaction;
}