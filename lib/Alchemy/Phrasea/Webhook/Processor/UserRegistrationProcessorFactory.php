<?php

namespace Alchemy\Phrasea\Webhook\Processor;

use Alchemy\Phrasea\BaseApplication;

class UserRegistrationProcessorFactory implements ProcessorFactory
{
    /**
     * @var BaseApplication
     */
    private $app;

    /**
     * @param BaseApplication $application
     */
    public function __construct(BaseApplication $application)
    {
        $this->app = $application;
    }

    /**
     * @return UserRegistrationProcessor
     */
    public function createProcessor()
    {
        return new UserRegistrationProcessor($this->app['repo.users']);
    }
}
