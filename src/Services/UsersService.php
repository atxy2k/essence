<?php namespace Atxy2k\Essence\Services;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 18:04
 */

use Atxy2k\Essence\Constants\Interactions;
use Atxy2k\Essence\Eloquent\Interaction;
use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\Exceptions\Claims\ClaimNotFoundException;
use Atxy2k\Essence\Exceptions\EmailRequests\EmailNotAvailableException;
use Atxy2k\Essence\Exceptions\Essence\InvalidParamsException;
use Atxy2k\Essence\Exceptions\Essence\ValidationException;
use Atxy2k\Essence\Exceptions\Interactions\InteractionNotCreatedException;
use Atxy2k\Essence\Exceptions\Interactions\InteractionNotFoundException;
use Atxy2k\Essence\Exceptions\Roles\AdminRoleNotFound;
use Atxy2k\Essence\Exceptions\Roles\IntegerOrStringRequiredException;
use Atxy2k\Essence\Exceptions\Roles\RoleNotFoundException;
use Atxy2k\Essence\Exceptions\Users\InconsistentTokenException;
use Atxy2k\Essence\Exceptions\Users\IncorrectPasswordException;
use Atxy2k\Essence\Exceptions\Users\TokenExpiredException;
use Atxy2k\Essence\Exceptions\Users\UserAlreadyActiveException;
use Atxy2k\Essence\Exceptions\Users\UserAlreadyHaveClaimException;
use Atxy2k\Essence\Exceptions\Users\UserAlreadyInRoleException;
use Atxy2k\Essence\Exceptions\Users\UserAlreadyIsAdminException;
use Atxy2k\Essence\Exceptions\Users\UserDoesNotAdminException;
use Atxy2k\Essence\Exceptions\Users\UserDoesNotHaveClaimException;
use Atxy2k\Essence\Exceptions\Users\UserDoesntHaveRoleException;
use Atxy2k\Essence\Exceptions\Users\UserNotActiveException;
use Atxy2k\Essence\Exceptions\Users\UserNotCreatedException;
use Atxy2k\Essence\Exceptions\Users\UserNotFoundException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Interfaces\Services\UsersServiceInterface;
use Atxy2k\Essence\Repositories\ClaimsRepository;
use Atxy2k\Essence\Repositories\InteractionsTypeRepository;
use Atxy2k\Essence\Repositories\RolesRepository;
use Atxy2k\Essence\Validators\UsersValidator;
use Atxy2k\Essence\Repositories\UsersRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Throwable;
use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Exception;
use Auth;
use Essence;

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
    /** @var ClaimsRepository */
    protected $claimsRepository;

    public function __construct(UsersValidator $usersValidator,
                                RolesRepository $rolesRepository,
                                InteractionsTypeRepository $interactionsTypeRepository,
                                ClaimsRepository $claimsRepository,
                                UsersRepository $usersRepository, InteractionsService $interactionsService)
    {
        parent::__construct();
        $this->validator = $usersValidator;
        $this->usersRepository = $usersRepository;
        $this->rolesRepository = $rolesRepository;
        $this->interactionsTypeRepository = $interactionsTypeRepository;
        $this->interactionsService = $interactionsService;
        $this->claimsRepository = $claimsRepository;
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
            $user_credentials = Arr::only($data, [
                'first_name' => Arr::get($data,'first_name'),
                'last_name'  => Arr::get($data,'last_name'),
                'email'      => strtolower(trim( Arr::get($data, 'email'))),
                'password'   => Hash::make(trim(Arr::get($data,'password'))),
                'active'     => $activated,
                'activated_at' => $activated ? date('Y-m-d H:i:s') : null,
            ]);
            $user = $this->usersRepository->create($user_credentials);
            throw_if(is_null($user), UserNotCreatedException::class);
            $roles = Arr::get($data,'roles',[]);
            $user->roles()->sync($roles);

            $interaction = $this->interactionsService->generate(Interactions::CREATE, $user);
            throw_unless($interaction instanceof Interaction, InteractionNotCreatedException::class);
            $return = $this->usersRepository->find($user->id);
            DB::commit();
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
            Essence::log($e);
        }
        return $return;
    }

    function login(array $data): bool
    {
        $return = false;
        try
        {
            throw_unless($this->validator->with($data)->passes('authenticate'),
            new ValidationException($this->validator->errors()->first()));
            $email = strtolower(trim($data['email']));
            $user = $this->usersRepository->findByEmail($email);
            throw_if(is_null($user), UserNotFoundException::class);
            $compare_password = Hash::make(trim($data['password']));
            throw_unless($user->password === $compare_password,
                IncorrectPasswordException::class);
            throw_unless($user->is_active, UserNotActiveException::class);
            $interaction = $this->interactionsService->generate(Interactions::LOGIN, $user);
            throw_unless($interaction instanceof Interaction, InteractionNotCreatedException::class);
            if(Auth::attempt([ 'email' => $email, 'password' => $data['password'], 'is_activated' => true ]))
            {
               $return = true;
            }
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
            Essence::log($e);
        }
        return $return;
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
            $interaction = $this->interactionsService->generate(Interactions::AUTHENTICATE, $user);
            throw_unless($interaction instanceof Interaction, InteractionNotCreatedException::class);
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
            $interaction = $this->interactionsService->generate(Interactions::UPDATE, $user);
            throw_if(is_null($interaction), InteractionNotCreatedException::class);
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
            $interaction = $this->interactionsService->generate(Interactions::ACTIVATE, $user);
            throw_if(is_null($interaction), InteractionNotCreatedException::class);
            DB::commit();
            $completed = true;
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
            $interaction = $this->interactionsService->generate(Interactions::DEACTIVATE, $user);
            throw_if(is_null($interaction), InteractionNotCreatedException::class);
            DB::commit();
            $completed = true;
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
            Essence::log($e);
        }
        return $completed;
    }

    function loginWith(int $id): bool
    {
        $return = false;
        try
        {
            $user = $this->usersRepository->find($id);
            throw_if(is_null($user), UserNotFoundException::class);
            Auth::login($user);
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            Essence::log($e);
        }
        return $return;
    }

    function addRole(int $user_id, int $role_id): bool
    {
        $completed = false;
        try
        {
            DB::beginTransaction();
            $user = $this->usersRepository->find($user_id);
            $role = $this->rolesRepository->find($role_id);
            throw_if(is_null($user), UserNotFoundException::class);
            throw_if(is_null($role), RoleNotFoundException::class);
            throw_if(in_array($role->id, $user->roles()->pluck('roles.id')->all()),
                UserAlreadyInRoleException::class);
            $user->roles()->attach($role->id);
            DB::commit();
            $completed = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
            Essence::log($e);
        }
        return $completed;
    }

    function removeRole(int $user_id, int $role_id): bool
    {
        $completed = false;
        try
        {
            DB::beginTransaction();
            $user = $this->usersRepository->find($user_id);
            $role = $this->rolesRepository->find($role_id);
            throw_if(is_null($user), UserNotFoundException::class);
            throw_if(is_null($role), RoleNotFoundException::class);
            throw_if(!in_array($role->id, $user->roles()->pluck('roles.id')->all()),
                UserDoesntHaveRoleException::class);
            $user->roles()->detach($role->id);
            DB::commit();
            $completed = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
            Essence::log($e);
        }
        return $completed;
    }

    function syncRoles(int $user_id, array $roles = []): bool
    {
        $completed = false;
        try
        {
            DB::beginTransaction();
            $user = $this->usersRepository->find($user_id);
            throw_if(is_null($user), UserNotFoundException::class);
            $user->roles()->sync($roles);
            $completed = true;
            DB::commit();
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
            Essence::log($e);
        }
        return $completed;
    }

    function addClaim(int $user_id, array $claims): bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $user = $this->usersRepository->find($user_id);
            throw_if(is_null($user), UserNotFoundException::class);
            foreach ($claims as $_claim)
            {
                throw_unless( is_integer($_claim) || is_string($_claim),
                    IntegerOrStringRequiredException::class);
                $claim = null;
                if(is_integer($_claim))
                {
                    $claim = $this->claimsRepository->find($_claim);
                }
                else
                {
                    $claim = $this->claimsRepository->findByIdentifier($_claim);
                }
                throw_if(is_null($claim), ClaimNotFoundException::class);
                throw_if($user->claims->contains($claim), UserAlreadyHaveClaimException::class);
                $user->claims()->attach($claim->id);
            }
            DB::commit();
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
            Essence::log($e);
        }
        return $return;
    }

    function removeClaim(int $user_id, array $claims): bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $user = $this->usersRepository->find($user_id);
            throw_if(is_null($user), UserNotFoundException::class);
            foreach ($claims as $_claim)
            {
                throw_unless( is_integer($_claim) || is_string($_claim),
                    IntegerOrStringRequiredException::class);
                $claim = null;
                if(is_integer($_claim))
                {
                    $claim = $this->claimsRepository->find($_claim);
                }
                else
                {
                    $claim = $this->claimsRepository->findByIdentifier($_claim);
                }
                throw_if(is_null($claim), ClaimNotFoundException::class);
                throw_unless($user->claims->contains($claim), UserDoesNotHaveClaimException::class);
                $user->claims()->detach($claim->id);
            }
            DB::commit();
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
            Essence::log($e);
        }
        return $return;
    }

    function syncClaims(int $user_id, array $claims): bool
    {
        $return = false;
        try
        {
            DB::beginTransaction();
            $user = $this->usersRepository->find($user_id);
            throw_if(is_null($user), UserNotFoundException::class);
            $user->claims()->sync($claims);
            DB::commit();
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            DB::rollback();
            Essence::log($e);
        }
        return $return;
    }

    function requestPasswordRecovery(array $data): string
    {
        $return = null;
        try
        {
            throw_unless($this->validator->with($data)->passes('request-password-recovery'),
                new ValidationException($this->validator->errors()->first()));
            $user = $this->usersRepository->findByEmail($data['email']);
            throw_if(is_null($user), UserNotFoundException::class);
            $data = [
                'email' => $data['email'],
                'date'  => date('Y-m-d H:i:s')
            ];
            $data_encoded = json_encode($data);
            $token = encrypt($data_encoded);
            $return = $token;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            Essence::log($e);
        }
        return $return;
    }

    function validateRequestPasswordRecovery(string $data): bool
    {
        $return = null;
        try
        {
            $encoded_data = decrypt($data);
            $data = json_decode($encoded_data, true);
            throw_unless($this->validator->with($data)->passes('encoded-password-recovery-token'),
                InconsistentTokenException::class);
            /** @var Carbon $date */
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $data['date']);
            /** @var Carbon $now */
            $now = now();
            $timeout = (int) config('essence.password_recovery_timeout', 30);
            throw_if( $now->diffInMinutes($date) > $timeout,
                TokenExpiredException::class );
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
            Essence::log($e);
        }
        return $return;
    }
}
