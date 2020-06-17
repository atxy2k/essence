<?php


namespace Atxy2k\Essence\Tests;

use Atxy2k\Essence\Constants\Interactions;
use Atxy2k\Essence\Eloquent\InteractionType;
use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\Repositories\UsersRepository;
use Atxy2k\Essence\Services\InteractionsTypeService;
use Atxy2k\Essence\Services\RolesService;
use Atxy2k\Essence\Services\UsersService;
use DB;
use Hash;
use Illuminate\Support\Str;

class UsersTest extends TestCase
{
    public function testRegister()
    {
       DB::beginTransaction();
       /** @var RolesService $rolesService */
       $rolesService = $this->app->make(RolesService::class);
       /** @var UsersService $service */
       $service = $this->app->make(UsersService::class);
       /** @var InteractionsTypeService $interactionTypeService */
       $interactionTypeService = $this->app->make(InteractionsTypeService::class);

       $interaction_create_type = $interactionTypeService->create([
           'name' => 'create',
           'description' => 'Create element'
       ]);
       $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
       $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

        $role_data = [
            'name' => 'Developer'
        ];
        $role = $rolesService->create($role_data);
        $this->assertNotNull($role, $rolesService->errors()->first());

       $data = [];
       $item = $service->register($data);
       $this->assertNull($item);

       $data['first_name'] = 'ivan';
       $data['last_name'] = 'alvarado';
       $data['email'] = 'dev@serprogramador.es';
       $data['email_confirmation'] = 'dev@serprogramador.es';
       $data['password'] = 'passwd';
       $data['roles'] = [$role->id];

       $item = $service->register($data);
       $this->assertNull($item);

       $data['password_confirmation'] = 'passwd';

       $item = $service->register($data);
       $this->assertNotNull($item, json_encode($service->errors()));
       $this->assertInstanceOf(User::class, $item);
       $this->assertEquals($data['first_name'], $item->first_name);
       $this->assertEquals($data['last_name'], $item->last_name);
       $this->assertEquals($data['email'], $item->email);
       $this->assertEquals(1, $item->roles->count());
       $this->assertEquals(1, $item->interactions->count());
       $this->assertTrue(Hash::check($data['password'], $item->password));
       $this->assertNotNull($item->created_at);
       $this->assertNotNull($item->updated_at);
       $this->assertFalse($item->active);

       DB::rollback();
    }

    public function testLoginAndAuthenticate()
    {
       DB::beginTransaction();
       /** @var RolesService $rolesService */
       $rolesService = $this->app->make(RolesService::class);
       /** @var UsersService $service */
       $service = $this->app->make(UsersService::class);
       /** @var InteractionsTypeService $interactionTypeService */
       $interactionTypeService = $this->app->make(InteractionsTypeService::class);

       $interaction_create_type = $interactionTypeService->create([
           'name' => 'create',
           'description' => 'Create element'
       ]);
       $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
       $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

       $interaction_login_type = $interactionTypeService->create([
           'name' => 'login',
           'description' => 'Login'
       ]);
       $this->assertNotNull($interaction_login_type, $interactionTypeService->errors()->first());
       $this->assertInstanceOf(InteractionType::class, $interaction_login_type);

       $interaction_authenticate_type = $interactionTypeService->create([
           'name' => 'authenticate',
           'description' => 'Authenticate'
       ]);
       $this->assertNotNull($interaction_authenticate_type, $interactionTypeService->errors()->first());
       $this->assertInstanceOf(InteractionType::class, $interaction_authenticate_type);

        $role_data = [
            'name' => 'Developer'
        ];
        $role = $rolesService->create($role_data);
        $this->assertNotNull($role, $rolesService->errors()->first());

       $data = [
           'first_name' => 'ivan',
           'last_name'  => 'alvarado',
           'email'      => 'dev@serprogramador.es',
           'email_confirmation'      => 'dev@serprogramador.es',
           'password'   => 'passwd',
           'password_confirmation'   => 'passwd',
           'roles' => [$role->id],
           'activate' => true
       ];

       $item = $service->register($data);
       $this->assertNotNull($item, json_encode($service->errors()));
       $this->assertInstanceOf(User::class, $item);
        $this->assertEquals($data['email'], $item->email);
        $this->assertTrue($item->active);

        /****************************************************
         * Login with fake data
         ****************************************************/
        $credentials = [
            'email'     => 'dev@serprogramador.es',
            'password'  => 'another_password'
        ];
        $this->assertFalse($service->login($credentials));
        $this->assertFalse($service->authenticate($credentials));

        /****************************************************
         * Login with real data
         ****************************************************/
        $this->assertTrue($service->login([
            'email'     => $data['email'],
            'password'  => $data['password']
        ]), json_encode($service->errors()));
        $this->assertTrue($service->authenticate([
            'email'     => $data['email'],
            'password'  => $data['password']
        ]));
        DB::rollback();
    }

