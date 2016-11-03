<?php

/*
 * This file is part of phrasea-4.0.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phrasea\Setup\Action;

use Alchemy\Phrasea\Core\Configuration\ConfigurationInterface;
use Alchemy\Phrasea\Setup\InstallerAction;
use Alchemy\Phrasea\Setup\InstallerConfiguration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class RollbackInstallAction implements InstallerAction
{
    /**
     * @var ConfigurationInterface
     */
    private $configurationStore;

    /**
     * @param ConfigurationInterface $configurationStore
     */
    public function __construct(ConfigurationInterface $configurationStore)
    {
        $this->configurationStore = $configurationStore;
    }

    /**
     * @param InstallerConfiguration $configuration
     */
    public function execute(InstallerConfiguration $configuration)
    {
        $structure = simplexml_load_file(__DIR__ . "/../../../../conf.d/bases_structure.xml");

        if (!$structure) {
            throw new \RuntimeException('Unable to load database schema file');
        }

        $this->dropTables($abConn, $structure->appbox->tables->table);

        if (null !== $dbConn) {
            $this->dropTables($dbConn, $structure->databox->tables->table);
        }

        $this->configurationStore->delete();
    }

    private function dropTables(Connection $connection, $tables)
    {
        foreach ($tables as $table) {
            try {
                $statement = $connection->prepare(sprintf('DROP TABLE IF EXISTS `%s`', $table['name']));

                $statement->execute();
                $statement->closeCursor();
            } catch (DBALException $e) {

            }
        }
    }
}
