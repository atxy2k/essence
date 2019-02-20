<?php namespace Atxy2k\Essence\Tests;
/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 12/2/2019
 * Time: 19:52
 */

use Atxy2k\Essence\Eloquent\Role;
use Atxy2k\Essence\Eloquent\User;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
use Cartalyst\Sentinel\Reminders\EloquentReminder;
use Cartalyst\Sentinel\Reminders\IlluminateReminderRepository;
use Orchestra\Testbench\TestCase as OrchestraTest;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Atxy2k\Essence\Facades\Essence;

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
            'Activation' => Activation::class,
            'Reminder'   => Reminder::class,
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
        $app['config']->set('sentinel.users.model', User::class);
        $app['config']->set('sentinel.roles.model', Role::class);
        $app['config']->set('config.key', 'base64:+74b9J7uq7IWsUt5D8ij+dwA1nV3+I48P1WkN4tleHw=');
    }

}
