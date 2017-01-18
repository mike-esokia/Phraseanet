<?php
/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2016 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Alchemy\Phrasea\Databox;

use Alchemy\Phrasea\BaseApplication;
use Alchemy\Phrasea\Cache\CacheService;
use Alchemy\Phrasea\Core\Connection\ConnectionSettings;
use Alchemy\Phrasea\Core\Database\DatabaseConnection;
use Alchemy\Phrasea\Core\Database\DatabaseMaintenanceServiceFactory;
use Alchemy\Phrasea\Core\Version\DataboxVersionRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DataboxFactory
{
    /**
     * @var BaseApplication
     */
    private $app;

    /**
     * @var CacheService
     */
    private $cacheService;

    /**
     * @var DataboxRepository
     */
    private $databoxRepository;

    /**
     * @var DatabaseMaintenanceServiceFactory
     */
    private $databaseMaintenanceServiceFactory;

    /**
     * @var DataboxVersionRepository
     */
    private $databoxVersionRepository;

    /**
     * @param BaseApplication $app
     * @param CacheService $cacheService
     * @param DatabaseMaintenanceServiceFactory $databaseMaintenanceServiceFactory
     * @param DataboxRepository $databoxRepository
     * @param DataboxVersionRepository $databoxVersionRepository
     */
    public function __construct(
        BaseApplication $app,
        CacheService $cacheService,
        DatabaseMaintenanceServiceFactory $databaseMaintenanceServiceFactory,
        DataboxRepository $databoxRepository,
        DataboxVersionRepository $databoxVersionRepository
    ) {
        $this->app = $app;
        $this->cacheService = $cacheService;
        $this->databaseMaintenanceServiceFactory = $databaseMaintenanceServiceFactory;
        $this->databoxRepository = $databoxRepository;
        $this->databoxVersionRepository = $databoxVersionRepository;
    }

    /**
     * @param DataboxRepository $databoxRepository
     */
    public function setDataboxRepository(DataboxRepository $databoxRepository)
    {
        $this->databoxRepository = $databoxRepository;
    }

    /**
     * @param int $id
     * @param array $raw
     * @return \databox when Databox could not be retrieved from Persistence layer
     */
    public function create($id, array $raw)
    {
        $connectionConfigs = \phrasea::sbas_params($this->app);

        if (! isset($connectionConfigs[$id])) {
            throw new NotFoundHttpException(sprintf('databox %d not found', $id));
        }

        $connectionConfig = $connectionConfigs[$id];
        $connection = $app['db.provider']($connectionConfig);
        $connectionSettings = new ConnectionSettings(
            $connectionConfig['host'],
            $connectionConfig['port'],
            $connectionConfig['dbname'],
            $connectionConfig['user'],
            $connectionConfig['password']
        );

        $databaseConnection = new DatabaseConnection($connectionSettings, $connection);

        return new \databox(
            $this->app,
            $id,
            $this->cacheService,
            $databaseConnection,
            $this->databaseMaintenanceServiceFactory,
            $this->databoxRepository,
            $this->databoxVersionRepository,
            $raw
        );
    }

    /**
     * @param array $rows
     * @throws NotFoundHttpException when Databox could not be retrieved from Persistence layer
     * @return \databox[]
     */
    public function createMany(array $rows)
    {
        $databoxes = [];

        foreach ($rows as $id => $raw) {
            $databoxes[$id] = $this->create($id, $raw);
        }

        return $databoxes;
    }
}
