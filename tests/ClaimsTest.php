<?php


namespace Atxy2k\Essence\Tests;
use Atxy2k\Essence\Eloquent\Claim;
use Atxy2k\Essence\Interfaces\Services\ClaimsServiceInterface;
use Atxy2k\Essence\Repositories\ClaimsRepository;
use Atxy2k\Essence\Services\ClaimsService;
use DB;
use Illuminate\Support\Str;

class ClaimsTest extends TestCase
{
    public function testCreateSimpleInstanceReturnClaimsService()
    {
        /** @var ClaimsService $service */
        $service = $this->app->make(ClaimsService::class);
        $this->assertNotNull($service);
        $this->assertInstanceOf(ClaimsService::class, $service);
        $this->assertInstanceOf(ClaimsServiceInterface::class, $service);
    }

    public function testCreateWithWrongDataReturnNull()
    {
        DB::beginTransaction();
        /** @var ClaimsService $service */
        $service = $this->app->make(ClaimsService::class);
        $this->assertNull($service->create([]));
        DB::rollback();
    }

    public function testUpdateWithWrongDataReturnFalse()
    {
        DB::beginTransaction();
        /** @var ClaimsService $service */
        $service = $this->app->make(ClaimsService::class);
        $this->assertFalse($service->update(-1,[]));
        DB::rollback();
    }

    public function testDeleteWithWrongDataReturnFalse()
    {
        DB::beginTransaction();
        /** @var ClaimsService $service */
        $service = $this->app->make(ClaimsService::class);
        $this->assertFalse($service->delete(-1));
        DB::rollback();
    }

    public function testCreateWithRealDataReturnClaimObject()
    {
        DB::beginTransaction();
        /** @var ClaimsService $service */
        $service = $this->app->make(ClaimsService::class);
        $data = [
            'name' => 'User list',
            'identifier' => 'users.list'
        ];

        $this->assertTrue($service->isIdentifierAvailability($data['identifier']));
        $item = $service->create($data);
        $this->assertNotNull($item);
        $this->assertInstanceOf(Claim::class, $item);
        $this->assertEquals($item->name, $data['name']);
        $this->assertEquals($item->identifier, $data['identifier']);
        $this->assertNull($item->description);
        $this->assertTrue($item->enabled);
        $this->assertNotNull($item->created_at);
        $this->assertNotNull($item->updated_at);

        $this->assertFalse($service->isIdentifierAvailability($data['identifier']));
        $this->assertTrue($service->isIdentifierAvailability($data['identifier'], $item->id));

        /**********************************************
         * Prevent duplicates
         **********************************************/
        $another_item = $service->create($data);
        $this->assertNull($another_item);

        DB::rollback();
    }

    public function testUpdateWithRealDataReturnTrue()
    {
        DB::beginTransaction();
        /** @var ClaimsService $service */
        $service = $this->app->make(ClaimsService::class);
        /** @var ClaimsRepository $claimsRepository */
        $claimsRepository = $this->app->make(ClaimsRepository::class);
        $data = [
            'name' => 'User list',
            'identifier' => 'users.list'
        ];
        $item = $service->create($data);
        $this->assertNotNull($item);

        $created_claim = [
            'name' => 'User create',
            'identifier' => 'users.create'
        ];

        $another_item = $service->create($created_claim);
        $this->assertNotNull($another_item);

        $updated_data = [
            'name' => 'List of users',
            'identifier' => 'users.list'
        ];

        $updated = $service->update($item->id, $updated_data);
        $this->assertTrue($updated);
        $item_updated = $claimsRepository->find($item->id);
        $this->assertNotNull($item_updated);
        $this->assertEquals($item_updated->name, $updated_data['name']);
        $this->assertEquals($item_updated->identifier, $updated_data['identifier']);
        $this->assertNull($item_updated->description);
        $this->assertTrue($item_updated->enabled);

        /**********************************************************
         * Prevent duplicates
         **********************************************************/

        $another_updated = [
            'name' => 'Another create user',
            'identifier' => 'users.create'
        ];

        $trying_update = $service->update($item->id, $another_updated);
        $this->assertFalse($trying_update);

        DB::rollback();
    }

    public function testDeleteExistentItemReturnTrue()
    {
        DB::beginTransaction();
        /** @var ClaimsService $service */
        $service = $this->app->make(ClaimsService::class);
        /** @var ClaimsRepository $claimsRepository */
        $claimsRepository = $this->app->make(ClaimsRepository::class);
        $data = [
            'name' => 'User list',
            'identifier' => 'users.list'
        ];
        $item = $service->create($data);
        $this->assertNotNull($item);

        $deleted = $service->delete($item->id);
        $this->assertTrue($deleted);

        $deleted_item = $claimsRepository->find($item->id);
        $this->assertNull($deleted_item);

        DB::rollback();
    }

    public function testDisableExistentElementReturnTrue()
    {
        DB::beginTransaction();
        /** @var ClaimsService $service */
        $service = $this->app->make(ClaimsService::class);
        /** @var ClaimsRepository $claimsRepository */
        $claimsRepository = $this->app->make(ClaimsRepository::class);
        $data = [
            'name' => 'User list',
            'identifier' => 'users.list'
        ];
        $item = $service->create($data);
        $this->assertNotNull($item);

        $this->assertTrue($service->disabled($item->id));

        $item = $claimsRepository->find($item->id);
        $this->assertNotNull($item);
        $this->assertFalse($item->enabled);

        $this->assertTrue($service->enabled($item->id));

        $item = $claimsRepository->find($item->id);
        $this->assertNotNull($item);
        $this->assertTrue($item->enabled);

        DB::rollback();
    }



}