    public function testResetPassword()
    {
        DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        /** @var UsersService $service */
        $service = $this->app->make(UsersService::class);
        /** @var InteractionsTypeService $interactionTypeService */
        $interactionTypeService = $this->app->make(InteractionsTypeService::class);

        $interaction_create_type = $interactionTypeService->create([
            'name' => 'create',
            'description' => 'Create element'
        ]);
        $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

        $interaction_update_type = $interactionTypeService->create([
            'name' => 'update',
            'description' => 'update'
        ]);
        $this->assertNotNull($interaction_update_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_update_type);

        $interaction_authenticate_type = $interactionTypeService->create([
            'name' => 'authenticate',
            'description' => 'Authenticate'
        ]);
        $this->assertNotNull($interaction_authenticate_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_authenticate_type);

        $role_data = [
            'name' => 'Developer'
        ];
        $role = $rolesService->create($role_data);
        $this->assertNotNull($role, $rolesService->errors()->first());

        $data = [
            'first_name' => 'ivan',
            'last_name'  => 'alvarado',
            'email'      => 'dev@serprogramador.es',
            'email_confirmation'      => 'dev@serprogramador.es',
            'password'   => 'passwd',
            'password_confirmation'   => 'passwd',
            'roles' => [$role->id],
            'activate' => true
        ];

        $item = $service->register($data);
        $this->assertNotNull($item, json_encode($service->errors()));
        $this->assertInstanceOf(User::class, $item);

        $data_reset_password = [
            'old_password' => 'fake_password',
            'password' => 'shark',
            'password_confirmation' => 'shark',
        ];
        $this->assertFalse($service->resetPassword($item->id, $data_reset_password));

        $data_reset_password['old_password'] = 'passwd';
        $this->assertTrue($service->resetPassword($item->id, $data_reset_password));

        $this->assertTrue($service->authenticate([
            'email' => $item->email,
            'password' => $data_reset_password['password']
        ]));

        DB::rollback();
    }

    public function testChangePassword()
    {
        DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        /** @var UsersService $service */
        $service = $this->app->make(UsersService::class);
        /** @var InteractionsTypeService $interactionTypeService */
        $interactionTypeService = $this->app->make(InteractionsTypeService::class);

        $interaction_create_type = $interactionTypeService->create([
            'name' => 'create',
            'description' => 'Create element'
        ]);
        $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

        $interaction_update_type = $interactionTypeService->create([
            'name' => 'update',
            'description' => 'update'
        ]);
        $this->assertNotNull($interaction_update_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_update_type);

        $interaction_authenticate_type = $interactionTypeService->create([
            'name' => 'authenticate',
            'description' => 'Authenticate'
        ]);
        $this->assertNotNull($interaction_authenticate_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_authenticate_type);

        $role_data = [
            'name' => 'Developer'
        ];
        $role = $rolesService->create($role_data);
        $this->assertNotNull($role, $rolesService->errors()->first());

        $data = [
            'first_name' => 'ivan',
            'last_name'  => 'alvarado',
            'email'      => 'dev@serprogramador.es',
            'email_confirmation'      => 'dev@serprogramador.es',
            'password'   => 'passwd',
            'password_confirmation'   => 'passwd',
            'roles' => [$role->id],
            'activate' => true
        ];

        $item = $service->register($data);
        $this->assertNotNull($item, json_encode($service->errors()));
        $this->assertInstanceOf(User::class, $item);

        $data_change_password = [
            'password' => 'shark',
            'password_confirmation' => 'shark',
        ];
        $this->assertTrue($service->changePassword($item->id, $data_change_password));

        $this->assertTrue($service->authenticate([
            'email' => $item->email,
            'password' => $data_change_password['password']
        ]));

        DB::rollback();
    }

