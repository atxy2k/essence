<?php namespace Atxy2k\Essence\Tests;
use Atxy2k\Essence\Commands\MexicanTowns\MexicanTownsCommand;

/**
 * Created by PhpStorm.
 * User: atxy2k
 * Date: 14/03/2019
 * Time: 13:44
 */
class MexicanTownsCommandTest extends TestCase
{
    public function testCreateInstanceDone() : void
    {
        /** @var MexicanTownsCommand $mexicanTownsCommand */
        $mexicanTownsCommand = $this->app->make(MexicanTownsCommand::class);
        $this->assertNotNull($mexicanTownsCommand);
    }
}
