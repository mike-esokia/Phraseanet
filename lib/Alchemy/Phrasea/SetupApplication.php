<?php

namespace Alchemy\Phrasea;

use Alchemy\Phrasea\Command\Plugin\AddPlugin;
use Alchemy\Phrasea\Command\Plugin\DisablePlugin;
use Alchemy\Phrasea\Command\Plugin\EnablePlugin;
use Alchemy\Phrasea\Command\Plugin\ListPlugin;
use Alchemy\Phrasea\Command\Plugin\RemovePlugin;
use Alchemy\Phrasea\Command\Setup\CheckEnvironment;
use Alchemy\Phrasea\Command\Setup\ConfigurationEditor;
use Alchemy\Phrasea\Command\Setup\CrossDomainGenerator;
use Alchemy\Phrasea\Command\Setup\Install;
use Alchemy\Phrasea\Command\Setup\PluginsReset;
use Alchemy\Phrasea\Command\UpgradeDBDatas;
use Alchemy\Phrasea\Core\CLIProvider\PluginServiceProvider;
use Alchemy\Phrasea\Setup\ConfigurationTester;

class SetupApplication extends CommandLineApplication
{

    public function __construct($name, $version = null, $environment = self::ENV_PROD)
    {
        parent::__construct($name, $version, $environment);

        $this['phraseanet.setup_mode'] = true;
    }

    protected function loadServiceProviders()
    {
        parent::loadServiceProviders();

        $this->register(new PluginServiceProvider());
    }

    protected function getCommands()
    {
        $commands = parent::getCommands();

        /** @var ConfigurationTester $configurationTester */
        $configurationTester = $this['phraseanet.configuration-tester'];

        if($configurationTester->isMigrable() || $configurationTester->isUpgradable() || $configurationTester->isInstalled()) {
            $commands[] = new \module_console_systemUpgrade('system:upgrade');
        }

        if ($configurationTester->isInstalled()) {
            $commands[] = new UpgradeDBDatas('system:upgrade-datas');
            $commands[] = new ConfigurationEditor('system:config');
        }

        $commands[] = new AddPlugin();
        $commands[] = new ListPlugin();
        $commands[] = new RemovePlugin();
        $commands[] = new PluginsReset();
        $commands[] = new EnablePlugin();
        $commands[] = new DisablePlugin();
        $commands[] = new CheckEnvironment('check:system');
        $commands[] = new CheckEnvironment('system:check');
        $commands[] = new Install('system:install');
        $commands[] = new CrossDomainGenerator();

        return $commands;
    }
}
