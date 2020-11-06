<?php namespace Atxy2k\Essence\Tests;

use Atxy2k\Essence\Eloquent\InteractionType;
use Atxy2k\Essence\Repositories\InteractionsRepository;
use Atxy2k\Essence\Repositories\InteractionsTypeRepository;
use Atxy2k\Essence\Repositories\RolesRepository;
use Atxy2k\Essence\Services\InteractionsTypeService;
use Atxy2k\Essence\Services\RolesService;
use Atxy2k\Essence\Tests\Criteria\BlockedCriteria;
use Atxy2k\Essence\Tests\Criteria\FilterRoleCriteria;
use DB;

class CriteriaTest extends TestCase
{
    public function testConcatCriteria()
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
            'description' => 'Create element'
        ]);
        $this->assertNotNull($interaction_create_type, $interactionTypeService->errors()->first());
        $this->assertInstanceOf(InteractionType::class, $interaction_create_type);

        $developer_data = ['name' => 'Developer', 'blocked' => true];
        $developer_junior_data = ['name' => 'Developer junior'];
        $developer_senior_data = ['name' => 'Developer senior'];
        $developer_semi_senior_data = ['name' => 'Developer semi-senior'];

        $developer = $service->create($developer_data);
        $this->assertNotNull($developer);

        $junior = $service->create($developer_junior_data);
        $this->assertNotNull($junior);

        $senior = $service->create($developer_senior_data);
        $this->assertNotNull($senior);

        $semi_senior = $service->create($developer_semi_senior_data);
        $this->assertNotNull($semi_senior);

        /** @var RolesRepository $rolesRepository */
        $rolesRepository = $this->app->make(RolesRepository::class);
        $this->assertEquals(4, $rolesRepository->all()->count());
        $this->assertEquals(4, $rolesRepository->allWithCriteria()->count());

        $rolesRepository->pushCriteria(new FilterRoleCriteria('senior'));
        $this->assertEquals(4, $rolesRepository->all()->count());
        $this->assertEquals(2, $rolesRepository->allWithCriteria()->count());

        $rolesRepository->pushCriteria(new BlockedCriteria());
        $this->assertEquals(2, count($rolesRepository->getCriteria()));
        $this->assertEquals(4, $rolesRepository->all()->count());
        $this->assertEquals(0, $rolesRepository->allWithCriteria()->count());

        $rolesRepository->cleanCriteria();
        $this->assertEquals(0, count($rolesRepository->getCriteria()));
        $this->assertEquals(4, $rolesRepository->all()->count());
        $this->assertEquals(4, $rolesRepository->allWithCriteria()->count());

        $rolesRepository->addCriteria(new BlockedCriteria());
        $this->assertEquals(1, count($rolesRepository->getCriteria()));
        $this->assertEquals(4, $rolesRepository->all()->count());
        $this->assertEquals(1, $rolesRepository->allWithCriteria()->count());





        DB::rollback();
    }
}