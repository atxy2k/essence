<?php namespace Atxy2k\Essence\Tests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/2/2019
 * Time: 11:32
 */

use Atxy2k\Essence\Repositories\UsersRepository;
use Atxy2k\Essence\Services\RolesService;
use Atxy2k\Essence\Validators\UsersValidator;
use Atxy2k\Essence\Services\UsersService;
use Cartalyst\Sentinel\Users\EloquentUser;
use DB;
use Atxy2k\Essence\Eloquent\User;
use Illuminate\Support\Collection;
use Throwable;

class UsersTest extends TestCase
{

    public function testValidatorLoginWithEmptyDataReturnFalse()
    {
        $validator = $this->app->make(UsersValidator::class);
        $data = [];
        $this->assertFalse($validator->with($data)->passes('login'));
        $this->assertEquals(2, $validator->errors()->count());
    }

    public function testValidatorLoginWithInCompleteDataReturnFalse()
    {
        $validator = $this->app->make(UsersValidator::class);
        $data = ['email' => 'ivan.alvarado@serprogramador.es'];
        $this->assertFalse($validator->with($data)->passes('login'));
        $this->assertEquals(1, $validator->errors()->count());
    }

    public function testValidatorLoginWithDataAndReturnTrue()
    {
        $validator = $this->app->make(UsersValidator::class);
        $data = ['email' => 'ivan.alvarado@serprogramador.es', 'password' => 'testPassword' ];
        $this->assertTrue($validator->with($data)->passes('login'));
        $this->assertEquals(0, $validator->errors()->count());
    }

    public function testValidatorRegisterWithIncompleteData()
    {
        $validator = $this->app->make(UsersValidator::class);
        $data = [];
        $this->assertFalse($validator->with($data)->passes('register'));
        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'password'   => 'pleaseDontUsePasswordAsPassword',
            'roles'      => []
        ];
        $this->assertFalse($validator->with($data)->passes('register'));
        $this->assertEquals(4, $validator->errors()->count());

        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation'   => 'ivan.alvarado@serprogramador.es',
            'password'   => 'pleaseDontUsePasswordAsPassword',
            'asign_password' => true,
            'roles'      => []
        ];
        $this->assertFalse($validator->with($data)->passes('register'));
        $this->assertEquals(2, $validator->errors()->count(), $validator->errors()->first());

        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'password'   => 'pleaseDontUsePasswordAsPassword',
            'password_confirmation' => 'pleaseDontUsePasswordAsPassword',
            'asign_password' => true,
            'roles'      => []
        ];
        $this->assertFalse($validator->with($data)->passes('register'));
        $this->assertEquals(1, $validator->errors()->count());

        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'password'   => 'pleaseDontUsePasswordAsPassword',
            'password_confirmation' => 'pleaseDontUsePasswordAsPassword',
            'asign_password' => true,
            'roles'      => [1,2,3]
        ];
        $this->assertTrue($validator->with($data)->passes('register'));
        $this->assertEquals(0, $validator->errors()->count());

        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'roles'      => [1,2,3]
        ];
        $this->assertTrue($validator->with($data)->passes('register'));
        $this->assertEquals(0, $validator->errors()->count());

        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'asign_password' => 1,
            'roles'      => [1,2,3]
        ];
        $this->assertFalse($validator->with($data)->passes('register'));
        $this->assertEquals(1, $validator->errors()->count());

        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'asign_password' => true,
            'password'   => 'pleaseDontUsePasswordAsPassword',
            'password_confirmation' => 'pleaseDontUsePasswordAsPassword',
            'roles'      => [1,2,3]
        ];
        $this->assertTrue($validator->with($data)->passes('register'));
        $this->assertEquals(0, $validator->errors()->count());
    }
    /** Testing without notifications */
    public function testCreateUserWithAdminRole()
    {
        DB::beginTransaction();
        /** @var UsersService $usersService */
        $usersService = $this->app->make(UsersService::class);
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        $role_data = [
            'name' => config('essence.admin_role_slug'),
        ];
        $role = $rolesService->create($role_data);
        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'password'   => time(),
            'password_confirmation'   => time(),
            'roles'      => [$role->id],
            'asign_password' => true
        ];
        $user = $usersService->register($data);
        $this->assertNotNull($user, $usersService->errors()->first());
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($data['first_name'], $user->first_name);
        $this->assertEquals($data['last_name'], $user->last_name);
        $this->assertEquals($data['email'], $user->email);
        $this->assertNotNull($user->full_name);
        $this->assertFalse($user->is_activated);
        $this->assertEquals(1, $user->roles->count());
        $this->assertTrue($user->is_admin);
        DB::rollBack();
    }

    public function testCreateUserWithNoAdminRole()
    {
        DB::beginTransaction();
        /** @var UsersService $usersService */
        $usersService = $this->app->make(UsersService::class);
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        $role_data = [
            'name' => 'tester',
        ];
        $role = $rolesService->create($role_data);
        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'password'   => time(),
            'password_confirmation'   => time(),
            'roles'      => [$role->id],
            'asign_password' => true
        ];
        $user = $usersService->register($data);
        $this->assertNotNull($user, $usersService->errors()->first());
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($data['first_name'], $user->first_name);
        $this->assertEquals($data['last_name'], $user->last_name);
        $this->assertEquals($data['email'], $user->email);
        $this->assertNotNull($user->full_name);
        $this->assertFalse($user->is_activated);
        $this->assertEquals(1, $user->roles->count());
        $this->assertFalse($user->is_admin);
        DB::rollBack();
    }

    public function testAutoActivateUser()
    {
        DB::beginTransaction();
        /** @var UsersService $usersService */
        $usersService = $this->app->make(UsersService::class);
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        $role_data = [
            'name' => 'tester',
        ];
        $role = $rolesService->create($role_data);
        $this->assertNotNull($role, $rolesService->errors()->first());
        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'password'   => time(),
            'password_confirmation'   => time(),
            'roles'      => [$role->id],
            'activate'   => true,
            'asign_password' => true
        ];
        $user = $usersService->register($data);
        $this->assertNotNull($user, $usersService->errors()->first());
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($data['first_name'], $user->first_name);
        $this->assertEquals($data['last_name'], $user->last_name);
        $this->assertEquals($data['email'], $user->email);
        $this->assertNotNull($user->password);
        $this->assertNotEquals($data['password'], $user->password);
        $this->assertNotNull($user->full_name);
        $this->assertTrue($user->is_activated);
        $this->assertEquals(1, $user->roles->count());
        $this->assertFalse($user->is_admin);
        DB::rollBack();
    }

    //TODO check this function, doesn't work in unit tests but I don't know if the reason is the session or something is wrong
