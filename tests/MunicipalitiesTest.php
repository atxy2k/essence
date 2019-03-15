<?php namespace Atxy2k\Essence\Tests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 14/03/2019
 * Time: 12:02
 */
use Atxy2k\Essence\Eloquent\Country;
use Atxy2k\Essence\Eloquent\State;
use Atxy2k\Essence\Eloquent\Municipality;
use Atxy2k\Essence\Repositories\MunicipalitiesRepository;
use Atxy2k\Essence\Validators\MunicipalitiesValidator;
use Atxy2k\Essence\Services\MunicipalitiesService;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MunicipalitiesTest extends TestCase
{
    public function testValidatorWithFakeDataReturnFalse() : void
    {
        /** @var MunicipalitiesValidator $municipalitiesValidator */
        $municipalitiesValidator = $this->app->make(MunicipalitiesValidator::class);
        $this->assertFalse($municipalitiesValidator->with([])->passes());
    }

    public function testValidatorWithRealDataReturnTrue() : void
    {
        DB::beginTransaction();
        /** @var MunicipalitiesValidator $municipalitiesValidator */
        $municipalitiesValidator = $this->app->make(MunicipalitiesValidator::class);
        $this->assertTrue($municipalitiesValidator->with([
            'name' => 'Calkini',
            'state_id' => State::create([ 'name' => 'Campeche', 'slug' => 'campeche', 'country_id' => Country::create([ 'name' => 'Mexico', 'slug' => 'mexico' ])->id ])->id
        ])->passes());
        DB::rollBack();
    }

    public function testCreateReturnMunicipality() : void
    {
        DB::beginTransaction();
        $country = Country::create([ 'name' => 'Mexico', 'slug' => 'mexico' ]);
        $state = State::create([ 'name' => 'Campeche', 'country_id' => $country->id , 'slug' => 'campeche' ]);
        /** @var MunicipalitiesService $municipalitiesService */
        $municipalitiesService = $this->app->make(MunicipalitiesService::class);
        $data = [
            'name' => 'Calkini',
            'state_id' => $state->id
        ];
        $municipality = $municipalitiesService->create($data);
        $this->assertNotNull($municipality, $municipalitiesService->errors()->first());
        $this->assertInstanceOf(Municipality::class, $municipality);
        $this->assertEquals($data['state_id'], $municipality->state_id);
        $this->assertNull($municipality->user);
        $this->assertNotNull($municipality->state);
        $this->assertNotNull($municipality->suburbs);
        $this->assertInstanceOf(State::class, $municipality->state);
        $this->assertInstanceOf(Collection::class, $municipality->suburbs);
        DB::rollBack();
    }

    public function testDelete() : void
    {
        DB::beginTransaction();
        $country = Country::create([ 'name' => 'Mexico', 'slug' => 'mexico' ]);
        $state = State::create([ 'name' => 'Campeche', 'country_id' => $country->id , 'slug' => 'campeche' ]);
        /** @var MunicipalitiesService $municipalitiesService */
        $municipalitiesService = $this->app->make(MunicipalitiesService::class);
        /** @var MunicipalitiesRepository $municipalitiesRepository */
        $municipalitiesRepository = $this->app->make(MunicipalitiesRepository::class);
        $data = [
            'name' => 'Calkini',
            'state_id' => $state->id
        ];
        $municipality = $municipalitiesService->create($data);
        $this->assertTrue($municipalitiesService->delete($municipality->id));
        $this->assertNull($municipalitiesRepository->find($state->id));
        DB::rollBack();
    }

    public function testPreventDuplicatesOnCreate() : void
    {
        DB::beginTransaction();
        $country = Country::create([ 'name' => 'Mexico', 'slug' => 'mexico' ]);
        $state = State::create([ 'name' => 'Campeche', 'country_id' => $country->id , 'slug' => 'campeche' ]);
        /** @var MunicipalitiesService $municipalitiesService */
        $municipalitiesService = $this->app->make(MunicipalitiesService::class);
        $data = [
            'name' => 'Calkini',
            'state_id' => $state->id
        ];
        $municipality = $municipalitiesService->create($data);
        $this->assertNotNull($municipality, $municipalitiesService->errors()->first());
        $this->assertNull($municipalitiesService->create($data));
        DB::rollBack();
    }

    public function testUpdate() : void
    {
        DB::beginTransaction();
        $country = Country::create([ 'name' => 'Mexico', 'slug' => 'mexico' ]);
        $state = State::create([ 'name' => 'Campeche', 'country_id' => $country->id , 'slug' => 'campeche' ]);
        /** @var MunicipalitiesService $municipalitiesService */
        $municipalitiesService = $this->app->make(MunicipalitiesService::class);
        /** @var MunicipalitiesRepository $municipalitiesRepository */
        $municipalitiesRepository = $this->app->make(MunicipalitiesRepository::class);
        $data = [
            'name' => 'Calkini',
            'state_id' => $state->id
        ];
        $municipality = $municipalitiesService->create($data);
        $update_data = [
            'name' => 'Campeche',
            'state_id' => $state->id
        ];
        $updated = $municipalitiesService->update($municipality->id, $update_data);
        $this->assertNotNull($updated);
        $this->assertInstanceOf(Municipality::class, $updated);
        $this->assertEquals($update_data['name'], $updated->name);
        $this->assertEquals(Str::slug($update_data['name']), $updated->slug);
        $this->assertEquals($update_data['state_id'], $updated->state_id);
        DB::rollBack();
    }

    public function testPreventDuplicateOnUpdate() : void
    {
        DB::beginTransaction();
        $country = Country::create([ 'name' => 'Mexico', 'slug' => 'mexico' ]);
        $state = State::create([ 'name' => 'Campeche', 'country_id' => $country->id , 'slug' => 'campeche' ]);
        /** @var MunicipalitiesService $municipalitiesService */
        $municipalitiesService = $this->app->make(MunicipalitiesService::class);

        $data_calkini = [
            'name' => 'Calkini',
            'state_id' => $state->id
        ];
        $calkini = $municipalitiesService->create($data_calkini);

        $data_champoton = [
            'name' => 'Champoton',
            'state_id' => $state->id
        ];
        $champoton = $municipalitiesService->create($data_champoton);
        $this->assertNull($municipalitiesService->update($champoton->id, $data_calkini));
        DB::rollBack();
    }

}
