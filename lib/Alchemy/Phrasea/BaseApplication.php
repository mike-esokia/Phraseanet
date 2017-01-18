<?php

namespace Alchemy\Phrasea;

use Alchemy\Phrasea\Application\Environment;
use Alchemy\Phrasea\Application\Helper\ApplicationBoxAware;
use Alchemy\Phrasea\Core\MetaProvider\DatabaseMetaProvider;
use Alchemy\Phrasea\Core\Provider\CacheServiceProvider;
use Alchemy\Phrasea\Core\Provider\ConfigurationServiceProvider;
use Alchemy\Phrasea\Core\Provider\ConfigurationTesterServiceProvider;
use Alchemy\Phrasea\Core\Provider\DataboxServiceProvider;
use Alchemy\Phrasea\Core\Provider\ORMServiceProvider;
use Alchemy\Phrasea\Core\Provider\PhraseanetServiceProvider;
use Alchemy\Phrasea\Core\Provider\PhraseaVersionServiceProvider;
use Alchemy\Phrasea\Core\Provider\TranslationServiceProvider;
use Alchemy\Phrasea\Core\Provider\VersionProbeServiceProvider;
use Alchemy\Phrasea\Core\Provider\VersionRepositoryServiceProvider;
use Alchemy\Phrasea\Filesystem\ApplicationPathServiceGenerator;
use Alchemy\Phrasea\Filesystem\FilesystemServiceProvider;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Silex\Application;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\TranslationServiceProvider as SilexTranslationServiceProvider;
use Sorien\Provider\PimpleDumpProvider;

class BaseApplication extends Application
{
    use ApplicationBoxAware;
    use Application\TranslationTrait;

    const ENV_DEV = 'dev';
    const ENV_PROD = 'prod';
    const ENV_TEST = 'test';

    protected static $availableLanguages = [
        'de' => 'Deutsch',
        'en' => 'English',
        'fr' => 'Français',
        'nl' => 'Dutch',
    ];

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @param Environment|string $environment
     */
    public function __construct($environment = null)
    {
        if (is_string($environment)) {
            $environment = new Environment($environment, false);
        }

        $this->environment = $environment ?: new Environment(self::ENV_PROD, false);

        parent::__construct([
            'debug' => $this->environment->isDebug()
        ]);

        $this->setupCharset();
        $this->setupApplicationPaths();

        $this->loadServiceProviders();
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment->getName();
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->environment->isDebug();
    }

    /**
     * Loads Phraseanet plugins
     */
    public function loadPlugins()
    {
        call_user_func(function ($app) {
            if (file_exists($app['plugin.path'] . '/services.php')) {
                require $app['plugin.path'] . '/services.php';
            }
        }, $this);
    }

    protected function bindRoutes()
    {

    }

    protected function loadServiceProviders()
    {
        if ('allowed' == getenv('APP_CONTAINER_DUMP')) {
            $this->register(new PimpleDumpProvider());
        }


        $this->register(new ConfigurationServiceProvider());
        $this->register(new ConfigurationTesterServiceProvider());
        $this->register(new VersionRepositoryServiceProvider());
        $this->register(new VersionProbeServiceProvider());

        $this->register(new FilesystemServiceProvider());
        $this->register(new MonologServiceProvider());

        $this->register(new PhraseaVersionServiceProvider());

        $this->register(new SilexTranslationServiceProvider());
        $this->register(new TranslationServiceProvider());

        $this->register(new DatabaseMetaProvider());
        $this->register(new DataboxServiceProvider());

        $this->register(new CacheServiceProvider());

        $this->register(new PhraseanetServiceProvider());

        $this->setupMonolog();
    }

    private function setupApplicationPaths()
    {
        // app root path
        $this['root.path'] = realpath(__DIR__ . '/../../..');
        // temporary resources default path such as download zip, quarantined documents etc ..
        $this['tmp.path'] = getenv('PHRASEANET_TMP') ?: $this['root.path'].'/tmp';

        // plugin path
        $this['plugin.path'] = $this['root.path'].'/plugins';
        // thumbnails path
        $this['thumbnail.path'] = $this['root.path'].'/www/thumbnails';

        $factory = new ApplicationPathServiceGenerator();

        $this['cache.path'] = $factory->createDefinition(
            ['main', 'storage', 'cache'],
            function (Application $app) {
                return $app['root.path'].'/cache';
            }
        );
        $this['cache.paths'] = function (Application $app) {
            return new \ArrayObject([
                $app['cache.path'],
            ]);
        };

        $this['log.path'] = $factory->createDefinition(
            ['main', 'storage', 'log'],
            function (Application $app) {
                return $app['root.path'].'/logs';
            }
        );

        $this['tmp.download.path'] = $factory->createDefinition(
            ['main', 'storage', 'download'],
            function (Application $app) {
                return $app['tmp.path'].'/download';
            }
        );

        $this['tmp.lazaret.path'] = $factory->createDefinition(
            ['main', 'storage', 'quarantine'],
            function (Application $app) {
                return $app['tmp.path'].'/lazaret';
            }
        );

        $this['tmp.caption.path'] = $factory->createDefinition(
            ['main', 'storage', 'caption'],
            function (Application $app) {
                return $app['tmp.path'].'/caption';
            }
        );
    }

    private function setupCharset()
    {
        $this['charset'] = 'UTF-8';
        mb_internal_encoding($this['charset']);
    }


    private function setupMonolog()
    {
        $this['monolog.name'] = 'phraseanet';
        $this['monolog.handler'] = $this->share(function (BaseApplication $app) {
            return new RotatingFileHandler(
                $app['log.path'] . '/app_error.log',
                10,
                Logger::ERROR,
                $app['monolog.bubble'],
                $app['monolog.permission']
            );
        });
    }
}
