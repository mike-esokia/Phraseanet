<?php

namespace Alchemy\Phrasea\Application;

use Alchemy\Phrasea\BaseApplication;

class Environment
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $debug = false;

    public function __construct($name, $debug)
    {
        $this->name = (string) $name;
        $this->debug = ((bool) $debug) || $name === BaseApplication::ENV_DEV;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }
}
