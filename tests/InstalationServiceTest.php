<?php


namespace Atxy2k\Essence\Tests;


use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\Repositories\InteractionsTypeRepository;
use Atxy2k\Essence\Repositories\RolesRepository;
use Atxy2k\Essence\Repositories\UsersRepository;
use Atxy2k\Essence\Services\InstallationService;
use DB;
use Hash;
use Illuminate\Support\Collection;

class InstalationServiceTest extends TestCase
{
    public function testInstallWorksFine()
    {
        DB::beginTransaction();
        /** @var InstallationService $service */
        $service = $this->app->make(InstallationService::class);
        /** @var InteractionsTypeRepository $interactionsTypeRepository */
        $interactionsTypeRepository = $this->app->make(InteractionsTypeRepository::class);
        /** @var RolesRepository $rolesRepository */
        $rolesRepository = $this->app->make(RolesRepository::class);
        /** @var UsersRepository $usersRepository */
        $usersRepository = $this->app->make(UsersRepository::class);

        $this->assertTrue($service->install());
        /******************************************************
         * Asserts
         ******************************************************/
        $interactions = $interactionsTypeRepository->all();
        $this->assertNotNull($interactions);
        $this->assertInstanceOf(Collection::class, $interactions);
        $this->assertEquals(7, $interactions->count());

        $roles = $rolesRepository->all();
        $this->assertEquals(1, $roles->count());
        $this->assertEquals(1, $roles->first()->users->count());

        $users = $usersRepository->all();
        $this->assertEquals(1, $users->count());
        $user = $users->first();
        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(config('essence.default_user.first_name'), $user->first_name);
        $this->assertEquals(config('essence.default_user.last_name'), $user->last_name);
        $this->assertEquals(config('essence.default_user.email'), $user->email);
        $this->assertTrue(Hash::check(
            config('essence.default_user.password'),
            $user->password
        ));
        $this->assertTrue($user->active);

        DB::rollback();
    }
}