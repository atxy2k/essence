<?php namespace Atxy2k\Essence\Services;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 18:04
 */

use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\Exceptions\EmailRequests\EmailNotAvailableException;
use Atxy2k\Essence\Exceptions\Essence\InvalidParamsException;
use Atxy2k\Essence\Exceptions\Interactions\InteractionNotCreatedException;
use Atxy2k\Essence\Exceptions\Interactions\InteractionNotFoundException;
use Atxy2k\Essence\Exceptions\Roles\AdminRoleNotFound;
use Atxy2k\Essence\Exceptions\Roles\RoleNotFoundException;
use Atxy2k\Essence\Exceptions\Users\IncorrectPasswordException;
use Atxy2k\Essence\Exceptions\Users\UserAlreadyActiveException;
use Atxy2k\Essence\Exceptions\Users\UserAlreadyInRoleException;
use Atxy2k\Essence\Exceptions\Users\UserAlreadyIsAdminException;
use Atxy2k\Essence\Exceptions\Users\UserDoesNotAdminException;
use Atxy2k\Essence\Exceptions\Users\UserDoesntHaveRoleException;
use Atxy2k\Essence\Exceptions\Users\UserNotActiveException;
use Atxy2k\Essence\Exceptions\Users\UserNotCreatedException;
use Atxy2k\Essence\Exceptions\Users\UserNotFoundException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Interfaces\Services\UsersServiceInterface;
use Atxy2k\Essence\Repositories\InteractionsTypeRepository;
use Atxy2k\Essence\Repositories\RolesRepository;
use Atxy2k\Essence\Validators\UsersValidator;
use Atxy2k\Essence\Repositories\UsersRepository;
use Illuminate\Support\Facades\Hash;
use Throwable;
use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Exception;
use Auth;

class UsersService extends Service implements UsersServiceInterface
{
    /** @var UsersRepository */
    protected $usersRepository;
    /** @var RolesRepository */
    protected $rolesRepository;
    /** @var InteractionsTypeRepository */
    protected $interactionsTypeRepository;
    /** @var InteractionsService */
    protected $interactionsService;

    public function __construct(UsersValidator $usersValidator,
                                RolesRepository $rolesRepository,
                                InteractionsTypeRepository $interactionsTypeRepository,
                                UsersRepository $usersRepository, InteractionsService $interactionsService)
    {
        parent::__construct();
        $this->validator = $usersValidator;
        $this->usersRepository = $usersRepository;
        $this->rolesRepository = $rolesRepository;
        $this->interactionsTypeRepository = $interactionsTypeRepository;
        $this->interactionsService = $interactionsService;
    }

    function register(array $data): ?User
    {
        $return = null;
        try
        {
            DB::beginTransaction();
            throw_unless($this->validator->with($data)->passes('register'),
                new Exception($this->validator->errors()->first()));
            throw_unless($this->checkEmailAvailability($data['email']), EmailNotAvailableException::class);
            $activated = (bool) (int) Arr::get($data, 'activate', false);
            $set_password = (bool) (int) Arr::get($data, 'set_password', false);
            if(!$set_password)
            {
                $data['password'] = strtolower(Str::random(6));
            }
            $user_credentials = Arr::only($data, [
                'first_name' => Arr::get($data,'first_name'),
                'last_name'  => Arr::get($data,'last_name'),
                'email'      => Arr::get($data, 'email'),
                'password'   => Hash::make(trim(Arr::get($data,'password'))),
                'active'     => $activated,
                'activated_at' => $activated ? date('Y-m-d H:i:s') : null,
            ]);
            $user = $this->usersRepository->create($user_credentials);
            throw_if(is_null($user), UserNotCreatedException::class);
            $roles = Arr::get($data,'roles',[]);
            $user->roles()->sync($roles);

            $this->interactionsService->generate('create', $user);
            $return = $this->usersRepository->find($user->id);
            DB::commit();
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
        }
        return $return;
    }

    function sessionLogin(array $data): bool
    {
        // TODO: Implement sessionLogin() method.
    }

