<?php

namespace Alchemy\Phrasea\Webhook\Processor;

use Alchemy\Phrasea\BaseApplication;

class OrderNotificationProcessorFactory implements ProcessorFactory
{
    /**
     * @var BaseApplication
     */
    private $application;

    public function __construct(BaseApplication $application)
    {
        $this->application = $application;
    }

    /**
     * @return ProcessorInterface
     */
    public function createProcessor()
    {
        return new OrderNotificationProcessor(
            $this->application['repo.orders'],
            $this->application['repo.users']
        );
    }
}
