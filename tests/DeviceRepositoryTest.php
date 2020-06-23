<?php


namespace Atxy2k\Essence\Tests;


use Atxy2k\Essence\Repositories\DevicesRepository;
use DB;
use App;

class DeviceRepositoryTest extends TestCase
{
    public function testFindByIdentifierWithWrongIdentifierReturnNull()
    {
        DB::beginTransaction();
        /** @var DevicesRepository $devicesRepository */
        $devicesRepository = App::make(DevicesRepository::class);
        $this->assertNotNull($devicesRepository);
        $this->assertNull($devicesRepository->find(uniqid()));
        DB::rollBack();
    }
}