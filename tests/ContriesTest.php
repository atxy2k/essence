<?php namespace Atxy2k\Essence\Tests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 13/03/2019
 * Time: 20:55
 */

use Atxy2k\Essence\Repositories\CountriesRepository;
use Atxy2k\Essence\Services\CountriesService;
use Atxy2k\Essence\Validators\CountriesValidator;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ContriesTest extends TestCase
{

    public function testCreateReturnCountryItem() : void
    {
       DB::beginTransaction();
       $data = [ 'name' => 'The united states' ];
       /** @var CountriesService $countriesService */
       $countriesService = $this->app->make(CountriesService::class);
       $country = $countriesService->create($data);
       $this->assertNotNull($country, $countriesService->errors()->first());
       $this->assertEquals($country->name, $data['name']);
       $this->assertEquals($country->slug, Str::slug($data['name']));
       $this->assertNull($country->user_id);
       $this->assertNotNull($country->created_at);
       $this->assertInstanceOf(Carbon::class,$country->created_at);
       $this->assertNotNull($country->updated_at);
       $this->assertInstanceOf(Carbon::class,$country->updated_at);
       $this->assertNotNull($country->states);
       $this->assertInstanceOf(Collection::class, $country->states);
       DB::rollBack();
    }

    public function testPreventDuplicatedItems() : void
    {
        DB::beginTransaction();
        $data = [ 'name' => 'The united states' ];
        /** @var CountriesService $countriesService */
        $countriesService = $this->app->make(CountriesService::class);
        $country = $countriesService->create($data);
        $this->assertNotNull($country, $countriesService->errors()->first());
        $this->assertNull($countriesService->create($data));
        DB::rollBack();
    }

    public function testDelete() : void
    {
        DB::beginTransaction();
        $data = [ 'name' => 'The united states' ];
        /** @var CountriesService $countriesService */
        $countriesService = $this->app->make(CountriesService::class);
        /** @var CountriesRepository $countriesRepository */
        $countriesRepository = $this->app->make(CountriesRepository::class);
        $country = $countriesService->create($data);
        $this->assertTrue($countriesService->delete($country->id));
        $country = $countriesRepository->find($country->id);
        $this->assertNull($country);
        DB::rollBack();
    }

    public function testUpdate() : void
    {
        DB::beginTransaction();
        $data = [ 'name' => 'The united states' ];
        /** @var CountriesService $countriesService */
        $countriesService = $this->app->make(CountriesService::class);
        $country = $countriesService->create($data);

        $new_data = [
            'name' => 'Mexico'
        ];
        $updated = $countriesService->update($country->id, $new_data);
        $this->assertNotNull($updated, $countriesService->errors()->first());
        $this->assertEquals($new_data['name'], $updated->name);
        $this->assertEquals(Str::slug($new_data['name']), $updated->slug);
        DB::rollBack();
    }

    public function testPreventUpdateAndDuplicatedItem() : void
    {
        DB::beginTransaction();
        $data = [ 'name' => 'The united states' ];
        /** @var CountriesService $countriesService */
        $countriesService = $this->app->make(CountriesService::class);
        $country = $countriesService->create($data);

        $data = [ 'name' => 'Mexico' ];
        $mexico = $countriesService->create($data);

        $new_data = [
            'name' => 'The united states'
        ];
        $updated = $countriesService->update($mexico->id, $new_data);
        $this->assertNull($updated);
        DB::rollBack();
    }

    public function testValidationCreateWithRealDataReturnTrue() : void
    {
        /** @var CountriesValidator $countriesValidador */
        $countriesValidador = $this->app->make(CountriesValidator::class);
        $data = ['name' => 'Brazil'];
        $this->assertTrue($countriesValidador->with($data)->passes());
    }

    public function testValidationCreateWithFakeDataReturnFalse() : void
    {
        /** @var CountriesValidator $countriesValidador */
        $countriesValidador = $this->app->make(CountriesValidator::class);
        $data = [];
        $this->assertFalse($countriesValidador->with($data)->passes());
        $data = ['name '=> 'test-name'];
        $this->assertFalse($countriesValidador->with($data)->passes());
    }

}