    public function testCheckEmailAvailability()
    {
        DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        /** @var UsersService $service */
        $service = $this->app->make(UsersService::class);
        /** @var InteractionsTypeService $interactionTypeService */
        $interactionTypeService = $this->app->make(InteractionsTypeService::class);

        $interaction_create_type = $interactionTypeService->create([
            'name' => 'create',
            'description' => 'Create element'
        ]);
        $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

        $interaction_update_type = $interactionTypeService->create([
            'name' => 'update',
            'description' => 'update'
        ]);
        $this->assertNotNull($interaction_update_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_update_type);

        $interaction_authenticate_type = $interactionTypeService->create([
            'name' => 'authenticate',
            'description' => 'Authenticate'
        ]);
        $this->assertNotNull($interaction_authenticate_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_authenticate_type);

        $role_data = [
            'name' => 'Developer'
        ];
        $role = $rolesService->create($role_data);
        $this->assertNotNull($role, $rolesService->errors()->first());

        $data = [
            'first_name' => 'ivan',
            'last_name'  => 'alvarado',
            'email'      => 'dev@serprogramador.es',
            'email_confirmation'      => 'dev@serprogramador.es',
            'password'   => 'passwd',
            'password_confirmation'   => 'passwd',
            'roles' => [$role->id],
            'activate' => true
        ];

        $this->assertTrue($service->checkEmailAvailability($data['email']));

        $item = $service->register($data);
        $this->assertNotNull($item, json_encode($service->errors()));
        $this->assertInstanceOf(User::class, $item);

        $this->assertFalse($service->checkEmailAvailability($data['email']));
        $this->assertTrue($service->checkEmailAvailability($data['email'], $item->id));

        DB::rollback();
    }

    public function testRequestPasswordRecovery()
    {
        DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        /** @var UsersService $service */
        $service = $this->app->make(UsersService::class);
        /** @var InteractionsTypeService $interactionTypeService */
        $interactionTypeService = $this->app->make(InteractionsTypeService::class);

        $interaction_create_type = $interactionTypeService->create([
            'name' => 'create',
            'description' => 'Create element'
        ]);
        $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

        $role_data = [
            'name' => 'Developer'
        ];
        $role = $rolesService->create($role_data);
        $this->assertNotNull($role, $rolesService->errors()->first());

        $data = [
            'first_name' => 'ivan',
            'last_name'  => 'alvarado',
            'email'      => 'dev@serprogramador.es',
            'email_confirmation'      => 'dev@serprogramador.es',
            'password'   => 'passwd',
            'password_confirmation'   => 'passwd',
            'roles' => [$role->id],
            'activate' => true
        ];

        $item = $service->register($data);
        $this->assertNotNull($item, json_encode($service->errors()));
        $this->assertInstanceOf(User::class, $item);

        $recovery_data =['email' => 'nonexistentuser@gmail.com'];
        $this->assertNull($service->requestPasswordRecovery($recovery_data));

        $token = $service->requestPasswordRecovery(['email' => $data['email']]);
        $this->assertNotNull($token, json_encode($service->errors()));
        $this->assertTrue(strlen($token) > 0);

        $decoded_string = decrypt($token);
        $decoded_data = json_decode($decoded_string, true);
        $this->assertIsArray($decoded_data);
        $this->assertTrue(isset($decoded_data['email']));
        $this->assertTrue(isset($decoded_data['date']));

        $this->assertTrue($service->validateRequestPasswordRecovery($token));
        $this->assertFalse($service->validateRequestPasswordRecovery(encrypt(Str::random())));

        DB::rollback();
    }

