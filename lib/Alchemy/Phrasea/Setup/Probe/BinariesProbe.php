<?php

/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2016 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phrasea\Setup\Probe;

use Alchemy\Phrasea\BaseApplication;
use Alchemy\Phrasea\Setup\Requirements\BinariesRequirements;

class BinariesProbe extends BinariesRequirements implements ProbeInterface
{
    public function __construct(array $binaries)
    {
        parent::__construct(array_filter($binaries));
    }

    /**
     * {@inheritdoc}
     *
     * @return BinariesProbe
     */
    public static function create(BaseApplication $app)
    {
        return new static($app['conf']->get(['main', 'binaries']));
    }
}
