<?php


namespace Atxy2k\Essence\Interfaces\Services;


use Atxy2k\Essence\Eloquent\User;

interface UsersServiceInterface
{
    function register(array $data) : ?User;
    function login(array $data) : bool;
    function authenticate(array $data) : bool;
    /***
     * Reset password by own user
     * @param int $user_id
     * @param array $data
     * @return bool
     */
    function resetPassword(int $user_id, array $data) : bool;

    /**
     * Reset password from another user
     * @param int $id
     * @param array $data
     * @return bool
     */
    function changePassword(int $id, array  $data) : bool ;
    function checkEmailAvailability(string $email, int $except_id = null) : bool;
    function requestPasswordRecovery(array $data) : string ;
    function validateRequestPasswordRecovery(string $data) : bool ;
    function activate(int $user_id) : bool ;
    function deactivate(int $user_id) : bool ;
    function update(int $user_id, array $data) : bool ;
    function delete(int $user_id) : bool ;
    function removeAdminPrivileges(int $id, array $data) : bool ;
    function grantAdminPrivileges(int $id) : bool ;
    function loginWith(int $id) : bool ;
    function addRole(int $user_id, int $role_id) : bool;
    function removeRole(int $user_id, int $role_id) : bool;
    function syncRoles(int $user_id, array $roles = []) : bool ;

    function addClaim(int $user_id,array $claims) : bool ;
    function removeClaim(int $user_id,array $claims) : bool ;
    function syncClaims(int $user_id, array $claims) : bool ;

}