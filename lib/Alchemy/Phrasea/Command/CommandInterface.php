<?php

/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2016 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phrasea\Command;

use Alchemy\Phrasea\BaseApplication;

interface CommandInterface
{
    /**
     * Inject the container in the command.
     *
     * @param BaseApplication $container
     */
    public function setContainer(BaseApplication $container);

    /**
     * Factory for the command.
     */
    public static function create();
}