    public function testActivateAndDeactivateFunctions()
    {
        DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        /** @var UsersService $service */
        $service = $this->app->make(UsersService::class);
        /** @var InteractionsTypeService $interactionTypeService */
        $interactionTypeService = $this->app->make(InteractionsTypeService::class);
        /** @var UsersRepository $usersRepository */
        $usersRepository = $this->app->make(UsersRepository::class);

        $interaction_create_type = $interactionTypeService->create([
            'name' => 'create',
            'description' => 'Create element'
        ]);
        $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

        $interaction_activate_type = $interactionTypeService->create([
            'name' => Interactions::ACTIVATE,
            'description' => 'Activat element'
        ]);
        $this->assertNotNull($interaction_activate_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class,$interaction_activate_type);

        $interaction_deactivate_type = $interactionTypeService->create([
            'name' => Interactions::DEACTIVATE,
            'description' => 'Activat element'
        ]);
        $this->assertNotNull($interaction_deactivate_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class,$interaction_deactivate_type);

        $role_data = [
            'name' => 'Developer'
        ];
        $role = $rolesService->create($role_data);
        $this->assertNotNull($role, $rolesService->errors()->first());

        $data = [
            'first_name' => 'ivan',
            'last_name'  => 'alvarado',
            'email'      => 'dev@serprogramador.es',
            'email_confirmation'      => 'dev@serprogramador.es',
            'password'   => 'passwd',
            'password_confirmation'   => 'passwd',
            'roles' => [$role->id],
        ];

        $item = $service->register($data);
        $this->assertNotNull($item, json_encode($service->errors()));
        $this->assertInstanceOf(User::class, $item);

        $this->assertFalse($item->active);

        $this->assertTrue($service->activate($item->id));

        $item = $usersRepository->find($item->id);
        $this->assertTrue($item->active);

        $this->assertTrue($service->deactivate($item->id));
        $item = $usersRepository->find($item->id);
        $this->assertFalse($item->active);


        DB::rollback();
    }

    public function testUpdateAndDelete()
    {
        DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        /** @var UsersService $service */
        $service = $this->app->make(UsersService::class);
        /** @var InteractionsTypeService $interactionTypeService */
        $interactionTypeService = $this->app->make(InteractionsTypeService::class);
        /** @var UsersRepository $usersRepository */
        $usersRepository = $this->app->make(UsersRepository::class);

        $interaction_create_type = $interactionTypeService->create([
            'name' => 'create',
            'description' => 'Create element'
        ]);
        $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

        $interaction_activate_type = $interactionTypeService->create([
            'name' => Interactions::ACTIVATE,
            'description' => 'Activat element'
        ]);
        $this->assertNotNull($interaction_activate_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class,$interaction_activate_type);

        $interaction_deactivate_type = $interactionTypeService->create([
            'name' => Interactions::DEACTIVATE,
            'description' => 'Activat element'
        ]);
        $this->assertNotNull($interaction_deactivate_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class,$interaction_deactivate_type);

        $role_data = [
            'name' => 'Developer'
        ];
        $role = $rolesService->create($role_data);
        $this->assertNotNull($role, $rolesService->errors()->first());

        $data = [
            'first_name' => 'ivan',
            'last_name'  => 'alvarado',
            'email'      => 'dev@serprogramador.es',
            'email_confirmation'      => 'dev@serprogramador.es',
            'password'   => 'passwd',
            'password_confirmation'   => 'passwd',
            'roles' => [$role->id],
        ];

        $item = $service->register($data);
        $this->assertNotNull($item, json_encode($service->errors()));
        $this->assertInstanceOf(User::class, $item);

        $update_date = [
            'first_name' => 'victor',
            'last_name'  => 'gonzalez'
        ];

        $completed = $service->update($item->id, $update_date);
        $this->assertTrue($completed);
        $user = $usersRepository->find($item->id);
        $this->assertNotNull($user);
        $this->assertEquals($update_date['first_name'], $user->first_name);
        $this->assertEquals($update_date['last_name'], $user->last_name);

        $deleted = $service->delete($item->id);
        $this->assertTrue($deleted);

        $delete_item = $usersRepository->find($item->id);
        $this->assertNull($delete_item);
        DB::rollback();
    }

