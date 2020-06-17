<?php


namespace Atxy2k\Essence\Tests;


use Atxy2k\Essence\Constants\Interactions;
use Atxy2k\Essence\Eloquent\Interaction;
use Atxy2k\Essence\Eloquent\InteractionType;
use Atxy2k\Essence\Eloquent\Role;
use Atxy2k\Essence\Interfaces\Services\RolesServiceInterface;
use Atxy2k\Essence\Repositories\InteractionsRepository;
use Atxy2k\Essence\Repositories\InteractionsTypeRepository;
use Atxy2k\Essence\Repositories\RolesRepository;
use Atxy2k\Essence\Repositories\UsersRepository;
use Atxy2k\Essence\Services\ClaimsService;
use Atxy2k\Essence\Services\InteractionsTypeService;
use Atxy2k\Essence\Services\RolesService;
use Atxy2k\Essence\Services\UsersService;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RolesTest extends TestCase
{
    public function testCreateSimpleInstanceReturnRolesService()
    {
        /** @var RolesService $service */
        $service = $this->app->make(RolesService::class);
        $this->assertNotNull($service);
        $this->assertInstanceOf(RolesService::class, $service);
        $this->assertInstanceOf(RolesServiceInterface::class, $service);
    }

    public function testCreateWithWrongDataReturnNull()
    {
        /** @var RolesService $service */
        $service = $this->app->make(RolesService::class);
        $this->assertNull($service->create([]));
    }

    public function testUpdateWithWrongDataReturnFalse()
    {
        /** @var RolesService $service */
        $service = $this->app->make(RolesService::class);
        $this->assertFalse($service->update(-1,[]));
    }

    public function testDeleteWithWrongDataReturnFalse()
    {
        /** @var RolesService $service */
        $service = $this->app->make(RolesService::class);
        $this->assertFalse($service->delete(-1));
    }

    public function testCreateWithRealDataReturnRoleObject()
    {
        DB::beginTransaction();
        /** @var RolesService $service */
        $service = $this->app->make(RolesService::class);
        $this->assertNotNull($service);
        /** @var InteractionsTypeService $interactionTypeService */
        $interactionTypeService = $this->app->make(InteractionsTypeService::class);
        /** @var InteractionsRepository $interactionsRepository */
        $interactionsRepository = $this->app->make(InteractionsRepository::class);
        /** @var InteractionsTypeRepository $interactionsTypeRepository */
        $interactionsTypeRepository = $this->app->make(InteractionsTypeRepository::class);
        $this->assertNotNull($interactionTypeService);
        $interaction_create_type = $interactionTypeService->create([
           'name' => 'create',
           'description' => 'Create element'
        ]);
        $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

        $role_data = ['name' => 'Developer'];

        $this->assertTrue($service->checkNameAvailability($role_data['name']));

        $role = $service->create($role_data);
        $this->assertNotNull($role, $service->errors()->first());
        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals($role->name, $role_data['name']);
        $this->assertEquals($role->slug, Str::slug($role_data['name']));
        $this->assertNotNull($role->created_at);
        $this->assertNotNull($role->updated_at);
        $this->assertFalse($role->blocked);

        $another_role_data = [
            'name' => 'Superadministrator',
            'blocked' => true
        ];

        $this->assertFalse($service->checkNameAvailability($role_data['name']));

        $this->assertNotNull($role->interactions);
        $this->assertNotNull($role->users);
        $this->assertInstanceOf(Collection::class, $role->users);
        $this->assertEquals(0, $role->users->count());
        $this->assertInstanceOf(Collection::class, $role->interactions);
        $this->assertEquals(1, $role->interactions->count());

        $interactions = $interactionsRepository->all();
        $this->assertInstanceOf(Collection::class, $interactions);
        $this->assertEquals(1, $interactions->count());
        $register_interaction = $interactions->first();
        $this->assertNotNull($register_interaction);

        $create_interaction = $interactionsTypeRepository->findBySlug('create');
        $this->assertNotNull($create_interaction);

        $this->assertNotNull($create_interaction->roles);
        $this->assertInstanceOf(Collection::class, $create_interaction->roles);
        $this->assertEquals(1, $create_interaction->roles->count());
        $role_created = $create_interaction->roles->first();
        $this->assertNotNull($role_created);
        $this->assertEquals($role->id, $role_created->id);

        /******************************************************
         * Prevent duplicates
         ******************************************************/
        $duplicated_role = $service->create($role_data);
        $this->assertNull($duplicated_role);

        $another_role = $service->create($another_role_data);
        $this->assertNotNull($another_role, $service->errors()->first());
        $this->assertInstanceOf(Role::class, $another_role);
        $this->assertEquals($another_role->name, $another_role_data['name']);
        $this->assertEquals($another_role->slug, Str::slug($another_role_data['name']));
        $this->assertNotNull($another_role->created_at);
        $this->assertNotNull($another_role->updated_at);
        $this->assertTrue($another_role->blocked);


        DB::rollback();
    }

    public function testUpdateExistentObjectReturnTrue()
    {
        DB::beginTransaction();
        /** @var RolesService $service */
        $service = $this->app->make(RolesService::class);
        /** @var RolesRepository $roleRepository */
        $roleRepository = $this->app->make(RolesRepository::class);
        $this->assertNotNull($service);
        /** @var InteractionsTypeService $interactionTypeService */
        $interactionTypeService = $this->app->make(InteractionsTypeService::class);
        /** @var InteractionsRepository $interactionsRepository */
        $interactionsRepository = $this->app->make(InteractionsRepository::class);
        /** @var InteractionsTypeRepository $interactionsTypeRepository */
        $interactionsTypeRepository = $this->app->make(InteractionsTypeRepository::class);
        $this->assertNotNull($interactionTypeService);
        $interaction_create_type = $interactionTypeService->create([
            'name' => 'create',
            'description' => 'Create element'
        ]);
        $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

        $interaction_update_type = $interactionTypeService->create([
           'name' => 'update',
           'description' => 'Update element'
        ]);
        $this->assertNotNull($interaction_update_type);
        $this->assertInstanceOf(InteractionType::class, $interaction_update_type);

        $role_data = ['name' => 'Developer'];

        $this->assertTrue($service->checkNameAvailability($role_data['name']));

        $role = $service->create($role_data);
        $this->assertNotNull($role, $service->errors()->first());

        $another_role_data = ['name' => 'Standard','blocked' => true];

        $this->assertTrue($service->checkNameAvailability($another_role_data['name']));

        $standard_role = $service->create($another_role_data);
        $this->assertNotNull($standard_role, $service->errors()->first());
        $this->assertTrue($standard_role->blocked);

        $failed_update_data = [
            'name'=> 'Standard'
        ];
        /******************************************************************
         * trying to use update data from standard to update developer role
         ******************************************************************/

        $failed_updated = $service->update($role->id, $failed_update_data);
        $this->assertFalse($failed_updated);

        $success_update_data = [
            'name' => 'Superuser'
        ];
        $success_updated = $service->update($role->id, $success_update_data);
        $this->assertTrue($success_updated);

        $updated_role = $roleRepository->find($role->id);
        $updated_role->name = $success_update_data['name'];
        $updated_role->slug = Str::slug($success_update_data['name']);

        $interactions = $updated_role->interactions;
        $this->assertInstanceOf(Collection::class, $interactions);
        $this->assertEquals(2, $interactions->count());

        $register_interaction = $interactions->first();
        $this->assertNotNull($register_interaction);
        $this->assertInstanceOf(InteractionType::class, $register_interaction);
        $this->assertEquals($register_interaction->slug, 'create');

        $updated_interaction = $interactions->last();
        $this->assertNotNull($updated_interaction);
        $this->assertEquals($updated_interaction->slug, 'update');

        DB::rollback();
    }

    public function testDeleteExistentItemReturnTrue()
    {
        DB::beginTransaction();

        /** @var RolesService $service */
        $service = $this->app->make(RolesService::class);
        /** @var RolesRepository $roleRepository */
        $roleRepository = $this->app->make(RolesRepository::class);
        $this->assertNotNull($service);
        /** @var InteractionsTypeService $interactionTypeService */
        $interactionTypeService = $this->app->make(InteractionsTypeService::class);
        /** @var InteractionsRepository $interactionsRepository */
        $interactionsRepository = $this->app->make(InteractionsRepository::class);
        /** @var InteractionsTypeRepository $interactionsTypeRepository */
        $interactionsTypeRepository = $this->app->make(InteractionsTypeRepository::class);
        $this->assertNotNull($interactionTypeService);
        $interaction_create_type = $interactionTypeService->create([
            'name' => 'create',
            'description' => 'Create element'
        ]);
        $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

        $interaction_update_type = $interactionTypeService->create([
            'name' => 'update',
            'description' => 'Update element'
        ]);
        $this->assertNotNull($interaction_update_type);
        $this->assertInstanceOf(InteractionType::class, $interaction_update_type);

        $role_data = ['name' => 'Developer'];

        $this->assertTrue($service->checkNameAvailability($role_data['name']));

        $role = $service->create($role_data);
        $this->assertNotNull($role, $service->errors()->first());

        $another_role_data = ['name' => 'Standard','blocked' => true];

        $this->assertTrue($service->checkNameAvailability($another_role_data['name']));

        $standard_role = $service->create($another_role_data);
        $this->assertNotNull($standard_role, $service->errors()->first());
        $this->assertTrue($standard_role->blocked);

        $deleting_role_element = $service->delete($role->id);
        $this->assertTrue($deleting_role_element);

        $deleting_blocked_item_return_false = $service->delete($standard_role->id);
        $this->assertFalse($deleting_blocked_item_return_false);

        DB::rollback();
    }

    public function testAddAndRemoveClaim()
    {
        DB::beginTransaction();
        /** @var RolesService $rolesService */
        $rolesService = $this->app->make(RolesService::class);
        /** @var UsersService $service */
        $service = $this->app->make(UsersService::class);
        /** @var InteractionsTypeService $interactionTypeService */
        $interactionTypeService = $this->app->make(InteractionsTypeService::class);
        /** @var RolesRepository $rolesRepository */
        $rolesRepository = $this->app->make(RolesRepository::class);

        /** @var ClaimsService $claimsService */
        $claimsService = $this->app->make(ClaimsService::class);

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

        $claims = [
            ['name' => 'user add', 'identifier' => 'users.add'],
            ['name' => 'user edit','identifier' => 'users.edit'],
            ['name' => 'user index','identifier' => 'users.index'],
            ['name' => 'user list','identifier' => 'users.list'],
        ];
        $registered = [];
        foreach ($claims as $claim)
        {
            $claim_item = $claimsService->create($claim);
            $this->assertNotNull($claim_item);
            $this->assertTrue($rolesService->addClaim($role->id, [$claim_item->id]));
            $registered[] = $claim_item;
        }

        $role = $rolesRepository->find($role->id);
        $this->assertEquals(4, $role->claims->count());

        $rolesService->removeClaim($role->id, [$registered[0]->id, $registered[1]->id]);
        $role = $rolesRepository->find($role->id);
        $this->assertEquals(2,$role->claims->count());

        $claims_for_sync = [];
        foreach ($registered as $r)
        {
            $claims_for_sync[] = $r->id;
        }
        $rolesService->syncClaims($role->id, $claims_for_sync);

        $role = $rolesRepository->find($role->id);
        $this->assertEquals(4, $role->claims->count());

        DB::rollback();
    }

}