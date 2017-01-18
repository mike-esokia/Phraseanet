<?php

namespace Alchemy\Phrasea\Core\Provider;

use Alchemy\Phrasea\BaseApplication as PhraseaApplication;
use Alchemy\Phrasea\Databox\AccessRestrictedDataboxRepository;
use Alchemy\Phrasea\Databox\ArrayCacheDataboxRepository;
use Alchemy\Phrasea\Databox\CachingDataboxRepositoryDecorator;
use Alchemy\Phrasea\Databox\DataboxFactory;
use Alchemy\Phrasea\Databox\DataboxService;
use Alchemy\Phrasea\Databox\DbalDataboxRepository;
use Silex\Application;
use Silex\ServiceProviderInterface;

class DataboxServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['databox.repository'] = $app->share(function (PhraseaApplication $app) {
            $appbox = $app['phraseanet.appbox'];

            $factory = new DataboxFactory($app);
            $repository = new CachingDataboxRepositoryDecorator(
                new DbalDataboxRepository($appbox->get_connection(), $factory),
                $app['cache'],
                $appbox->get_cache_key($appbox::CACHE_LIST_BASES),
                $factory
            );

            $repository = new ArrayCacheDataboxRepository($repository);

            $factory->setDataboxRepository($repository);

            return $repository;
        });

        $app['databox.restricted-repository'] = $app->share(function (PhraseaApplication $app) {
            return new AccessRestrictedDataboxRepository($app['databox.repository'], $app['conf.restrictions']);
        });

        $app['databox.service'] = $app->share(function (PhraseaApplication $app) {
            return new DataboxService(
                $app,
                $app['phraseanet.appbox'],
                $app['dbal.provider'],
                $app['repo.databoxes'],
                $app['conf'],
                $app['root.path']
            );
        });
    }

    public function boot(Application $app)
    {

    }
}
