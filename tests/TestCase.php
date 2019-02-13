<?php namespace Atxy2k\Essence\Tests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 19:52
 */
use Atxy2k\Essence\EssenceServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTest;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

class TestCase extends OrchestraTest
{

//    protected function getPackageProviders($app)
//    {
//        return [EssenceServiceProvider::class];
//    }
//
    protected function getPackageAliases($app)
    {
        return [
            'Essence' => Essence::class,
            'Sentinel' => Sentinel::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'white_rats',
            'username'  => 'homestead',
            'password'  => 'secret',
            'port'      => '33060',
        ]);
        $app['config']->set('essence.admin_role_slug', 'developer');
    }

}
