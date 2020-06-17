<?php namespace Atxy2k\Essence\Tests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 19:52
 */
use Atxy2k\Essence\Eloquent\Role;
use Atxy2k\Essence\Eloquent\User;
use Atxy2k\Essence\EssenceServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTest;
use Atxy2k\Essence\Facades\Essence;

class TestCase extends OrchestraTest
{

    protected function getPackageProviders($app)
    {
        return [
            EssenceServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Essence' => Essence::class,
        ];
    }

    protected function getApplicationTimezone($app)
    {
        return 'America/Merida';
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('logging.default', 'stack');
        $app['config']->set('logging.channels.single.path', __DIR__.'/../logs/laravel.log');
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'white_rats',
            'username'  => 'atxy2k',
            'password'  => 'inariama1110',
            'port'      => '3306',
        ]);
        $app['config']->set('essence.admin_role_slug', 'developer');
        $app['config']->set('config.key', 'base64:+74b9J7uq7IWsUt5D8ij+dwA1nV3+I48P1WkN4tleHw=');
    }

    protected function setUp() : void
    {
        parent::setUp();
        $this->artisan('migrate', [ '--database' => 'testbench' ])->run();
    }

}