    function authenticate(array $data): bool
    {
        $authenticated = false;
        try
        {
            throw_unless($this->validator->with($data)->passes('authenticate'),
                new Exception($this->validator->errors()->first()));
            $user = $this->usersRepository->findByEmail($data['email']);
            throw_if(is_null($user), UserNotFoundException::class);
            $compare_password = Hash::make(trim($data['password']));
            throw_unless($user->password === $compare_password,
                IncorrectPasswordException::class);
            $authenticated = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $authenticated;
    }

    /***
     * Reset password by own user
     * @param int $user_id
     * @param array $data
     * @return bool
     */
    function resetPassword(int $user_id, array $data): bool
    {
        $completed = false;
        try
        {
            DB::beginTransaction();
            $user = $this->usersRepository->find($user_id);
            throw_if(is_null($user), UserNotFoundException::class);
            throw_unless($this->validator->with($data)->passes('reset-password'),
                new Exception($this->validator->errors()->first()));
            $old_password = Hash::make(Arr::get($data,'old_password'));
            throw_unless($old_password === $user->password,
                IncorrectPasswordException::class);
            $new_password = Hash::make(trim(Arr::get($data,'password')));
            $user->password = $new_password;
            $user->save();
            DB::commit();
            $completed = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
        }
        return $completed;
    }

    /**
     * Reset password from another user
     * @param int $id
     * @param array $data
     * @return bool
     */
    function changePassword(int $id, array $data): bool
    {
        $completed = false;
        try
        {
            DB::beginTransaction();
            $user = $this->usersRepository->find($id);
            throw_if(is_null($user), UserNotFoundException::class);
            throw_unless($this->validator->with($data)->passes('change-password'),
                new Exception($this->validator->errors()->first()));
            $new_password = Hash::make(trim(Arr::get($data,'password')));
            $user->password = $new_password;
            $user->save();
            DB::commit();
            $completed = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
        }
        return $completed;
    }

    function checkEmailAvailability(string $email, int $except_id = null): bool
    {
        return $this->usersRepository->findByEmail($email, $except_id) != null;
    }

    function requestEmailChange(int $user_id, array $data)
    {
        // TODO: Implement requestEmailChange() method.
    }

    function requestPasswordRecovery(array $data): bool
    {
        // TODO: Implement requestPasswordRecovery() method.
    }

    function validateRequestPasswordRecovery(array $data): bool
    {
        // TODO: Implement validateRequestPasswordRecovery() method.
    }

    function completeRequestPasswordRecovery(array $data): bool
    {
        // TODO: Implement completeRequestPasswordRecovery() method.
    }

    function activate(int $user_id): bool
    {
        $completed = false;
        try
        {
            DB::beginTransaction();
            $user = $this->usersRepository->find($user_id);
            throw_if(is_null($user), UserNotFoundException::class);
            throw_if($user->active, UserAlreadyActiveException::class);
            $user->active = true;
            $user->activated_at = date('Y-m-d H:i:s');
            $user->save();
            $completed = true;
            DB::commit();
        }
        catch (Throwable $e)
        {
            DB::rollback();
            $this->pushError($e->getMessage());
        }
        return $completed;
    }

    function deactivate(int $user_id): bool
    {
        $completed = false;
        try
        {
            DB::beginTransaction();
            $user = $this->usersRepository->find($user_id);
            throw_if(is_null($user), UserNotFoundException::class);
            throw_unless($user->active, UserNotActiveException::class);
            $user->active = false;
            $user->activated_at = null;
            $user->save();
            $completed = true;
            DB::commit();
        }
        catch (Throwable $e)
        {
            DB::rollback();
            $this->pushError($e->getMessage());
        }
        return $completed;
    }

    function update(int $user_id, array $data): bool
    {
        $completed = false;
        try
        {
            DB::beginTransaction();
            $user = $this->usersRepository->find($user_id);
            throw_if(is_null($user), UserNotFoundException::class);
            throw_unless($this->validator->with($data)->passes('update'),
                new Exception($this->validator->errors()->first()));
            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];
            $user->save();
            DB::commit();
            $completed = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
        }
        return $completed;
    }

    function delete(int $user_id) : bool
    {
        $completed = false;
        try
        {
            DB::beginTransaction();
            $user = $this->usersRepository->find($user_id);
            throw_if(is_null($user), UserNotFoundException::class);
            $user->delete();
            $completed = true;
            DB::commit();
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
        }
        return $completed;
    }

    function removeAdminPrivileges(int $id, array $data) : bool
    {
        $completed = false;
        try
        {
            DB::beginTransaction();
            $user = $this->usersRepository->find($id);
            throw_if(is_null($user), UserNotFoundException::class);
            throw_if(!$user->is_adin, UserDoesNotAdminException::class);
            $role = $this->rolesRepository->findBySlug(config('essence.admin_role_slug','developer'));
            throw_if(is_null($role), AdminRoleNotFound::class);
            $user->roles()->detach();
            $user->roles()->attach($role->id);
            DB::commit();
            $completed = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
        }
        return $completed;
    }

    function grantAdminPrivileges(int $id) : bool
    {
        $completed = false;
        try
        {
            DB::beginTransaction();
            $user = $this->usersRepository->find($id);
            throw_if($user->is_admin, UserAlreadyIsAdminException::class);
            throw_if(is_null($user), UserNotFoundException::class);
            $role = $this->rolesRepository->findBySlug(config('essence.admin_role_slug', 'developer'));
            throw_if(is_null($role), AdminRoleNotFound::class);
            $user->roles()->detach();
            $user->roles()->attach($role->id);
            DB::commit();
            $completed = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
        }
        return $completed;
    }

    function loginSessionWith(int $id): bool
    {
        // TODO: Implement loginWith() method.
    }

    function addRole(int $user_id, int $role_id): bool
    {
        $completed = false;
        try
        {
            $user = $this->usersRepository->find($user_id);
            $role = $this->rolesRepository->find($role_id);
            throw_if(is_null($user), UserNotFoundException::class);
            throw_if(is_null($role), RoleNotFoundException::class);
            throw_if(in_array($role->id, $user->roles()->pluck('roles.id')->all()),
                UserAlreadyInRoleException::class);
            $user->roles()->attach($role->id);
            $completed = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $completed;
    }

    function removeRole(int $user_id, int $role_id): bool
    {
        $completed = false;
        try
        {
            $user = $this->usersRepository->find($user_id);
            $role = $this->rolesRepository->find($role_id);
            throw_if(is_null($user), UserNotFoundException::class);
            throw_if(is_null($role), RoleNotFoundException::class);
            throw_if(!in_array($role->id, $user->roles()->pluck('roles.id')->all()),
                UserDoesntHaveRoleException::class);
            $user->roles()->detach($role->id);
            $completed = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $completed;
    }

    function updateRoles(int $user_id, array $roles = []): bool
    {
        $completed = false;
        try
        {
            $user = $this->usersRepository->find($user_id);
            throw_if(is_null($user), UserNotFoundException::class);
            $user->roles()->sync($roles);
            $completed = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $completed;
    }
}