    public function testAdminPrivileges()
    {
        DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        /** @var UsersService $service */
        $service = $this->app->make(UsersService::class);
        /** @var InteractionsTypeService $interactionTypeService */
        $interactionTypeService = $this->app->make(InteractionsTypeService::class);
        /** @var UsersRepository $usersRepository */
        $usersRepository = $this->app->make(UsersRepository::class);

        $interaction_create_type = $interactionTypeService->create([
            'name' => 'create',
            'description' => 'Create element'
        ]);
        $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

        $interaction_activate_type = $interactionTypeService->create([
            'name' => Interactions::ACTIVATE,
            'description' => 'Activat element'
        ]);
        $this->assertNotNull($interaction_activate_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class,$interaction_activate_type);

        $interaction_deactivate_type = $interactionTypeService->create([
            'name' => Interactions::DEACTIVATE,
            'description' => 'Activat element'
        ]);
        $this->assertNotNull($interaction_deactivate_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class,$interaction_deactivate_type);

        $role_data = [
            'name' => 'Developer'
        ];
        $role = $rolesService->create($role_data);
        $this->assertNotNull($role, $rolesService->errors()->first());

        $standard_role_data = [
            'name' => 'Standard'
        ];
        $standard_role = $rolesService->create($standard_role_data);
        $this->assertNotNull($standard_role, $rolesService->errors()->first());


        $data = [
            'first_name' => 'ivan',
            'last_name'  => 'alvarado',
            'email'      => 'dev@serprogramador.es',
            'email_confirmation'      => 'dev@serprogramador.es',
            'password'   => 'passwd',
            'password_confirmation'   => 'passwd',
            'roles' => [$role->id],
        ];

        $item = $service->register($data);
        $this->assertNotNull($item, json_encode($service->errors()));
        $this->assertInstanceOf(User::class, $item);

        $this->assertTrue($item->is_admin);

        /********************************************************************
         * Trying to remove admin privileges with admin role in the list
         ********************************************************************/
        $this->assertFalse($service->removeAdminPrivileges($item->id, [$standard_role->id, $role->id]));

        $this->assertTrue($service->removeAdminPrivileges($item->id, [$standard_role->id]));
        $user = $usersRepository->find($item->id);
        $this->assertFalse($user->is_admin);

        $this->assertTrue($service->grantAdminPrivileges($user->id));
        $user = $usersRepository->find($user->id);
        $this->assertEquals(1, $user->roles->count());
        $this->assertTrue($user->is_admin);

        DB::rollback();
    }

