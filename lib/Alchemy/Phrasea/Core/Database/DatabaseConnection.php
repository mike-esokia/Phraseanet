<?php

namespace Alchemy\Phrasea\Core\Database;

use Alchemy\Phrasea\Core\Connection\ConnectionSettings;
use Doctrine\DBAL\Connection;

class DatabaseConnection
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ConnectionSettings
     */
    private $settings;

    /**
     * @param ConnectionSettings $settings
     * @param Connection $connection
     */
    public function __construct(ConnectionSettings $settings, Connection $connection)
    {
        $this->settings = $settings;
        $this->connection = $connection;
    }

    /**
     * @return ConnectionSettings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
