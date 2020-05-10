<?php


namespace Atxy2k\Essence\Tests;


use Atxy2k\Essence\Eloquent\Interaction;
use Atxy2k\Essence\Eloquent\InteractionType;
use Atxy2k\Essence\Eloquent\Role;
use Atxy2k\Essence\Interfaces\Services\RolesServiceInterface;
use Atxy2k\Essence\Repositories\InteractionsRepository;
use Atxy2k\Essence\Repositories\InteractionsTypeRepository;
use Atxy2k\Essence\Services\InteractionsTypeService;
use Atxy2k\Essence\Services\RolesService;
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
           'description' => 'Create interaction'
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

        DB::rollback();
    }

}