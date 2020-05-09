<?php


namespace Atxy2k\Essence\Interfaces\Services;


use Atxy2k\Essence\Eloquent\Role;

interface RolesServiceInterface
{
    function create(array $data) : ?Role;
    function update(int $id, array $data) : ?Role;
    function checkNameAvailability( string $name, int $except = null ) : bool;
    function delete(int $id) : bool;
}