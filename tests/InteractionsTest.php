<?php


namespace Atxy2k\Essence\Tests;


use Atxy2k\Essence\Eloquent\InteractionType;
use Atxy2k\Essence\Interfaces\Services\InteractionsTypeServiceInterface;
use Atxy2k\Essence\Services\InteractionsTypeTypeService;
use DB;
use Illuminate\Support\Str;

class InteractionsTest extends TestCase
{
    public function testSimpleInstanceReturnInteractionsService()
    {
        /** @var InteractionsTypeTypeService $service */
        $service = $this->app->make(InteractionsTypeTypeService::class);
        $this->assertNotNull($service);
        $this->assertInstanceOf(InteractionsTypeTypeService::class, $service);
        $this->assertInstanceOf(InteractionsTypeServiceInterface::class, $service);
    }

    public function testCreateWithWrongDataReturnNull()
    {
        DB::beginTransaction();
        /** @var InteractionsTypeTypeService $service */
        $service = $this->app->make(InteractionsTypeTypeService::class);
        $this->assertNull($service->create([]));
        $this->assertNull($service->create([
            'name' => 'Name without description'
        ]));
        DB::rollback();
    }

    public function testCreateWithRealDataReturnInteraction()
    {
        DB::beginTransaction();
        /** @var InteractionsTypeTypeService $service */
        $service = $this->app->make(InteractionsTypeTypeService::class);
        $data =[
            'name' => 'Create',
            'description' => 'Register the create interaction with one object'
        ];
        $interaction = $service->create($data);
        $this->assertNotNull($interaction, $service->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction);
        $this->assertEquals($data['name'], $interaction->name);
        $this->assertEquals(Str::slug($data['name']), $interaction->slug);
        $this->assertEquals($data['description'], $interaction->description);
        $this->assertNotNull($interaction->created_at);
        $this->assertNotNull($interaction->updated_at);
        DB::beginTransaction();
    }

    public function testCreateWithRealDataReturnInteractionAndPreventDuplicates()
    {
        DB::beginTransaction();
        /** @var InteractionsTypeTypeService $service */
        $service = $this->app->make(InteractionsTypeTypeService::class);
        $data =[
            'name' => 'Update',
            'description' => 'Register the update interaction with one object'
        ];
        $interaction = $service->create($data);
        $this->assertNotNull($interaction, $service->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction);

        $another_interaction = $service->create($data);
        $this->assertNull($another_interaction);

        DB::beginTransaction();
    }



}