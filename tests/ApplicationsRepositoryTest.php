<?php


namespace Atxy2k\Essence\Tests;

use Atxy2k\Essence\Tests\TestCase;
use Atxy2k\Essence\Interfaces\Repositories\ApplicationsRepositoryInterface;
use Atxy2k\Essence\Repositories\ApplicationsRepository;
use App;
use DB;
use Atxy2k\Essence\Services\ApplicationsService;
use Atxy2k\Essence\Eloquent\Application;

class ApplicationsRepositoryTest extends TestCase
{

    /**********************************************************************
     * FIND BY APP ID
     **********************************************************************/

    public function testFindByAppIdWithNonexistentElementReturnNull()
    {
        DB::beginTransaction();
        /** @var ApplicationsRepositoryInterface $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $this->assertNotNull($applicationsRepository);
        $app = $applicationsRepository->findByAppId("this doesn't exists");
        $this->assertNull($app);
        DB::rollBack();
    }

    public function testFindByAppIdWithExistentItemReturnAppObject()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $this->assertNotNull($app, $applicationsService->errors()->first());
        $existent_app = $applicationsRepository->findByAppId($app->app_id);
        $this->assertNotNull($existent_app);
        $this->assertInstanceOf(Application::class, $existent_app);
        $this->assertEquals($existent_app->id, $app->id);
        DB::rollBack();
    }

    /**********************************************************************
     * FIND BY APP ID AND APP SECRET
     **********************************************************************/

    public function testFindByAppIdAndAppSecretWithExistentItemReturnAppObject()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $existent_app = $applicationsRepository->findByAppIdAndAppSecret($app->app_id, $app->app_secret);
        $this->assertNotNull($existent_app);
        $this->assertInstanceOf(Application::class, $existent_app);
        $this->assertEquals($existent_app->id, $app->id);
        $this->assertEquals($existent_app->app_id, $app->app_id);
        $this->assertEquals($existent_app->app_secret, $app->app_secret);
        DB::rollBack();
    }

