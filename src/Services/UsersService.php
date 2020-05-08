<?php namespace Atxy2k\Essence\Services;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 11/2/2019
 * Time: 18:04
 */

use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\Exceptions\EmailRequests\IdenticalEmailsException;
use Atxy2k\Essence\Exceptions\EmailRequests\InvalidTokenException;
use Atxy2k\Essence\Exceptions\Essence\InvalidParamsException;
use Atxy2k\Essence\Exceptions\Essence\UnexpectedException;
use Atxy2k\Essence\Exceptions\Roles\AdminRoleNotFound;
use Atxy2k\Essence\Exceptions\Users\DeleteMyselfException;
use Atxy2k\Essence\Exceptions\Users\DoesntHaveRolesException;
use Atxy2k\Essence\Exceptions\Users\IncorrectPasswordException;
use Atxy2k\Essence\Exceptions\Users\UserAlreadyActiveException;
use Atxy2k\Essence\Exceptions\Users\UserAlreadyIsAdminException;
use Atxy2k\Essence\Exceptions\Users\UserDoesNotAdminException;
use Atxy2k\Essence\Exceptions\Users\UserNotActiveException;
use Atxy2k\Essence\Exceptions\Users\UserNotCreatedException;
use Atxy2k\Essence\Exceptions\Users\UserNotFoundException;
use Atxy2k\Essence\Exceptions\Users\UserNotUpdatedException;
use Atxy2k\Essence\Infraestructure\Service;
use Atxy2k\Essence\Validators\UsersValidator;
use Atxy2k\Essence\Repositories\UsersRepository;
use Cartalyst\Sentinel\Users\EloquentUser;
use Sentinel;
use Throwable;
use Reminder;
use Illuminate\Validation\Rule;
use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class UsersService extends Service
{
    /** @var UsersRepository */
    protected $usersRepository;
    /** @var ChangeEmailRequestService */
    protected $changeEmailRequestService;

    public function __construct(UsersValidator $usersValidator,
                                UsersRepository $usersRepository, ChangeEmailRequestService $changeEmailRequestService)
    {
        parent::__construct();
        $this->validator = $usersValidator;
        $this->usersRepository = $usersRepository;
        $this->changeEmailRequestService = $changeEmailRequestService;
    }


    /**
     * Reset password of user
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function resetPassword( int $id,array $data ) : bool
    {
        $return = false;
        $user = $this->usersRepository->find($id);
        try {
            throw_if(is_null($user), UserNotFoundException::class);
            if ( $this->validator->with($data)->passes('reset_password') )
            {
                $this->usersRepository->update($user->id, [ 'password' => $data['password'] ]);
                $return = true;
            }
        } catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    /**
     * Change user password
     * //TODO test, Sentinel::stateless doesn't work me last time
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function changePassword(int $id, array $data ) : bool
    {
        $return = false;
        $user = Sentinel::getUserRepository()->findById($id);
        try {
            throw_if(is_null($user), UserNotFoundException::class);
            if ( $this->validator->with($data)->passes('changePassword') )
            {
                $before_password = $data['before_password'];
                $password = $data['password'];
                $credentiales = [
                    'email' => $user->email,
                    'password' => $before_password
                ];
                if ( $user = Sentinel::stateless($credentiales) )
                {
                    Sentinel::update($user, compact('password'));
                    $return = true;
                }
                else
                {
                    $this->errors->add('error', __('La contraseña anterior no es válida'));
                }
            }
        } catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    /**
     * Change email availability
     * @param string $email
     * @param string|null $except
     * @return bool
     */
    public function checkEmailAvailability( string $email, string $except = null ) : bool
    {
        $return = true;
        if ( !is_null($except) )
        {
            $user = $this->usersRepository->model()->where('email', $email)->where('id', '!=', $except)->first();
        }
        else
        {
            $user = $this->usersRepository->model()->where('email', $email)->first();
        }
        if ( !is_null($user) )
        {
            $return = false;
        }
        return $return;
    }

    /**
     * Create a request for change email
     * @param int $user_id
     * @param array $data
     * @return bool
     */
    public function requestEmailChanged(int $user_id, array $data ) : bool
    {
        $return = false;
        $user = $this->usersRepository->find($user_id);
        try {
            throw_if(is_null($user), UserNotFoundException::class);
            if ( $this->validator->with($data)->passes('changeEmail') )
            {
                if ( $user->changeEmailRequests->count() > 0 )
                {
                    foreach ( $user->changeEmailRequests as $req )
                    {
                        $req->delete();
                    }
                    $this->errors->add('warning', __('Se han borrado las solicitudes de cambio anteriores.'));
                }
                $request_data = [
                    'user_id'       => $user->id,
                    'before_email'  => $user->email,
                    'next_email'    => $data['email']
                ];
                throw_unless($user->email != $request_data['next_email'], IdenticalEmailsException::class);
                $request = $this->changeEmailRequestService->create( $request_data );
                if ( $request )
                {
                    //$user->notify( new RequestChangeEmail( $request ) );
                    //Mail::to( $request->next_email )->send( new EmailConfirmationChanged( $user, $request ) );
                    $return = true;
                }
                else
                {
                    $this->pushErrors( $this->changeEmailRequestService->errors() );
                }
            }
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    /**
     * //TODO check this function, we have a problem with sentinel's environment in unit tests.
     * Create a reminder, practically begin process to restart his password.
     * @param string $email
     * @return bool
     */
    public function createReminder(string $email ) : bool
    {
        $return = false;
        $user = Sentinel::getUserRepository()->findByCredentials([ 'email' => $email ]);
        try {
            throw_if(is_null($user), UserNotFoundException::class);
            ($reminder = Sentinel::getReminderRepository()->exists($user)) || ($reminder = Sentinel::getReminderRepository()->create($user) );
            //$user->notify( new ForgotPassword($reminder) );
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    /**
     * //TODO check tests for this function, depends of above function.
     * Check if until can change his password
     * @param array $data
     * @return bool
     */
    public function reminderCanChangePassword(array $data) : bool
    {
        $return = false;
        if ( $this->validator->with($data)->passes('validate_reminder') )
        {
            try {
                $user = Sentinel::getUserRepository()->findByCredentials([ 'email' => $data['email'] ]);
                throw_if(is_null($user), UserNotFoundException::class);
                throw_unless(Reminder::exists($user, $data['code']), InvalidTokenException::class);
                $return = true;
            } catch (Throwable $e)
            {
                $this->pushError($e->getMessage());
            }
        }
        return $return;
    }

    /**
     * //TODO check unit tests for this function, it is depend of above function
     * Update user password
     * @param array $data
     * @return bool
     */
    public function updatePasswordForReminder(array $data) : bool
    {
        $return = false;
        if ( $this->validator->with($data)->passes('update_password_from_reminder') )
        {
            $token    = Arr::get($data, 'token');
            $password = Arr::get($data, 'password');
            try
            {
                $token = decrypt($token);
                $params = explode('_____', $token);
                throw_unless(count($params) == 2, InvalidParamsException::class);
                $data = [
                    'email' => $params[0],
                    'code'  => $params[1]
                ];
                if ( $this->reminderCanChangePassword($data) )
                {
                    $user = Sentinel::getUserRepository()->findByCredentials(['email'=>$data['email']]);
                    Sentinel::update($user, compact('password'));
                    //$user->notify(new PasswordChanged());
                    $return = true;
                }
            }
            catch (Throwable $e)
            {
                $this->putError( $e->getMessage() );
            }
        }
        return $return;
    }

    /**
     * Force activate user
     * @param int $id
     * @return bool
     */
    public function forceActivate(int $id ) : bool
    {
        $return = false;
        $user = Sentinel::getUserRepository()->findById($id);
        try
        {
            ( $activation = Sentinel::getActivationRepository()->exists($user) ) || ( $activation = Sentinel::getActivationRepository()->create($user) );
            $data_activation = [ 'email' => $user->email, 'code' => $activation->code ];
            $return = $this->activate($data_activation);
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    /**
     * Deactivate user
     * @param int $user_id
     * @return bool
     */
    public function deactivateUser(int $user_id) : bool
    {
        $return = false;
        try
        {
            $user = $this->usersRepository->find($user_id);
            throw_if(is_null($user), UserNotFoundException::class);
            throw_unless($user->is_activated, UserNotActiveException::class);
            throw_unless(Sentinel::getActivationRepository()->remove($user), new UnexpectedException());
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    /**
     * Activate user
     * @param array $data
     * @return bool
     */
    public function activate(array $data) : bool
    {
        $return = false;
        if ( $this->validator->with($data)->passes('activate') )
        {
            try
            {
                $credentials = [ 'email' => $data['email'] ];
                $user = Sentinel::getUserRepository()->findByCredentials($credentials);
                throw_if(is_null($user), UserNotFoundException::class);
                throw_if($user->is_activated, UserAlreadyActiveException::class);
                throw_unless(Sentinel::getActivationRepository()->complete($user, $data['code']),
                    new UnexpectedException('No se pudo completar la activación del usuario. Contacte al administrador.'));
                $return = true;
            }
            catch (Throwable $e)
            {
                $this->pushError($e->getMessage());
            }
        }
        return $return;
    }

    /**
     * Update user's data
     * @param int $id
     * @param array $data
     * @return User|null
     */
    public function update(int $id, array $data) : ?User
    {
        $return = null;
        $user = Sentinel::getUserRepository()->findById($id);
        try
        {
            throw_if(is_null($user), UserNotFoundException::class);
            $this->validator->add('update', 'email',  Rule::unique('users')->ignore( $user->id ));
            if ( $this->validator->with( $data )->passes('update') )
            {
                $updated_data = Arr::only($data,[ 'first_name', 'last_name', 'email']);
                throw_unless( $this->usersRepository->update($id, $updated_data ), UserNotUpdatedException::class );
                $return = $this->usersRepository->find($id);
            }
        } catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    /**
     * Delete user
     * @param int $id
     * @return bool
     */
    public function delete(int $id) : bool
    {
        $return = false;
        $user = Sentinel::getUserRepository()->findById($id);
        try {
            throw_if(is_null($user), UserNotFoundException::class);
            $current_user = Sentinel::getUser();
            if($current_user!==null) {
                throw_if($user->id === $current_user->id, DeleteMyselfException::class);
            }
            $return = $this->usersRepository->delete($user->id);
        } catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    /**
     * Remove admin role for one user, one role is almost required.
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function changeAdminRole( int $id, array $data ) : bool
    {
        $return = false;
        $user = $this->usersRepository->find($id);
        try
        {
            throw_if(is_null($user), UserNotFoundException::class);
            throw_unless($user->is_admin, UserDoesNotAdminException::class);
            throw_if($this->validator->with($data)->fails('change-admin-role'), new UnexpectedException($this->validator->errors()->first()));
            $roles		= Arr::get($data,'roles');
            $user->roles()->sync($roles);
            $user->save();
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->putError($e->getMessage());
        }
        return $return;
    }

    /**
     * Remove all user's roles and put admin role for the user
     * @param int $id
     * @return bool
     */
    public function transformInAdministrator( int $id ) : bool
    {
        $return = false;
        $user = Sentinel::getUserRepository()->findById($id);
        try
        {
            $admin_role = Sentinel::getRoleRepository()->findBySlug(config('essence.admin_role_slug'));
            throw_if(is_null($user), UserNotFoundException::class);
            throw_if($user->id_admin, UserAlreadyIsAdminException::class);
            throw_if(is_null($admin_role), AdminRoleNotFound::class);
            $user->roles()->sync([$admin_role->id]);
            $user->save();
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->putError($e->getMessage());
        }
        return $return;
    }

    /**
     * Update roles for one user from array of ids.
     * @param int $user_id
     * @param array $roles
     * @return bool
     */
    public function updateRoles( int $user_id, array $roles = [] ) : bool
    {
        $return = false;
        $user = $this->usersRepository->find($user_id);
        try
        {
            throw_if(is_null($user), UserNotFoundException::class);
            throw_unless(count($roles) > 0, InvalidParamsException::class);
            $user->roles()->sync($roles);
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

    /**
     * Force login with some user
     * @param int $user_id
     * @return bool
     */
    public function loginWith(int $user_id) : bool
    {
        $return = false;
        try
        {
            $user = $this->usersRepository->find($user_id);
            throw_if(is_null($user), UserNotFoundException::class);
            Sentinel::login($user);
            $return = true;
        }
        catch (Throwable $e)
        {
            $this->pushError($e->getMessage());
        }
        return $return;
    }

}
