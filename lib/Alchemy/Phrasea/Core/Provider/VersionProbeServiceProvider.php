<?php

namespace Alchemy\Phrasea\Core\Provider;

use Alchemy\Phrasea\BaseApplication;
use Alchemy\Phrasea\Setup\Version\Probe\VersionProbesFactory;
use Silex\Application;
use Silex\ServiceProviderInterface;

class VersionProbeServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $app['version-probe.factory'] = $app->share(function (BaseApplication $app) {
            return new VersionProbesFactory($app);
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }
}