    public function testFindByAppIdAndAppSecretWithExistentAppIdButFakeAppSecretReturnNull()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $existent_app = $applicationsRepository->findByAppIdAndAppSecret($app->app_id, uniqid());
        $this->assertNull($existent_app);
        DB::rollBack();
    }

    public function testFindByAppIdAndAppSecretWithExistentAppSecretButFakeAppIdReturnNull()
    {
        DB::beginTransaction();
        /** @var ApplicationsService$applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $existent_app = $applicationsRepository->findByAppIdAndAppSecret(uniqid(), $app->app_secret);
        $this->assertNull($existent_app);
        DB::rollBack();
    }

    /**********************************************************************
     * IS ENABLED
     **********************************************************************/

    public function testIsEnabledByAppIdWithNonexistentAppIdReturnFalse()
    {
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $response = $applicationsRepository->isEnabledByAppId("this doesn't exists");
        $this->assertFalse($response);
    }

    public function testIsEnabledByAppIdToExistentItemReturnFalseAfterCreated()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $this->assertFalse($applicationsRepository->isEnabledByAppId($app->app_id));
        DB::rollBack();
    }

    public function testIsEnabledByApplicationIdWithNonexistentAppIdReturnFalse()
    {
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $response = $applicationsRepository->isEnabledByAppId(-1);
        $this->assertFalse($response);
    }

    public function testIsEnabledByApplicationIdToExistentItemReturnFalseAfterCreated()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $this->assertFalse($applicationsRepository->isEnabledByAppId($app->id));
        DB::rollBack();
    }

    /**********************************************************************
     * ENABLE
     **********************************************************************/

    public function testEnableWithNonexistentAppIdReturnFalse()
    {
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $this->assertFalse($applicationsRepository->enable(-1));
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
        $this->assertTrue($applicationsRepository->enable($app->id));
        $updated_app = $applicationsRepository->find($app->id);
        $this->assertTrue($updated_app->enabled);
        DB::rollBack();
    }

    public function testEnableToExistentItemObjectButItIsCurrentlyEnabledReturnFalse()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $this->assertTrue($applicationsRepository->enable($app->id));
        $updated_app = $applicationsRepository->find($app->id);
        $this->assertTrue($updated_app->enabled);
        $this->assertFalse($applicationsRepository->enable($app->id));
        DB::rollBack();
    }

    /**********************************************************************
     * ENABLE FROM APPLICATION ID
     **********************************************************************/

    public function testEnableFromAppIdWithNonexistentAppIdReturnFalse()
    {
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $this->assertFalse($applicationsRepository->enableFromApplicationId("this doesn't exists"));
    }

    public function testEnableFromApplicationIdToExistentItemObjectReturnTrue()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $this->assertTrue($applicationsRepository->enableFromApplicationId($app->app_id));
        $updated_app = $applicationsRepository->find($app->id);
        $this->assertTrue($updated_app->enabled);
        DB::rollBack();
    }

    public function testEnableFromApplicationIdToExistentItemObjectButItIsCurrentlyEnabledReturnFalse()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $this->assertTrue($applicationsRepository->enableFromApplicationId($app->app_id));
        $updated_app = $applicationsRepository->find($app->id);
        $this->assertTrue($updated_app->enabled);
        $this->assertFalse($applicationsRepository->enableFromApplicationId($app->app_id));
        DB::rollBack();
    }


    /**********************************************************************
     * DISABLE
     **********************************************************************/

    public function testDisableWithNonexistentAppIdReturnFalse()
    {
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $this->assertFalse($applicationsRepository->disable(-1));
    }

    public function testDisableToExistentItemObjectReturnTrue()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $this->assertTrue($applicationsRepository->enable($app->id));
        $updated_app = $applicationsRepository->find($app->id);
        $this->assertTrue($updated_app->enabled);
        $this->assertTrue($applicationsRepository->disable($app->id));
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
        $this->assertTrue($applicationsRepository->enable($app->id));
        $updated_app = $applicationsRepository->find($app->id);
        $this->assertTrue($updated_app->enabled);
        $this->assertTrue($applicationsRepository->disable($app->id));
        $updated_app = $applicationsRepository->find($app->id);
        $this->assertFalse($updated_app->enabled);
        $this->assertFalse($applicationsRepository->disable($updated_app->id));
        DB::rollBack();
    }

    /**********************************************************************
     * DISABLE FROM APPLICATION ID
     **********************************************************************/

    public function testDisableFromAppIdWithNonexistentAppIdReturnFalse()
    {
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $this->assertFalse($applicationsRepository->disableFromApplicationId("this doesn't exists"));
    }

    public function testDisableFromApplicationIdToExistentItemObjectReturnTrue()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $this->assertTrue($applicationsRepository->enableFromApplicationId($app->app_id));
        $updated_app = $applicationsRepository->find($app->id);
        $this->assertTrue($updated_app->enabled);
        $this->assertTrue($applicationsRepository->disableFromApplicationId($app->app_id));
        $updated_app = $applicationsRepository->find($app->id);
        $this->assertFalse($updated_app->enabled);
        DB::rollBack();
    }

    public function testDisableFromApplicationIdToExistentItemObjectButItIsCurrentlyEnabledReturnFalse()
    {
        DB::beginTransaction();
        /** @var ApplicationsService $applicationsService*/
        $applicationsService = App::make(ApplicationsService::class);
        /** @var ApplicationsRepository $applicationsRepository */
        $applicationsRepository = App::make(ApplicationsRepository::class);
        $data = ['name' => sprintf('App %s', uniqid()), 'description' => 'Application test'];
        $app = $applicationsService->create($data);
        $this->assertFalse($applicationsRepository->disableFromApplicationId($app->app_id));
        DB::rollBack();
    }


}