//    public function testLoginUserWithRealData()
//    {
//        DB::beginTransaction();
//        /** @var UsersService $usersService */
//        $usersService = $this->app->make(UsersService::class);
//        /** @var RolesService $rolesService */
//        $rolesService = $this->app->make(RolesService::class);
//        $role_data = [
//            'name' => 'tester',
//        ];
//        $role = $rolesService->create($role_data);
//        $username = 'ivan.alvarado@serprogramador.es';
//        $password = uniqid();
//        $data = [
//            'first_name' => 'Ivan',
//            'last_name'  => 'Alvarado',
//            'email'      => $username,
//            'email_confirmation' => $username,
//            'password'   => $password,
//            'password_confirmation'   => $password,
//            'roles'      => [$role->id],
//            'activate'   => true,
//            'assign_password' => true
//        ];
//        $user = $usersService->register($data);
//        $this->assertNotNull($user);
//        $credentials = [
//            'email' => $username,
//            'password' => $password
//        ];
//        $authenticate = $usersService->login($credentials);
//        $this->assertTrue($authenticate, $usersService->errors()->first());
//        DB::rollBack();
//    }

    public function testResetUserPasswordReturnTrue()
    {
        DB::beginTransaction();
        /** @var UsersService $usersService */
        $usersService = $this->app->make(UsersService::class);
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        $role_data = [
            'name' => 'tester',
        ];
        $role = $rolesService->create($role_data);
        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'password'   => time(),
            'password_confirmation'   => time(),
            'roles'      => [$role->id],
            'activate'   => true,
            'asign_password' => true,
        ];
        $user = $usersService->register($data);
        $this->assertNotNull($user, $usersService->errors()->first());

        $changed = $usersService->resetPassword($user->id, [
            'password' => 'hi12345',
            'password_confirmation' => 'hi12345',
        ]);
        $this->assertTrue($changed);

        DB::rollBack();
    }

    public function testCheckEmailAvailabilityWithRealDataReturnFalseAndTrue()
    {
        DB::beginTransaction();
        /** @var UsersService $usersService */
        $usersService = $this->app->make(UsersService::class);
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        $role_data = [
            'name' => 'tester',
        ];
        $role = $rolesService->create($role_data);
        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'password'   => time(),
            'password_confirmation'   => time(),
            'roles'      => [$role->id],
            'activate'   => true,
            'asign_password' => true
        ];
        $user = $usersService->register($data);
        $this->assertNotNull($user, $usersService->errors()->first());

        $this->assertFalse($usersService->checkEmailAvailability($user->email));
        $this->assertTrue($usersService->checkEmailAvailability($user->email, $user->id));

        DB::rollBack();
    }

    /** Testing without notifications */
    public function testRequestEmailChanged()
    {
        DB::beginTransaction();
        /** @var UsersService $usersService */
        $usersService = $this->app->make(UsersService::class);
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        $role_data = [
            'name' => 'tester',
        ];
        $role = $rolesService->create($role_data);
        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'password'   => time(),
            'password_confirmation'   => time(),
            'roles'      => [$role->id],
            'activate'   => true,
            'asign_password' => true
        ];
        $user = $usersService->register($data);
        $this->assertNotNull($user, $usersService->errors()->first());
        $this->assertNotNull($user->changeEmailRequests);
        $this->assertInstanceOf(Collection::class, $user->changeEmailRequests);

        $this->assertTrue($usersService->requestEmailChanged($user->id, [
            'email' => 'atxy2k@gmail.com',
            'email_confirmation' => 'atxy2k@gmail.com'
        ]), $usersService->errors()->first());

        DB::rollBack();
    }

