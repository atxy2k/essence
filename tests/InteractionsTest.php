<?php


namespace Atxy2k\Essence\Tests;


use Atxy2k\Essence\Eloquent\Interaction;
use Atxy2k\Essence\Interfaces\Services\InteractionsServiceInterface;
use Atxy2k\Essence\Services\InteractionsService;
use DB;
use Illuminate\Support\Str;

class InteractionsTest extends TestCase
{
    public function testSimpleInstanceReturnInteractionsService()
    {
        /** @var InteractionsService $service */
        $service = $this->app->make(InteractionsService::class);
        $this->assertNotNull($service);
        $this->assertInstanceOf(InteractionsService::class, $service);
        $this->assertInstanceOf(InteractionsServiceInterface::class, $service);
    }

    public function testCreateWithWrongDataReturnNull()
    {
        DB::beginTransaction();
        /** @var InteractionsService $service */
        $service = $this->app->make(InteractionsService::class);
        $this->assertNull($service->create([]));
        $this->assertNull($service->create([
            'name' => 'Name without description'
        ]));
        DB::rollback();
    }

    public function testCreateWithRealDataReturnInteraction()
    {
        DB::beginTransaction();
        /** @var InteractionsService $service */
        $service = $this->app->make(InteractionsService::class);
        $data =[
            'name' => 'Create',
            'description' => 'Register the create interaction with one object'
        ];
        $interaction = $service->create($data);
        $this->assertNotNull($interaction, $service->errors()->first());
        $this->assertInstanceOf(Interaction::class, $interaction);
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
        /** @var InteractionsService $service */
        $service = $this->app->make(InteractionsService::class);
        $data =[
            'name' => 'Update',
            'description' => 'Register the update interaction with one object'
        ];
        $interaction = $service->create($data);
        $this->assertNotNull($interaction, $service->errors()->first());
        $this->assertInstanceOf(Interaction::class, $interaction);

        $another_interaction = $service->create($data);
        $this->assertNull($another_interaction);

        DB::beginTransaction();
    }



}