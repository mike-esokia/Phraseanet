<?php

namespace Alchemy\Tests\Phrasea;

use Alchemy\Phrasea\CommandLineApplication;

/**
 * @group functional
 * @group legacy
 */
class CLITest extends \PhraseanetTestCase
{
    public function testsEmailWithoutQueue()
    {
        $app = new CommandLineApplication('Phrasea');

        $spool = $this->getMock('Swift_Spool');
        $spool->expects($this->once())
            ->method('flushQueue')
            ->with($this->isInstanceOf('Swift_Transport'));

        $app['swiftmailer.spooltransport'] = $this->getMockBuilder('Swift_SpoolTransport')
            ->disableOriginalConstructor()
            ->getMock();

        $app['swiftmailer.spooltransport']->expects($this->once())
            ->method('getSpool')
            ->will($this->returnValue($spool));

        $app['dispatcher']->dispatch('phraseanet.notification.sent');
    }
}
