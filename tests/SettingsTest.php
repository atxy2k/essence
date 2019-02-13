<?php namespace Atxy2k\Essence\Tests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 14:53
 */

use Atxy2k\Essence\Exceptions\Essence\UnexpectedException;
use DB;
use Atxy2k\Essence\Repositories\SettingsRepository;
use Atxy2k\Essence\Eloquent\Setting;

class SettingsTest extends TestCase
{

    public function testFindRandomKeyAndReturnNull()
    {
        $settingsRepository = $this->app->make(SettingsRepository::class);
        $fake_key = uniqid();
        /** @var Setting $setting */
        $setting = $settingsRepository->findByKey($fake_key);
        $this->assertNull($setting);
    }

    public function testCreateValueAndReturnObject()
    {
        DB::beginTransaction();
        $settingsRepository = $this->app->make(SettingsRepository::class);
        /** @var Setting $setting */
        $setting = $settingsRepository->setValue('system_layout', 'metronic');
        $this->assertNotNull($setting);
        $this->assertInstanceOf(Setting::class, $setting);
        $this->assertEquals($setting->key, 'system_layout');
        $this->assertFalse($setting->encode);
        $this->assertEquals($setting->value, 'metronic');
        $this->assertTrue(is_string($setting->value));
        $this->assertNotNull($setting->created_at);
        $this->assertNotNull($setting->updated_at);
        $this->assertNull($setting->user_id);
        DB::rollBack();
    }

    public function testCreateEncodedValueAndReturnObjectWithArray()
    {
        DB::beginTransaction();
        $settingsRepository = $this->app->make(SettingsRepository::class);
        /** @var Setting $setting */
        $setting = $settingsRepository->setEncodedValue('other_settings', [
            'id' => 1,
            'first_name' => 'Ivan',
            'last_name'  => 'Alvarado'
        ]);
        $this->assertNotNull($setting);
        $this->assertInstanceOf(Setting::class, $setting);
        $this->assertTrue($setting->encode);
        $this->assertEquals($setting->key, 'other_settings');
        $this->assertTrue(is_array($setting->value));
        $this->assertNotNull($setting->created_at);
        $this->assertNotNull($setting->updated_at);
        $data = $setting->value;
        $this->assertTrue(array_has($data,'id'));
        $this->assertTrue(array_has($data,'first_name'));
        $this->assertTrue(array_has($data,'last_name'));
        $this->assertEquals($data['id'], 1);
        $this->assertEquals($data['first_name'], 'Ivan');
        $this->assertEquals($data['last_name'], 'Alvarado');
        DB::rollBack();
    }

//    public function testCreateCustomUserConfig()
//    {
//        DB::beginTransaction();
//        $key = 'system_layout';
//        $user_id = 1;
//        $settingsRepository = $this->app->make(SettingsRepository::class);
//        /** @var Setting $setting */
//        $setting = $settingsRepository->setValue($key, 'metronic',$user_id);
//        $this->assertNotNull($setting);
//        $this->assertInstanceOf(Setting::class, $setting);
//        $this->assertEquals($setting->key, 'system_layout');
//        $this->assertFalse($setting->encode);
//        $this->assertEquals($setting->value, 'metronic');
//        $this->assertTrue(is_string($setting->value));
//        $this->assertNotNull($setting->created_at);
//        $this->assertNotNull($setting->updated_at);
//    }

    public function testFindValueAfterSavedIt()
    {
        DB::beginTransaction();
        $key = 'system_layout';
        $settingsRepository = $this->app->make(SettingsRepository::class);
        /** @var Setting $setting */
        $setting = $settingsRepository->setValue($key, 'metronic');
        $this->assertNotNull($setting);

        $findIt = $settingsRepository->getValue($key);
        $this->assertNotNull($findIt);
        $this->assertTrue(is_string($findIt));
        $this->assertEquals($findIt, 'metronic');
        DB::rollBack();
    }

    public function testFindArrayValueAfterSavedIt()
    {
        DB::beginTransaction();
        $key = 'system_layout';
        $settingsRepository = $this->app->make(SettingsRepository::class);
        /** @var Setting $setting */
        $setting = $settingsRepository->setEncodedValue($key, [
            'id' => 1,
            'first_name' => 'ivan'
        ]);
        $this->assertNotNull($setting);
        $findIt = $settingsRepository->getValue($key);
        $this->assertNotNull($findIt);
        $this->assertTrue(is_array($findIt));
        $this->assertEquals($findIt, [ 'id' => 1, 'first_name' => 'ivan' ]);
        DB::rollBack();
    }

}
