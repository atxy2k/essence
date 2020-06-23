<?php namespace Atxy2k\Essence\Tests;

use Atxy2k\Essence\Interfaces\Services\ConfigurationsServiceInterface;
use Atxy2k\Essence\Repositories\ConfigurationsRepository;
use Atxy2k\Essence\Services\ConfigurationsService;
use DB;

class ConfigurationsTest extends TestCase
{

    public function testSimpleInstanceReturnConfigurationsService()
    {
        /** @var ConfigurationsService $service */
        $service = $this->app->make(ConfigurationsService::class);
        $this->assertNotNull($service);
        $this->assertInstanceOf(ConfigurationsService::class, $service);
        $this->assertInstanceOf(ConfigurationsServiceInterface::class, $service);
    }

    public function testNoneExistentItemReturnFalse()
    {
        /** @var ConfigurationsService $service */
        $service = $this->app->make(ConfigurationsService::class);
        $this->assertNull($service->getConfiguration('nonexistent_element'));
    }

    public function testRegisterElementReturnConfiguration()
    {
        DB::beginTransaction();
        /** @var ConfigurationsService $service */
        $service = $this->app->make(ConfigurationsService::class);
        /** @var ConfigurationsRepository $repository */
        $repository = $this->app->make(ConfigurationsRepository::class);
        $configuration = $service->setConfiguration('system_configuration', 3);
        $this->assertNotNull($configuration);
        $this->assertEquals('system_configuration', $configuration->key);
        $this->assertEquals(3, $configuration->value);
        $this->assertEquals(false, $configuration->encode);
        $this->assertNull($configuration->configurable_id);
        $this->assertNull($configuration->configurable_type);
        $this->assertEquals(1, $repository->countByKey('system_configuration'));

        $updatedConfiguration = $service->setConfiguration('system_configuration', 5);
        $this->assertNotNull($updatedConfiguration);
        $this->assertEquals(5, $updatedConfiguration->value);
        DB::rollback();
    }

    public function testRegisterEncodedElementReturnConfiguration()
    {
        DB::beginTransaction();
        /** @var ConfigurationsService $service */
        $service = $this->app->make(ConfigurationsService::class);
        /** @var ConfigurationsRepository $repository */
        $repository = $this->app->make(ConfigurationsRepository::class);
        $val = [
            'first_name' => 'ivan',
            'last_name' => 'alvarado'
        ];
        $configuration = $service->setConfiguration('another_system_configuration', $val, true);
        $this->assertNotNull($configuration);
        $this->assertEquals('another_system_configuration', $configuration->key);
        $this->assertEquals($val, $configuration->value);
        $this->assertEquals(true, $configuration->encode);
        $this->assertNull($configuration->configurable_id);
        $this->assertNull($configuration->configurable_type);
        $this->assertEquals(1, $repository->countByKey('another_system_configuration'));
        DB::rollback();
    }

    public function testDeleteConfigurationWithRealDataReturnTrue()
    {
        DB::beginTransaction();
        /** @var ConfigurationsService $service */
        $service = $this->app->make(ConfigurationsService::class);
        /** @var ConfigurationsRepository $repository */
        $repository = $this->app->make(ConfigurationsRepository::class);
        $configuration = $service->setConfiguration('system_configuration_to_delete', 3);
        $this->assertNotNull($configuration);
        $this->assertEquals('system_configuration_to_delete', $configuration->key);
        $this->assertEquals(3, $configuration->value);
        $this->assertEquals(false, $configuration->encode);
        $this->assertNull($configuration->configurable_id);
        $this->assertNull($configuration->configurable_type);
        $this->assertEquals(1, $repository->countByKey('system_configuration_to_delete'));

        $this->assertTrue($service->removeConfiguration('system_configuration_to_delete'));
        $this->assertEquals(0, $repository->countByKey('system_configuration_to_delete'));
        DB::rollback();
    }

}