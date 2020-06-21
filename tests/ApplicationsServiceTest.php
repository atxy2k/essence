<?php


namespace Atxy2k\Essence\Tests;

use Atxy2k\Essence\Eloquent\Application;
use Atxy2k\Essence\Repositories\ApplicationsRepository;
use Atxy2k\Essence\Services\ApplicationsService;
use DB;
use App;

class ApplicationsServiceTest extends TestCase
{
    /**********************************************************************
     * CREATE
     **********************************************************************/

    public function testCreateApplicationWithNameReturnApplicationObject()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        $data = ['name' => sprintf('App %s', uniqid())];
        $app = $applicationsService->create($data);
        $this->assertNotNull($app, $applicationsService->errors()->first());
        $this->assertInstanceOf(Application::class, $app);
        $this->assertNotNull($app->app_id);
        $this->assertNotNull($app->app_secret);
        $this->assertNotNull($app->name);
        $this->assertEmpty($app->description);
        $this->assertFalse($app->enabled);
        DB::rollBack();
    }

    public function testCreateApplicationWithNameAndDescriptionReturnApplicationObject()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $this->assertNotNull($app);
        $this->assertInstanceOf(Application::class, $app);
        $this->assertNotNull($app->app_id);
        $this->assertNotNull($app->app_secret);
        $this->assertNotNull($app->name);
        $this->assertNotEmpty($app->description);
        $this->assertEquals($data['description'], $app->description);
        $this->assertEquals($data['name'], $app->name);
        $this->assertNotNull($app->enabled);
        $this->assertFalse($app->enabled);
        DB::rollBack();
    }

    /**********************************************************************
     * UPDATE
     **********************************************************************/
    public function testUpdateNonexistentItemReturnFalse()
    {
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        $this->assertFalse($applicationsService->update(-1, [
            'name' => 'updated name'
        ]));
    }

    public function testUpdateExistentItemReturnTrue()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $update_data = [
            'name' => 'new name',
            'description' => 'new description',
            'app_id'    => 'this should be the same',
            'app_secret'    => 'this should be the same',
        ];
        $completed = $applicationsService->update($app->id, $update_data);
        $this->assertTrue($completed);
        $item_updated = $applicationsRepository->find($app->id);
        $this->assertNotNull($item_updated);
        $this->assertEquals($item_updated->id, $app->id);
        $this->assertEquals($item_updated->name, $update_data['name']);
        $this->assertEquals($item_updated->description, $update_data['description']);
        $this->assertNotEquals($item_updated->app_id, $update_data['app_id']);
        $this->assertNotEquals($item_updated->app_secret, $update_data['app_secret']);
        $this->assertEquals($item_updated->app_id, $app->app_id);
        $this->assertEquals($item_updated->app_secret, $app->app_secret);
        $this->assertFalse($app->enabled);
        DB::rollBack();
    }

    /**********************************************************************
     * DELETE
     **********************************************************************/
    public function testDeleteNonexistentElementReturnFalse()
    {
        /** @var ApplicationsService $applicationsRepository */
        $service = App::make(ApplicationsService::class);
        $this->assertFalse($service->delete(-1));
    }

    public function testDeleteExistentItemReturnTrue()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $this->assertTrue($applicationsService->delete($app->id));
        $existent = $applicationsRepository->find($app->id);
        $this->assertNull($existent);
        DB::rollBack();
    }

    /**********************************************************************
     * ENABLE
     **********************************************************************/

    public function testEnableWithNonexistentAppIdReturnFalse()
    {
        /** @var ApplicationsService $applicationsRepository */
        $service = App::make(ApplicationsService::class);
        $this->assertFalse($service->enable(['application_id' => -1]));
    }

    public function testEnableToExistentItemObjectReturnTrue()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $this->assertTrue($applicationsService->enable([ 'application_id' => $app->id ]));
        $updated_app = $applicationsRepository->find($app->id);
        $this->assertTrue($updated_app->enabled);
        DB::rollBack();
    }

    public function testEnableToExistentItemObjectButItIsCurrentlyEnabledReturnFalse()
    {
        DB::beginTransaction();
        /** @var ApplicationsService$applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $this->assertTrue($applicationsService->enable(['application_id' => $app->id]));
        $updated_app = $applicationsRepository->find($app->id);
        $this->assertTrue( $updated_app->enabled);
        $this->assertFalse($applicationsService->enable(['application_id' => $app->id]));
        DB::rollBack();
    }

    /**********************************************************************
     * DISABLE
     **********************************************************************/

    public function testDisableWithNonexistentAppIdReturnFalse()
    {
        /** @var ApplicationsService $applicationsService */
        $applicationsService = App::make(ApplicationsService::class);
        $this->assertFalse($applicationsService->disable([ 'application_id' => -1 ]));
    }

    public function testDisableToExistentItemObjectReturnTrue()
    {
        DB::beginTransaction();
        /** @var ApplicationsService$applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $this->assertTrue($applicationsService->enable(['application_id' => $app->id]));
        $updated_app = $applicationsRepository->find($app->id);
        $this->assertTrue($updated_app->enabled);
        $this->assertTrue($applicationsService->disable(['application_id' => $app->id]));
        $updated_app = $applicationsRepository->find($app->id);
        $this->assertFalse($updated_app->enabled);
        DB::rollBack();
    }

    public function testDisableToExistentItemObjectButItIsCurrentlyEnabledReturnFalse()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $this->assertTrue($applicationsService->enable(['application_id' => $app->id]));
        $updated_app = $applicationsRepository->find($app->id);
        $this->assertTrue($updated_app->enabled);
        $this->assertTrue($applicationsService->disable(['application_id' => $app->id]));
        $updated_app = $applicationsRepository->find($app->id);
        $this->assertFalse($updated_app->enabled);
        $this->assertFalse($applicationsService->disable(['application_id' => $updated_app->id]));
        DB::rollBack();
    }
}