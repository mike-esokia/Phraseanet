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

return (new Application\WebApplicationLoader())->buildApplication(
    isset($environment) ? $environment : BaseApplication::ENV_PROD,
    isset($forceDebug) ? $forceDebug : false
);
