<?php

namespace Alchemy\Phrasea\Setup\Version\Probe;

use Alchemy\Phrasea\BaseApplication;
use Alchemy\Zippy\Adapter\VersionProbe\VersionProbeInterface;

class VersionProbesFactory
{
    /**
     * @var BaseApplication
     */
    private $app;

    /***
     * @var ProbeInterface[]
     */
    private $versionProbes = [];

    /**
     * @param BaseApplication $app
     */
    public function __construct(BaseApplication $app)
    {
        $this->app = $app;
    }

    /**
     * @return ProbeInterface[]
     */
    public function createProbes()
    {
        return array_merge([
            new Probe31($this->app),
            new Probe35($this->app),
            new Probe38($this->app),
        ], $this->versionProbes);
    }

    /**
     * @param ProbeInterface $probe
     */
    public function registerVersionProbe(ProbeInterface $probe)
    {
        $this->versionProbes[] = $probe;
    }
}
