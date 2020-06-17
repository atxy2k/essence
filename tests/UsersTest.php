<?php


namespace Atxy2k\Essence\Tests;

use Atxy2k\Essence\Eloquent\InteractionType;
use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\Services\InteractionsTypeService;
use Atxy2k\Essence\Services\RolesService;
use Atxy2k\Essence\Services\UsersService;
use DB;
use Hash;

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



}