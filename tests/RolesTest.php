<?php namespace Atxy2k\Essence\Tests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/2/2019
 * Time: 08:57
 */

use Atxy2k\Essence\Eloquent\Role;
use Atxy2k\Essence\Repositories\RolesRepository;
use Atxy2k\Essence\Validators\RolesValidator;
use Atxy2k\Essence\Services\RolesService;
use Cartalyst\Sentinel\Roles\RoleInterface;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RolesTest extends TestCase
{

    public function testValidatorSendingEmptyDataAndResponseFalse()
    {
        $rolesValidator = $this->app->make(RolesValidator::class);
        $data = [];
        $this->assertFalse($rolesValidator->with($data)->passes('create'));
    }

    public function testValidatorSendingOnlyNameResponseTrue()
    {
        $rolesValidator = $this->app->make(RolesValidator::class);
        $data = ['name' => 'admin'];
        $this->assertTrue($rolesValidator->with($data)->passes('create'));
        $this->assertEquals(0, $rolesValidator->errors()->count());
    }

    public function testValidatorSendingIncorrectDataAndResponseFalse()
    {
        $rolesValidator = $this->app->make(RolesValidator::class);
        $data = ['name' => 1, 'routes' => 'fake data'];
        $this->assertFalse($rolesValidator->with($data)->passes('create'));
        $this->assertEquals(2, $rolesValidator->errors()->count());

        $data = ['name' => 'testing', 'routes' => 'fake data'];
        $this->assertFalse($rolesValidator->with($data)->passes('create'));
        $this->assertEquals(1, $rolesValidator->errors()->count());

        $data = ['name' => 1, 'routes' => ['test' => 1]];
        $this->assertFalse($rolesValidator->with($data)->passes('create'));
        $this->assertEquals(1, $rolesValidator->errors()->count());
    }

    public function testCreateRolWithoutUsers()
    {
        DB::beginTransaction();
        $rolesService = $this->app->make(RolesService::class);
        $name = 'test';
        $permissions = ['admin.dashboard.index' => 1];
        $data = ['name' => $name, 'routes' =>  $permissions];
        $role = $rolesService->create($data);
        $this->assertNotNull($role, $rolesService->errors()->first());
        $this->assertInstanceOf(RoleInterface::class, $role);
        $this->assertEquals($role->name, $name);
        $this->assertEquals($role->permissions, $permissions);
        $this->assertNotNull($role->created_at);
        $this->assertNotNull($role->updated_at);
        $this->assertEquals(0,$role->users->count());
        DB::rollBack();
    }

    public function testCreateRolWithUsers()
    {
        DB::beginTransaction();
        $rolesService = $this->app->make(RolesService::class);
        $name = 'test';
        $permissions = ['admin.dashboard.index' => 1];
        // TODO change this implementation with real objects
        $users = [1,2,3];
        $data = ['name' => $name, 'routes' =>  $permissions, 'users' => $users];
        $role = $rolesService->create($data);
        $this->assertNotNull($role, $rolesService->errors()->first());
        $this->assertInstanceOf(RoleInterface::class, $role);
        $this->assertEquals($role->name, $name);
        $this->assertEquals($role->slug, Str::slug($name));
        $this->assertEquals($role->permissions, $permissions);
        $this->assertNotNull($role->created_at);
        $this->assertNotNull($role->updated_at);
        $this->assertEquals(0,$role->users->count());
        DB::rollBack();
    }

    public function testNameAvailabilitySendingExistingAndResponseFalse()
    {
        DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        /** @var RolesRepository $rolesRepository */
        $rolesRepository = $this->app->make(RolesRepository::class);
        $name = 'test';
        $permissions = ['admin.dashboard.index' => 1];
        $data = ['name' => $name, 'routes' =>  $permissions];
        $role = $rolesService->create($data);
        $this->assertNotNull($role, $rolesService->errors()->first());

        $existing = $rolesRepository->findBySlug(Str::slug($name));
        $this->assertNotNull($existing);

        $this->assertFalse($rolesService->checkNameAvailability($name), $rolesService->errors()->first());
        $this->assertTrue($rolesService->checkNameAvailability($name, $role->id));

        DB::rollBack();
    }

    public function testNameAvailabilitySendingExistingExceptingIdAndResponseTrue()
    {
        DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        /** @var RolesRepository $rolesRepository */
        $rolesRepository = $this->app->make(RolesRepository::class);
        $name = 'test';
        $permissions = ['admin.dashboard.index' => 1];
        $data = ['name' => $name, 'routes' =>  $permissions];
        $role = $rolesService->create($data);
        $this->assertNotNull($role, $rolesService->errors()->first());

        $existing = $rolesRepository->findBySlug(Str::slug($name));
        $this->assertNotNull($existing);
        $this->assertTrue($rolesService->checkNameAvailability($name, $role->id));
        DB::rollBack();
    }

    public function testDeleteRoleAndResponseTrue()
    {
        DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        /** @var RolesRepository $rolesRepository */
        $rolesRepository = $this->app->make(RolesRepository::class);
        $name = 'test';
        $permissions = ['admin.dashboard.index' => 1];
        $data = ['name' => $name, 'routes' =>  $permissions];
        $role = $rolesService->create($data);
        $this->assertNotNull($role, $rolesService->errors()->first());

        $this->assertTrue($rolesService->delete($role->id));
        $this->assertNull($rolesRepository->find($role->id));
        DB::rollBack();
    }

    public function testCloneRole()
    {
        DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        $name = 'test';
        $permissions = ['admin.dashboard.index' => 1];
        $data = ['name' => $name, 'routes' =>  $permissions];
        $role = $rolesService->create($data);
        $role_cloned = $rolesService->clone($role->id, 'test 2');
        $this->assertNotNull($role_cloned);
        $this->assertInstanceOf(RoleInterface::class, $role_cloned);
        $this->assertEquals($role_cloned->permissions, $role->permissions);
        DB::rollBack();
    }

    public function testCreatingAdminRoleAndFilterFromOtherRoles()
    {
        DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        /** @var RolesRepository $rolesRepository */
        $rolesRepository = $this->app->make(RolesRepository::class);
        $name = config('essence.admin_role_slug');

        $this->assertNotNull($name);

        $permissions = ['admin.dashboard.index' => 1];
        $data = ['name' => $name, 'routes' =>  $permissions];
        $admin_role = $rolesService->create($data);
        $other_roles = [];
        $additional_roles = 10;
        foreach (range(1,$additional_roles) as $n)
        {
            $other_roles[] = $rolesService->create([
                'name' => uniqid(),
                'routes' => $permissions
            ]);
        }
        $this->assertNotNull($admin_role);
        $this->assertEquals($additional_roles, count($other_roles));
        $this->assertTrue($rolesRepository->isAdminRole($admin_role->id));
        foreach ( $other_roles as $role )
        {
            $this->assertFalse($rolesRepository->isAdminRole($role->id));
        }
        $requestingAgainNotAdminRoles = $rolesRepository->notAdminRoles();
        $this->assertInstanceOf(Collection::class, $requestingAgainNotAdminRoles);
        $this->assertEquals($additional_roles, $requestingAgainNotAdminRoles->count());
        foreach ( $requestingAgainNotAdminRoles as $role )
        {
            $this->assertFalse($rolesRepository->isAdminRole($role->id));
        }
        DB::rollBack();
    }

}
