<?php

/*
 * This file is part of phrasea-4.0.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phrasea\Core\Connection;

use Alchemy\Phrasea\Application;

class ConnectionProvider
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param array|ConnectionSettings $info
     * @return \Doctrine\DBAL\Connection
     */
    public function __invoke($info)
    {
        if ($info instanceof ConnectionSettings) {
            $info = $info->toArray();
        }

        if (! is_array($info)) {
            throw new \InvalidArgumentException('ConnectionSettings or parameter array expected.');
        }

        $info = $this->app['db.info']($info);
        /** @var ConnectionPoolManager $manager */
        $manager = $this->app['connection.pool.manager'];

        return $manager->get($info);
    }
}
