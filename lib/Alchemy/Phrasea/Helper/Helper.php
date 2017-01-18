<?php

/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2016 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phrasea\Helper;

use Alchemy\Phrasea\BaseApplication;
use Symfony\Component\HttpFoundation\Request;

class Helper
{
    /** @var BaseApplication */
    protected $app;

    /** @var Request */
    protected $request;

    /**
     *
     * @param BaseApplication $app
     * @param Request     $Request
     *
     * @return Helper
     */
    public function __construct(BaseApplication $app, Request $Request)
    {
        $this->app = $app;
        $this->request = $Request;

        return $this;
    }

    /** @return Request */
    public function getRequest()
    {
        return $this->request;
    }
}