// TODO check this test, when run return sentinel.reminders does not exist.
    public function testCreateReminderForEmail()
    {
        DB::beginTransaction();
        /** @var UsersService $usersService */
        $usersService = $this->app->make(UsersService::class);
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        $role_data = [
            'name' => 'tester',
        ];
        $role = $rolesService->create($role_data);
        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'password'   => time(),
            'password_confirmation'   => time(),
            'roles'      => [$role->id],
            'activate'   => true,
            'asign_password' => true
        ];
        $user = $usersService->register($data);
        $reminder = $usersService->createReminder($user->email);
        $this->assertTrue($reminder, $usersService->errors()->first());
        DB::rollBack();
    }

    public function testForceActivationAndDeactivationUserAndReturnTrue()
    {
        DB::beginTransaction();
        /** @var UsersService $usersService */
        $usersService = $this->app->make(UsersService::class);
        /** @var UsersRepository $usersRepository */
        $usersRepository = $this->app->make(UsersRepository::class);
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        $role_data = [
            'name' => 'tester',
        ];
        $role = $rolesService->create($role_data);
        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'password'   => time(),
            'password_confirmation'   => time(),
            'asign_password' => true,
            'roles'      => [$role->id]
        ];
        $user = $usersService->register($data);
        $this->assertFalse($user->is_activated);
        $this->assertTrue($usersService->forceActivate($user->id), $usersService->errors()->first());

        $user_again = $usersRepository->find($user->id);
        $this->assertNotNull($user_again);
        $this->assertTrue($user_again->is_activated);

        $this->assertTrue($usersService->deactivateUser($user_again->id), $usersService->errors()->first());
        $user_initial = $usersRepository->find($user->id);
        $this->assertFalse($user_initial->is_activated);

        DB::rollBack();
    }

    public function testUpdateUser()
    {
        DB::beginTransaction();
        /** @var UsersService $usersService */
        $usersService = $this->app->make(UsersService::class);
        /** @var UsersRepository $usersRepository */
        $usersRepository = $this->app->make(UsersRepository::class);
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        $role_data = [
            'name' => 'tester',
        ];
        $role = $rolesService->create($role_data);
        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'password'   => time(),
            'password_confirmation'   => time(),
            'asign_password' => true,
            'roles'      => [$role->id]
        ];
        $user = $usersService->register($data);
        $update_data = [
            'first_name'    => 'John',
            'last_name'     => 'Doe'
        ];
        $user_updated = $usersService->update($user->id, $update_data);
        $this->assertNotNull($user_updated);
        $this->assertInstanceOf(User::class, $user_updated);
        $this->assertEquals($update_data['first_name'], $user_updated->first_name);
        $this->assertEquals($update_data['last_name'], $user_updated->last_name);
        $this->assertEquals($user->email, $user_updated->email);
        $this->assertEquals($user->password, $user_updated->password);
        $this->assertEquals($user->roles->count(), $user_updated->roles->count());
        DB::rollBack();
    }

    public function testDeleteUser()
    {
        DB::beginTransaction();
        /** @var UsersService $usersService */
        $usersService = $this->app->make(UsersService::class);
        /** @var UsersRepository $usersRepository */
        $usersRepository = $this->app->make(UsersRepository::class);
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        $role_data = [
            'name' => 'tester',
        ];
        $role = $rolesService->create($role_data);
        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'password'   => time(),
            'password_confirmation'   => time(),
            'roles'      => [$role->id],
            'asign_password' => true
        ];
        $user = $usersService->register($data);
        $this->assertTrue($usersService->delete($user->id));
        $this->assertNull($usersRepository->find($user->id));
        DB::rollBack();
    }

    public function testChangeAdminRoleWithUserThatDoesNotAdminReturnFalse()
    {
        DB::beginTransaction();
        /** @var UsersService $usersService */
        $usersService = $this->app->make(UsersService::class);
        /** @var UsersRepository $usersRepository */
        $usersRepository = $this->app->make(UsersRepository::class);
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        $role_data = [
            'name' => 'tester',
        ];
        $role = $rolesService->create($role_data);
        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'password'   => time(),
            'password_confirmation'   => time(),
            'roles'      => [$role->id],
            'asign_password' => true,
        ];
        $user = $usersService->register($data);
        $this->assertFalse($usersService->changeAdminRole($user->id, []));
        DB::rollBack();
    }

    public function testChangeAdminRoleWithAdminUserReturnTrue()
    {
        DB::beginTransaction();
        /** @var UsersService $usersService */
        $usersService = $this->app->make(UsersService::class);
        /** @var UsersRepository $usersRepository */
        $usersRepository = $this->app->make(UsersRepository::class);
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        $role_admin_data = [ 'name' => config('essence.admin_role_slug') ];
        $admin_role = $rolesService->create($role_admin_data);

        $other_role = [ 'name' => 'tester' ];
        $tester_role = $rolesService->create($other_role);

        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'password'   => time(),
            'password_confirmation'   => time(),
            'asign_password' => true,
            'roles'      => [$admin_role->id]
        ];
        $user = $usersService->register($data);
        $this->assertTrue($user->is_admin);
        $this->assertTrue(
            $usersService->changeAdminRole($user->id, [ 'roles' => [$tester_role->id]]),
            $usersService->errors()->first()
        );
        DB::rollBack();
    }

    public function testUpdateRoles()
    {
        DB::beginTransaction();
        /** @var UsersService $usersService */
        $usersService = $this->app->make(UsersService::class);
        /** @var UsersRepository $usersRepository */
        $usersRepository = $this->app->make(UsersRepository::class);
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        $role_admin_data = [ 'name' => config('essence.admin_role_slug') ];
        $admin_role = $rolesService->create($role_admin_data);

        $other_role = [ 'name' => 'tester' ];
        $tester_role = $rolesService->create($other_role);
        $this->assertNotNull($tester_role, $rolesService->errors()->first());
        $data = [
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado',
            'email'      => 'ivan.alvarado@serprogramador.es',
            'email_confirmation' => 'ivan.alvarado@serprogramador.es',
            'password'   => time(),
            'password_confirmation'   => time(),
            'asign_password' => true,
            'roles'      => [$admin_role->id]
        ];
        $user = $usersService->register($data);
        $this->assertNotNull($user->roles);
        $this->assertEquals(1, $user->roles->count());
        $new_roles = [ $admin_role->id, $tester_role->id ];
        $this->assertTrue($usersService->updateRoles($user->id, $new_roles));

        $_user = $usersRepository->find($user->id);
        $this->assertEquals(count($new_roles), $_user->roles->count());

        DB::rollBack();
    }

}
