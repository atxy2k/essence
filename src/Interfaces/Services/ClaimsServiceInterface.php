<?php


namespace Atxy2k\Essence\Interfaces\Services;


use Atxy2k\Essence\Eloquent\Claim;

interface ClaimsServiceInterface
{

    function create(array $data) : ?Claim;
    function update(int $id, array $data) : bool;
    function delete(int $id) : bool;
    function isIdentifierAvailability(string $name, int $except_id = null) : bool ;
    function enabled(int $id) : bool ;
    function disabled(int $id) : bool ;

}