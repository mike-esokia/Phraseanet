<?php

namespace Alchemy\Tests\Phrasea\Setup\Version\Probe;

use Alchemy\Phrasea\BaseApplication;
use Alchemy\Tests\Phrasea\Setup\AbstractSetupTester;
use Alchemy\Phrasea\Setup\Version\Probe\Probe38;

/**
 * @group functional
 * @group legacy
 */
class Probe38Test extends AbstractSetupTester
{
    public function testNoMigration()
    {
        $probe = $this->getProbe();
        $this->assertFalse($probe->isMigrable());
    }

    public function testMigration()
    {
        $app = new BaseApplication(BaseApplication::ENV_TEST);
        $app['root.path'] = __DIR__ . '/fixtures-3807';
        $probe = new Probe38($app);
        $this->assertTrue($probe->isMigrable());
        $this->assertInstanceOf('Alchemy\Phrasea\Setup\Version\Migration\Migration38', $probe->getMigration());
    }

    private function getProbe()
    {
        return new Probe38(new BaseApplication(BaseApplication::ENV_TEST));
    }
}
