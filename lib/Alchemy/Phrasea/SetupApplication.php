<?php

namespace Alchemy\Phrasea;

use Alchemy\Phrasea\Core\CLIProvider\PluginServiceProvider;

class SetupApplication extends CommandLineApplication
{

    protected function loadServiceProviders()
    {
        parent::loadServiceProviders();

        $this->register(new PluginServiceProvider());
    }
}
