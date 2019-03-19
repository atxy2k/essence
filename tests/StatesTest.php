<?php namespace Atxy2k\Essence\Tests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 14/03/2019
 * Time: 9:10
 */

use Atxy2k\Essence\Eloquent\Country;
use Atxy2k\Essence\Repositories\StatesRepository;
use Atxy2k\Essence\Services\CountriesService;
use Atxy2k\Essence\Services\StatesService;
use Atxy2k\Essence\Validators\StatesValidator;
use DB;
use Illuminate\Support\Str;

class StatesTest extends TestCase
{

    public function testValidatorWithFakeDataReturnFalse() : void
    {
        /** @var StatesValidator $statesValidator */
        $statesValidator = $this->app->make(StatesValidator::class);
        $this->assertFalse($statesValidator->with([])->passes());
    }

    public function testValidatorWithRealDataReturnTrue() : void
    {
        /** @var StatesValidator $statesValidator */
        $statesValidator = $this->app->make(StatesValidator::class);
        $this->assertFalse($statesValidator->with([ 'name' => 'Yucatan', 'country_id' => 1 ])->passes());
    }

    public function testCreateWithRealDataReturnState() : void
    {
        DB::beginTransaction();
        $data_country = [ 'name' => 'The united states' ];
        /** @var CountriesService $countriesService */
        $countriesService = $this->app->make(CountriesService::class);
        /** @var StatesService $statesService */
        $statesService = $this->app->make(StatesService::class);

        $country = $countriesService->create($data_country);
        $this->assertNotNull($country);
        $data = [
            'country_id' => $country->id,
            'name'       => 'Montana'
        ];
        $state = $statesService->create($data);
        $this->assertNotNull($state, $statesService->errors()->first());
        $this->assertEquals($country->id, $state->country_id);
        $this->assertEquals($data['name'], $state->name);
        $this->assertEquals(Str::slug($data['name']), $state->slug);
        $this->assertNotNull($state->country);
        $this->assertInstanceOf(Country::class, $state->country);
        DB::rollBack();
    }

    public function testDeleteWithFakeDataReturnFalse() : void
    {
        /** @var CountriesService $countriesService */
        $countriesService = $this->app->make(CountriesService::class);
        $this->assertFalse($countriesService->delete(99));
    }

    public function testDeleteReturnTrue() : void
    {
        DB::beginTransaction();
        $data_country = [ 'name' => 'The united states' ];
        /** @var CountriesService $countriesService */
        $countriesService = $this->app->make(CountriesService::class);
        /** @var StatesService $statesService */
        $statesService = $this->app->make(StatesService::class);
        /** @var StatesRepository $statesRepository */
        $statesRepository = $this->app->make(StatesRepository::class);

        $country = $countriesService->create($data_country);
        $this->assertNotNull($country);
        $data = [
            'country_id' => $country->id,
            'name'       => 'Montana'
        ];
        $state = $statesService->create($data);
        $this->assertNotNull($state, $statesService->errors()->first());
        $this->assertTrue($statesService->delete($state->id));
        $state_deleted = $statesRepository->find($state->id);
        $this->assertNull($state_deleted);
        DB::rollBack();
    }

    public function testUpdateData() : void
    {
        DB::beginTransaction();
        $data_country = [ 'name' => 'The united states' ];
        /** @var CountriesService $countriesService */
        $countriesService = $this->app->make(CountriesService::class);
        /** @var StatesService $statesService */
        $statesService = $this->app->make(StatesService::class);

        $country = $countriesService->create($data_country);
        $this->assertNotNull($country);
        $data = [
            'country_id' => $country->id,
            'name'       => 'Montana'
        ];
        $state = $statesService->create($data);
        $this->assertNotNull($state, $statesService->errors()->first());

        $updated_data = [
            'name' => 'Alabama',
            'country_id' => $country->id
        ];
        $updated = $statesService->update($state->id, $updated_data);
        $this->assertNotNull($updated, $statesService->errors()->first());

        DB::rollBack();
    }

    public function testPreventDuplicatesOnCreate() : void
    {
        DB::beginTransaction();
        $data_country = [ 'name' => 'The united states' ];
        /** @var CountriesService $countriesService */
        $countriesService = $this->app->make(CountriesService::class);
        /** @var StatesService $statesService */
        $statesService = $this->app->make(StatesService::class);

        $country = $countriesService->create($data_country);
        $this->assertNotNull($country);
        $data = [
            'country_id' => $country->id,
            'name'       => 'Montana'
        ];
        $state = $statesService->create($data);
        $this->assertNull($statesService->create($data));
        DB::rollBack();
    }

    public function testPreventDuplicatesOnUpdate() : void
    {
        DB::beginTransaction();
        $data_country = [ 'name' => 'The united states' ];
        /** @var CountriesService $countriesService */
        $countriesService = $this->app->make(CountriesService::class);
        /** @var StatesService $statesService */
        $statesService = $this->app->make(StatesService::class);

        $country = $countriesService->create($data_country);
        $this->assertNotNull($country);
        $data_arizona = [
            'country_id' => $country->id,
            'name'       => 'Montana'
        ];
        $data_alabama = [
            'country_id' => $country->id,
            'name'       => 'Alabama'
        ];
        $arizona = $statesService->create($data_arizona);
        $alabama = $statesService->create($data_alabama);
        $this->assertNotNull($arizona);
        $this->assertNotNull($alabama);
        $this->assertNull($statesService->update($alabama->id, $data_arizona));
        DB::rollBack();
    }

}
