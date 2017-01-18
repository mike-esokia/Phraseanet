<?php

namespace Alchemy\Phrasea\Webhook\Processor;

use Alchemy\Phrasea\BaseApplication;

class FeedEntryProcessorFactory implements ProcessorFactory
{
    /**
     * @var BaseApplication
     */
    private $app;

    public function __construct(BaseApplication $application)
    {
        $this->app = $application;
    }

    public function createProcessor()
    {
        return new FeedEntryProcessor(
            $this->app,
            $this->app['repo.feed-entries'],
            $this->app['phraseanet.user-query']
        );
    }
}
