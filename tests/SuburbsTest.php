<?php namespace Atxy2k\Essence\Tests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 14/03/2019
 * Time: 12:49
 */
use DB;
use Illuminate\Support\Str;
use Atxy2k\Essence\Services\SuburbsService;
use Atxy2k\Essence\Repositories\SuburbsRepository;
use Atxy2k\Essence\Eloquent\Suburb;
use Atxy2k\Essence\Eloquent\Country;
use Atxy2k\Essence\Eloquent\Municipality;
use Atxy2k\Essence\Eloquent\State;
use Atxy2k\Essence\Validators\SuburbsValidator;

class SuburbsTest extends TestCase
{
    public function testValidatorWithFakeDataReturnFalse() : void
    {
        /** @var SuburbsValidator $suburbsValidator */
        $suburbsValidator = $this->app->make(SuburbsValidator::class);
        $this->assertFalse($suburbsValidator->with([])->passes());
    }

    public function testValidatorWithRealDataReturnTrue() : void
    {
        DB::beginTransaction();
        /** @var SuburbsValidator $suburbsValidator */
        $suburbsValidator = $this->app->make(SuburbsValidator::class);
        $country = Country::create([ 'name' => 'Mexico', 'slug' => 'mexico' ]);
        $state = State::create([ 'name' => 'Campeche', 'slug' => 'campeche', 'country_id' => $country->id ]);
        $municipality = Municipality::create([
            'name' => 'Calkini',
            'slug' => 'calkini',
            'state_id' => $state->id
        ]);
        $suburb = [
            'name' => 'San roman',
            'municipality_id' => $municipality->id
        ];
        $this->assertFalse($suburbsValidator->with($suburb)->passes());
        DB::rollBack();
    }

    public function testCreateReturnSuburb() : void
    {
        DB::beginTransaction();
        $country = Country::create([ 'name' => 'Mexico', 'slug' => 'mexico' ]);
        $state = State::create([ 'name' => 'Campeche', 'slug' => 'campeche', 'country_id' => $country->id ]);
        $municipality = Municipality::create([ 'name' => 'Calkini', 'slug' => 'calkini', 'state_id' => $state->id ]);
        $suburb_data = [
            'name'              => 'San roman',
            'municipality_id'   => $municipality->id,
            'postal_code'       => 24070,
            'type'              => 'Colonia'
        ];
        /** @var SuburbsService $suburbsService */
        $suburbsService = $this->app->make(SuburbsService::class);
        $suburb = $suburbsService->create($suburb_data);
        $this->assertNotNull($suburb, $suburbsService->errors()->first());
        DB::rollBack();
    }

    public function testDelete() : void
    {
        DB::beginTransaction();
        $country = Country::create([ 'name' => 'Mexico', 'slug' => 'mexico' ]);
        $state = State::create([ 'name' => 'Campeche', 'slug' => 'campeche', 'country_id' => $country->id ]);
        $municipality = Municipality::create([ 'name' => 'Calkini', 'slug' => 'calkini', 'state_id' => $state->id ]);
        $suburb_data = [
            'name'              => 'San roman',
            'municipality_id'   => $municipality->id,
            'postal_code'       => 24070,
            'type'              => 'Colonia'
        ];
        /** @var SuburbsService $suburbsService */
        $suburbsService = $this->app->make(SuburbsService::class);
        /** @var SuburbsRepository $suburbsRepository */
        $suburbsRepository = $this->app->make(SuburbsRepository::class);
        $suburb = $suburbsService->create($suburb_data);
        $this->assertTrue($suburbsService->delete($suburb->id));
        $this->assertNull($suburbsRepository->find($suburb->id));
        DB::rollBack();
    }

    public function testPreventDuplicatesOnCreate() : void
    {
        DB::beginTransaction();
        $country = Country::create([ 'name' => 'Mexico', 'slug' => 'mexico' ]);
        $state = State::create([ 'name' => 'Campeche', 'slug' => 'campeche', 'country_id' => $country->id ]);
        $municipality = Municipality::create([ 'name' => 'Calkini', 'slug' => 'calkini', 'state_id' => $state->id ]);
        $suburb_data = [
            'name'              => 'San roman',
            'municipality_id'   => $municipality->id,
            'postal_code'       => 24070,
            'type'              => 'Colonia'
        ];
        /** @var SuburbsService $suburbsService */
        $suburbsService = $this->app->make(SuburbsService::class);
        $suburb = $suburbsService->create($suburb_data);
        $this->assertNotNull($suburb, $suburbsService->errors()->first());
        $this->assertNull($suburbsService->create($suburb_data));
        DB::rollBack();
    }

    public function testUpdate() : void
    {
        DB::beginTransaction();
        $country = Country::create([ 'name' => 'Mexico', 'slug' => 'mexico' ]);
        $state = State::create([ 'name' => 'Campeche', 'slug' => 'campeche', 'country_id' => $country->id ]);
        $municipality = Municipality::create([ 'name' => 'Calkini', 'slug' => 'calkini', 'state_id' => $state->id ]);
        $suburb_data = [
            'name'              => 'San roman',
            'municipality_id'   => $municipality->id,
            'postal_code'       => 24070,
            'type'              => 'Colonia'
        ];
        /** @var SuburbsService $suburbsService */
        $suburbsService = $this->app->make(SuburbsService::class);
        $suburb = $suburbsService->create($suburb_data);
        $this->assertNotNull($suburb);
        $updated_data = [
            'name'              => 'Huanal',
            'municipality_id'   => $municipality->id,
            'postal_code'       => 24000,
            'type'              => 'Asentamiento'
        ];
        $updated = $suburbsService->update($suburb->id, $updated_data);
        $this->assertNotNull($updated, $suburbsService->errors()->first());
        DB::rollBack();
    }

    public function testPreventDuplicateOnUpdate() : void
    {
        DB::beginTransaction();
        $country = Country::create([ 'name' => 'Mexico', 'slug' => 'mexico' ]);
        $state = State::create([ 'name' => 'Campeche', 'slug' => 'campeche', 'country_id' => $country->id ]);
        $municipality = Municipality::create([ 'name' => 'Calkini', 'slug' => 'calkini', 'state_id' => $state->id ]);
        /** @var SuburbsService $suburbsService */
        $suburbsService = $this->app->make(SuburbsService::class);

        $data_san_roman = [
            'name'              => 'San roman',
            'municipality_id'   => $municipality->id,
            'postal_code'       => 24070,
            'type'              => 'Colonia'
        ];
        $san_roman = $suburbsService->create($data_san_roman);

        $data_huanal = [
            'name'              => 'Huanal',
            'municipality_id'   => $municipality->id,
            'postal_code'       => 24070,
            'type'              => 'Asentamiento'
        ];
        $huanal = $suburbsService->create($data_huanal);

        $this->assertNotNull($san_roman);
        $this->assertNotNull($huanal);

        $this->assertNull($suburbsService->update($huanal->id, $data_san_roman));

        DB::rollBack();
    }

}
