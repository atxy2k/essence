<?php


namespace Atxy2k\Essence\Tests;
use Atxy2k\Essence\Constants\DeviceTypes;
use Atxy2k\Essence\Eloquent\Device;
use Atxy2k\Essence\Eloquent\DeviceLocationHistory;
use Atxy2k\Essence\Repositories\DeviceAccessHistoryRepository;
use Atxy2k\Essence\Repositories\DeviceLocationHistoryRepository;
use Atxy2k\Essence\Repositories\DevicesRepository;
use DB;
use Atxy2k\Essence\Services\DevicesService;
use App;

class DeviceServiceTest extends TestCase
{

    public function testCreateWithWrongDataReturnNull()
    {
        DB::beginTransaction();
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        $this->assertNull($devicesService->create([]));
        DB::rollBack();
    }

    public function testCreateWithRealDataReturnDeviceObject()
    {
        DB::beginTransaction();
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        $data = [
            'id' => uniqid(),
            'name'       => 'test name',
            'type'       => DeviceTypes::ANDROID
        ];
        $item = $devicesService->create($data);
        $this->assertNotNull($item, json_encode($devicesService->errors()));
        $this->assertInstanceOf(Device::class, $item);
        $this->assertEquals($item->name, $data['name']);
        $this->assertEquals($item->label, $data['name']);
        $this->assertEquals($item->id, $data['id']);
        $this->assertNull($item->version);
        $this->assertNull($item->os);
        $this->assertNotNull($item->last_connection);
        $this->assertNull($item->user_id);
        $this->assertFalse( $item->enabled);
        DB::rollBack();
    }

    public function testCreatingAutoActivatedItemReturnActivatedItem()
    {
        DB::beginTransaction();
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        $data = [
            'id' => uniqid(),
            'name'       => 'test name',
            'type'       => DeviceTypes::BROWSER
        ];
        $item = $devicesService->create($data);
        $this->assertNotNull($item, json_encode($devicesService->errors()));
        $this->assertInstanceOf(Device::class, $item);
        $this->assertEquals($item->name, $data['name']);
        $this->assertEquals($item->label, $data['name']);
        $this->assertEquals($item->id, $data['id']);
        $this->assertNull($item->version);
        $this->assertNull($item->os);
        $this->assertNotNull($item->last_connection);
        $this->assertNull($item->user_id);
        $this->assertTrue( $item->enabled);
        DB::rollBack();
    }



    public function testPreventDuplicatedReturnTheOldObject()
    {
        DB::beginTransaction();
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        $data = [
            'id' => uniqid(),
            'name'       => 'test name',
            'type'       => DeviceTypes::WINDOWS_UNIVERSAL_APPLICATION
        ];
        $item = $devicesService->create($data);
        $this->assertNotNull($item);

        $another_data = [
            'id' => $data['id'],
            'name'       => 'test name 2',
            'type'       => DeviceTypes::WINDOWS_UNIVERSAL_APPLICATION
        ];
        $them_same_object = $devicesService->create($another_data);
        $this->assertNotNull($them_same_object);
        $this->assertInstanceOf(Device::class, $them_same_object);
        $this->assertEquals($them_same_object->name, $data['name']);
        $this->assertEquals($them_same_object->id, $data['id']);
        $this->assertNull($them_same_object->version);
        $this->assertNull($them_same_object->os);
        $this->assertNotNull($them_same_object->last_connection);
        $this->assertNull($them_same_object->user_id);

        DB::rollBack();
    }

    public function testDeleteItem()
    {
        DB::beginTransaction();
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        /** @var DevicesRepository $devicesRepository */
        $devicesRepository = $this->app->make(DevicesRepository::class);
        $data = [
            'id' => uniqid(),
            'name'       => 'test name',
            'type'       => DeviceTypes::WINDOWS_UNIVERSAL_APPLICATION
        ];
        $item = $devicesService->create($data);
        $this->assertNotNull($item);

        $another_data = [
            'id'         => $data['id'],
            'name'       => 'test name 2',
            'type'       => DeviceTypes::IOS
        ];
        $them_same_object = $devicesService->create($another_data);
        $this->assertNotNull($them_same_object);
        $this->assertTrue($devicesService->delete($them_same_object->id));
        $item = $devicesRepository->find($item->id);
        $this->assertNull($item);

        DB::rollBack();
    }

    public function testUpdateLastAccessWithNonexistentElementReturnFalse()
    {
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        $this->assertFalse($devicesService->updateLastAccess(-1));
    }

    public function testUpdateLastAccessWithRealDataReturnTrueAndSaveElement()
    {
        DB::beginTransaction();
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        $data = [
            'id' => uniqid(),
            'name'       => 'test name',
            'type'       => DeviceTypes::MOBILE
        ];
        $item = $devicesService->create($data);
        $this->assertNotNull($item);

        $this->assertTrue($devicesService->updateLastAccess($item->id), $devicesService->errors()->first());

        /** @var DeviceAccessHistoryRepository $deviceAccessHistoryRepository */
        $deviceAccessHistoryRepository = $this->app->make(DeviceAccessHistoryRepository::class);
        $history = $deviceAccessHistoryRepository->allByDevice($item->id);
        $this->assertNotNull($history);
        $this->assertEquals(1, $history->count());
        DB::rollBack();
    }

    public function testRegisterLocationHistoryWithFakeDataReturnFalse()
    {
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        $this->assertNull($devicesService->registerLocationHistory([
            'device_id' => -1,
            'latitude'  => '-91.6712',
            'longitude'  => '-91.6712',
        ]));
    }

