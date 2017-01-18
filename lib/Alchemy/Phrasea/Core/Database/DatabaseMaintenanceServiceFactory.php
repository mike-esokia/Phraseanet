<?php

namespace Alchemy\Phrasea\Core\Database;

use Alchemy\Phrasea\BaseApplication;
use Doctrine\DBAL\Connection;

class DatabaseMaintenanceServiceFactory
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
     * @param Connection $connection
     * @return DatabaseMaintenanceService
     */
    public function createService(Connection $connection)
    {
        return new DatabaseMaintenanceService($this->app, $connection);
    }
}