    public function testLoginWithAnotherUser()
    {
        DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        /** @var UsersService $service */
        $service = $this->app->make(UsersService::class);
        /** @var InteractionsTypeService $interactionTypeService */
        $interactionTypeService = $this->app->make(InteractionsTypeService::class);
        /** @var UsersRepository $usersRepository */
        $usersRepository = $this->app->make(UsersRepository::class);

        $interaction_create_type = $interactionTypeService->create([
            'name' => 'create',
            'description' => 'Create element'
        ]);
        $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

        $interaction_activate_type = $interactionTypeService->create([
            'name' => Interactions::ACTIVATE,
            'description' => 'Activat element'
        ]);
        $this->assertNotNull($interaction_activate_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class,$interaction_activate_type);

        $interaction_deactivate_type = $interactionTypeService->create([
            'name' => Interactions::DEACTIVATE,
            'description' => 'Activat element'
        ]);
        $this->assertNotNull($interaction_deactivate_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class,$interaction_deactivate_type);

        $role_data = [
            'name' => 'Developer'
        ];
        $role = $rolesService->create($role_data);
        $this->assertNotNull($role, $rolesService->errors()->first());

        $standard_role_data = [
            'name' => 'Standard'
        ];
        $standard_role = $rolesService->create($standard_role_data);
        $this->assertNotNull($standard_role, $rolesService->errors()->first());


        $data = [
            'first_name' => 'ivan',
            'last_name'  => 'alvarado',
            'email'      => 'dev@serprogramador.es',
            'email_confirmation'      => 'dev@serprogramador.es',
            'password'   => 'passwd',
            'password_confirmation'   => 'passwd',
            'roles' => [$role->id],
        ];

        $item = $service->register($data);
        $this->assertNotNull($item, json_encode($service->errors()));
        $this->assertInstanceOf(User::class, $item);

        $this->assertTrue($service->loginWith($item->id));

        DB::rollback();
    }

    public function testAddRoleAndRemoveRole()
    {
        DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        /** @var UsersService $service */
        $service = $this->app->make(UsersService::class);
        /** @var InteractionsTypeService $interactionTypeService */
        $interactionTypeService = $this->app->make(InteractionsTypeService::class);
        /** @var UsersRepository $usersRepository */
        $usersRepository = $this->app->make(UsersRepository::class);

        $interaction_create_type = $interactionTypeService->create([
            'name' => 'create',
            'description' => 'Create element'
        ]);
        $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

        $interaction_activate_type = $interactionTypeService->create([
            'name' => Interactions::ACTIVATE,
            'description' => 'Activat element'
        ]);
        $this->assertNotNull($interaction_activate_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class,$interaction_activate_type);

        $interaction_deactivate_type = $interactionTypeService->create([
            'name' => Interactions::DEACTIVATE,
            'description' => 'Activat element'
        ]);
        $this->assertNotNull($interaction_deactivate_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class,$interaction_deactivate_type);

        $role_data = [
            'name' => 'Developer'
        ];
        $role = $rolesService->create($role_data);
        $this->assertNotNull($role, $rolesService->errors()->first());

        $standard_role_data = [
            'name' => 'Standard'
        ];
        $standard_role = $rolesService->create($standard_role_data);
        $this->assertNotNull($standard_role, $rolesService->errors()->first());

        $aux_role_data = [
            'name' => 'Aux'
        ];
        $aux_role = $rolesService->create($aux_role_data);
        $this->assertNotNull($aux_role, $rolesService->errors()->first());

        $data = [
            'first_name' => 'ivan',
            'last_name'  => 'alvarado',
            'email'      => 'dev@serprogramador.es',
            'email_confirmation'      => 'dev@serprogramador.es',
            'password'   => 'passwd',
            'password_confirmation'   => 'passwd',
            'roles' => [$role->id],
        ];

        $item = $service->register($data);
        $this->assertNotNull($item, json_encode($service->errors()));
        $this->assertInstanceOf(User::class, $item);

        $this->assertFalse($service->addRole($item->id, $standard_role->id));
        $this->assertFalse($service->removeRole($item->id, $role->id));

        $this->assertTrue($service->removeAdminPrivileges($item->id, [$standard_role->id]));

        $this->assertTrue($service->addRole($item->id, $aux_role->id));

        $user = $usersRepository->find($item->id);
        $this->assertEquals(2, $user->roles->count());

        $this->assertTrue($service->removeRole($item->id, $standard_role->id), json_encode($service->errors()));
        $user = $usersRepository->find($item->id);
        $this->assertEquals(1, $user->roles->count());

        $all_roles = [ $role->id, $standard_role->id, $aux_role->id ];

        $sync = $service->syncRoles($user->id, $all_roles);
        $this->assertTrue($sync);

        $user = $usersRepository->find($item->id);
        $this->assertEquals(3, $user->roles->count());

        DB::rollback();
    }



}