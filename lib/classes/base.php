<?php

/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2016 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Alchemy\Phrasea\BaseApplication;
use Alchemy\Phrasea\Cache\CacheService;
use Alchemy\Phrasea\Core\Connection\ConnectionSettings;
use Alchemy\Phrasea\Core\Database\DatabaseConnection;
use Alchemy\Phrasea\Core\Database\DatabaseMaintenanceService;
use Alchemy\Phrasea\Core\Database\DatabaseMaintenanceServiceFactory;
use Alchemy\Phrasea\Core\Version as PhraseaVersion;
use Doctrine\DBAL\Connection;

abstract class base implements cache_cacheableInterface
{

    const APPLICATION_BOX = 'APPLICATION_BOX';

    const DATA_BOX = 'DATA_BOX';

    /**
     * @var string
     */
    protected $version;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var SimpleXMLElement
     */
    protected $schema;

    /**
     * @var ConnectionSettings
     */
    protected $connectionSettings;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var PhraseaVersion\VersionRepository
     */
    protected $versionRepository;

    /**
     * @var CacheService
     */
    protected $cacheService;

    /**
     * @var DatabaseMaintenanceServiceFactory
     */
    private $databaseMaintenanceServiceFactory;

    /**
     * @param CacheService $cacheService
     * @param DatabaseConnection $databaseConnection
     * @param DatabaseMaintenanceServiceFactory $databaseMaintenanceServiceFactory
     * @param PhraseaVersion\VersionRepository $versionRepository
     */
    public function __construct(
        CacheService $cacheService,
        DatabaseConnection $databaseConnection,
        DatabaseMaintenanceServiceFactory $databaseMaintenanceServiceFactory,
        PhraseaVersion\VersionRepository $versionRepository
    ) {
        $this->cacheService = $cacheService;
        $this->databaseMaintenanceServiceFactory = $databaseMaintenanceServiceFactory;
        $this->connection = $databaseConnection->getConnection();
        $this->connectionSettings = $databaseConnection->getSettings();
        $this->versionRepository = $versionRepository;
    }

    /**
     * @return string
     */
    abstract public function get_base_type();

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return SimpleXMLElement
     * @throws Exception
     */
    public function get_schema()
    {
        if ($this->schema) {
            return $this->schema;
        }

        $this->load_schema();

        return $this->schema;
    }

    /**
     * @return string
     */
    public function get_dbname()
    {
        return $this->connectionSettings->getDatabaseName();
    }

    /**
     * @return string
     */
    public function get_passwd()
    {
        return $this->connectionSettings->getPassword();
    }

    /**
     * @return string
     */
    public function get_user()
    {
        return $this->connectionSettings->getUser();
    }

    /**
     * @return int
     */
    public function get_port()
    {
        return $this->connectionSettings->getPort();
    }

    /**
     * @return string
     */
    public function get_host()
    {
        return $this->connectionSettings->getHost();
    }

    /**
     * @return Connection
     */
    public function get_connection()
    {
        return $this->connection;
    }

    /**
     * @return \Alchemy\Phrasea\Cache\Cache
     */
    public function get_cache()
    {
        return $this->cacheService->getCache();
    }

    /**
     * @param string|null $option
     * @return mixed
     */
    public function get_data_from_cache($option = null)
    {
        return $this->cacheService->getDataFromCache($this, $option);
    }

    /**
     * @param $value
     * @param string|null $option
     * @param int $duration
     * @return bool
     */
    public function set_data_to_cache($value, $option = null, $duration = 0)
    {
        return $this->cacheService->writeDataToCache($this, $value, $option, $duration);
    }

    /**
     * @param string|null $option
     * @return \Alchemy\Phrasea\Cache\Cache|bool
     */
    public function delete_data_from_cache($option = null)
    {
        return $this->cacheService->deleteDataFromCache($this, $option);
    }

    /**
     * @return PhraseaVersion\VersionRepository
     */
    public function getVersionRepository()
    {
        return $this->versionRepository;
    }

    /**
     * @return string
     */
    public function get_version()
    {
        if (! $this->version) {
            $this->version = $this->versionRepository->getVersion();
        }

        return $this->version;
    }

    /**
     * @param PhraseaVersion $version
     * @return bool
     * @throws Exception
     */
    protected function setVersion(PhraseaVersion $version)
    {
        try {   
            return $this->versionRepository->saveVersion($version);
        } catch (\Exception $e) {
            throw new Exception('Unable to set the database version : ' . $e->getMessage());
        }
    }

    /**
     * @param $applyPatches
     * @return array
     */
    protected function upgradeDb($applyPatches)
    {
        $service = $this->databaseMaintenanceServiceFactory->createService($this->connection);

        return $service->upgradeDatabase($this, $applyPatches);
    }

    /**
     * @return base
     * @throws Exception
     */
    protected function load_schema()
    {
        if ($this->schema) {
            return $this;
        }

        if (false === $structure = simplexml_load_file(__DIR__ . "/../../lib/conf.d/bases_structure.xml")) {
            throw new Exception('Unable to load schema');
        }

        $this->schema = $this->getSchemaNode($structure);

        if (! $this->schema) {
            throw new Exception('Unknown schema type');
        }

        return $this;
    }

    /**
     * @param SimpleXMLElement $structure
     * @return SimpleXMLElement
     */
    abstract protected function getSchemaNode(SimpleXMLElement $structure);

    /**
     * @return base
     */
    public function insert_datas()
    {
        $this->load_schema();

        $service = $this->databaseMaintenanceServiceFactory->createService($this->connection);

        foreach ($this->get_schema()->tables->table as $table) {
            $service->createTable($table);
        }

        $this->setVersion(new PhraseaVersion());

        return $this;
    }

    public function apply_patches($from, $to, $post_process)
    {
        $service = $this->databaseMaintenanceServiceFactory->createService($this->connection);

        return $service->applyPatches($this, $from, $to, $post_process);
    }
}
