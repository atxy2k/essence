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

    /**
     * Check email availability
     * @param string $email
     * @param int|null $except_id
     * @return bool
     */
    function checkEmailAvailability(string $email, int $except_id = null) : bool;

    /**
     * Request password recovery token
     * @param array $data
     * @return string|null
     */
    function requestPasswordRecovery(array $data) : ?string ;

    /**
     * Validate recovery token
     * @param string $data
     * @return bool
     */
    function validateRequestPasswordRecovery(string $data) : bool ;

    /**
     * Activate user
     * @param int $user_id
     * @return bool
     */
    function activate(int $user_id) : bool ;

    /**
     * Deactivate user
     * @param int $user_id
     * @return bool
     */
    function deactivate(int $user_id) : bool ;

    /**
     * Update user, this only update first and last name.
     * @param int $user_id
     * @param array $data
     * @return bool
     */
    function update(int $user_id, array $data) : bool ;

    /**
     * Delete user
     * @param int $user_id
     * @return bool
     */
    function delete(int $user_id) : bool ;

    /**
     * Removing admin privileges
     * @param int $id
     * @param array $data
     * @return bool
     */
    function removeAdminPrivileges(int $id, array $data) : bool ;

    /**
     * Grant admin privileges
     * @param int $id
     * @return bool
     */
    function grantAdminPrivileges(int $id) : bool ;

    /**
     * Login with another user
     * @param int $id
     * @return bool
     */
    function loginWith(int $id) : bool ;

    /**
     * Add role to user except admin user
     * @param int $user_id
     * @param int $role_id
     * @return bool
     */
    function addRole(int $user_id, int $role_id) : bool;

    /**
     * Remove role from user
     * @param int $user_id
     * @param int $role_id
     * @return bool
     */
    function removeRole(int $user_id, int $role_id) : bool;

    /**
     * Use this function only if you know you are doing
     * @param int $user_id
     * @param array $roles
     * @return bool
     */
    function syncRoles(int $user_id, array $roles = []) : bool ;

    /**
     * Add claims to user
     * @param int $user_id
     * @param array $claims
     * @return bool
     */
    function addClaim(int $user_id,array $claims) : bool ;

    /**
     * Remove claims from user
     * @param int $user_id
     * @param array $claims
     * @return bool
     */
    function removeClaim(int $user_id,array $claims) : bool ;
    function syncClaims(int $user_id, array $claims) : bool ;

}