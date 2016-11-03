<?php

/*
 * This file is part of phrasea-4.0.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phrasea\Setup;

use Alchemy\Phrasea\Core\Connection\ConnectionSettings;

class InstallerConfiguration
{

    /**
     * @var ConnectionSettings
     */
    private $appboxConnectionSettings;

    /**
     * @var ConnectionSettings
     */
    private $databoxConnectionSettings;

    /**
     * @var string
     */
    private $adminEmail;

    /**
     * @var string
     */
    private $adminPassword;

    /**
     * @var string
     */
    private $serverName;

    /**
     * @var string
     */
    private $templateName;

    /**
     * @var string[]
     */
    private $binaries;
}
