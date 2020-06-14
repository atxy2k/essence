<?php


namespace Atxy2k\Essence\Interfaces\Services;


use Atxy2k\Essence\Eloquent\Role;
use Illuminate\Database\Eloquent\Collection;

interface RolesServiceInterface
{
    function create(array $data) : ?Role;
    function update(int $id, array $data) : bool;
    function checkNameAvailability( string $name, int $except = null ) : bool;
    function delete(int $id) : bool;
    function addClaim(int $role_id,array $claims) : bool ;
    function removeClaim(int $role_id,int $claims) : bool ;
    function syncClaims(int $role_id, array $claims) : bool ;
    function getIdentifierClaims(int $role_id) : ?array;
}