    public function testRegisterLocationHistoryWithRealDataReturnTrue()
    {
        DB::beginTransaction();
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        $data = [
            'id' => uniqid(),
            'name'       => 'test name',
            'type'       => DeviceTypes::MOBILE
        ];
        $item = $devicesService->create($data);
        $this->assertNotNull($item);

        $this->assertNotNull($devicesService->registerLocationHistory([
            'device_id' => $item->id,
            'latitude'  => '-91.6712',
            'longitude'  => '-91.6712',
        ]), $devicesService->errors()->first());

        /** @var DeviceLocationHistoryRepository $deviceLocationHistoryRepository */
        $deviceLocationHistoryRepository = $this->app->make(DeviceLocationHistoryRepository::class);
        $results = $deviceLocationHistoryRepository->findByDate(now());
        $this->assertNotNull($results);
        $this->assertEquals(1, $results->count());
        DB::rollBack();
    }

    public function testRegisterLocationHistoryWithRealAndRegisteringAccessHistoryComplete()
    {
        DB::beginTransaction();
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        $data = [
            'id' => uniqid(),
            'name'       => 'test name',
            'type'       => DeviceTypes::MOBILE
        ];
        $item = $devicesService->create($data);
        $this->assertNotNull($item);

        $location = $devicesService->registerLocationHistory([
            'device_id' => $item->id,
            'latitude'  => '-91.6712',
            'longitude'  => '-91.6712',
        ]);
        $this->assertNotNull($location, $devicesService->errors()->first());

        /** @var DeviceLocationHistoryRepository $deviceLocationHistoryRepository */
        $deviceLocationHistoryRepository = $this->app->make(DeviceLocationHistoryRepository::class);
        $results = $deviceLocationHistoryRepository->findByDate(now());
        $this->assertNotNull($results);
        $this->assertEquals(1, $results->count());

        $this->assertTrue($devicesService->updateLastAccess($item->id,[
            'location_id' => $location->id
        ]), $devicesService->errors()->first());

        /** @var DeviceAccessHistoryRepository $deviceAccessHistoryRepository */
        $deviceAccessHistoryRepository = $this->app->make(DeviceAccessHistoryRepository::class);
        $items = $deviceAccessHistoryRepository->allByDevice($item->id);
        $this->assertEquals(1, $items->count());
        foreach ( $items as $log )
        {
            $this->assertEquals($location->id, $log->device_location_history_id);
            $this->assertInstanceOf(DeviceLocationHistory::class, $log->location);
        }
        DB::rollBack();
    }

    public function testEnableWithFakeDataReturnFalse()
    {
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        $this->assertFalse($devicesService->enable([
            'device_id' => -1
        ]));
    }

    public function testEnableWithRealDataReturnTrue()
    {
        DB::beginTransaction();
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        /** @var DevicesRepository $devicesRepository */
        $devicesRepository = $this->app->make(DevicesRepository::class);
        $data = [
            'id' => uniqid(),
            'name'       => 'test name',
            'type'       => DeviceTypes::MOBILE
        ];
        $item = $devicesService->create($data);
        $this->assertNotNull($item);
        $this->assertTrue($devicesService->enable(['device_id' => $item->id]),
        $devicesService->errors()->first());
        $existent = $devicesRepository->find($item->id);
        $this->assertTrue($existent->enabled);
        DB::rollBack();
    }

    public function testTryEnableWithRealToAlreadyEnableDataReturnFalse()
    {
        DB::beginTransaction();
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        /** @var DevicesRepository $devicesRepository */
        $devicesRepository = $this->app->make(DevicesRepository::class);
        $data = [
            'id' => uniqid(),
            'name'       => 'test name',
            'type'       => DeviceTypes::MOBILE
        ];
        $item = $devicesService->create($data);
        $this->assertNotNull($item);
        $this->assertTrue($devicesService->enable(['device_id' => $item->id]), $devicesService->errors()->first());
        $existent = $devicesRepository->find($item->id);
        $this->assertTrue($existent->enabled);
        $this->assertFalse($devicesService->enable(['device_id' => $existent->id]), json_encode($devicesService->errors()));
        DB::rollBack();
    }

    public function testDisableWithFakeDataReturnFalse()
    {
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        $this->assertFalse($devicesService->disable([
            'device_id' => -1
        ]));
    }

    public function testTryDisableWithRealToAlreadyDisableDataReturnFalse()
    {
        DB::beginTransaction();
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        /** @var DevicesRepository $devicesRepository */
        $devicesRepository = $this->app->make(DevicesRepository::class);
        $data = [
            'id' => uniqid(),
            'name'       => 'test name',
            'type'       => DeviceTypes::MOBILE
        ];
        $item = $devicesService->create($data);
        $this->assertNotNull($item);
        $this->assertFalse($devicesService->disable(['device_id' => $item->id]));
        DB::rollBack();
    }

    public function testTryingDisableItemWithRealReturnTrue()
    {
        DB::beginTransaction();
        /** @var DevicesService $devicesService */
        $devicesService = $this->app->make(DevicesService::class);
        /** @var DevicesRepository $devicesRepository */
        $devicesRepository = $this->app->make(DevicesRepository::class);
        $data = [
            'id' => uniqid(),
            'name'       => 'test name',
            'type'       => DeviceTypes::MOBILE
        ];
        $item = $devicesService->create($data);
        $this->assertNotNull($item);
        $this->assertTrue($devicesService->enable(['device_id' => $item->id]));
        $existent = $devicesRepository->find($item->id);
        $this->assertTrue($existent->enabled);

        $this->assertTrue($devicesService->disable(['device_id'=> $item->id]));
        $existent = $devicesRepository->find($item->id);
        $this->assertFalse($existent->enabled);
        DB::rollBack();
    }

}